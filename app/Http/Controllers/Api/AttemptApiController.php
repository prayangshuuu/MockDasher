<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiSpeakingEvaluation;
use App\Models\AiWritingEvaluation;
use App\Models\ListeningAttempt;
use App\Models\ReadingAttempt;
use App\Models\TestAttempt;
use App\Models\TestSet;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttemptApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;
        $cursor = $request->input('cursor');

        $result = Cache::tags(["user-{$userId}"])->remember(
            "attempts:{$userId}:c" . ($cursor ?? 'start'),
            60,
            function () use ($userId, $cursor) {
                $paginator = TestAttempt::with(['test'])
                    ->where('user_id', $userId)
                    ->orderByDesc('id')
                    ->cursorPaginate(15, ['*'], 'cursor', $cursor);

                return [
                    'data' => collect($paginator->items())->map(fn ($a) => $this->formatAttempt($a)),
                    'meta' => [
                        'next_cursor' => $paginator->nextCursor()?->encode(),
                        'prev_cursor' => $paginator->previousCursor()?->encode(),
                        'per_page'    => $paginator->perPage(),
                    ],
                ];
            }
        );

        return response()->json($result);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $attempt = TestAttempt::with([
            'test', 'writingAnswers.writingTask',
            'speakingAnswers.question',
            'aiWritingEvaluation', 'aiSpeakingEvaluation',
            'readingAttempt', 'listeningAttempt',
        ])->where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json(['data' => $this->formatAttemptDetail($attempt)]);
    }

    public function start(Request $request): JsonResponse
    {
        $request->validate([
            'test_id'     => 'required_without:test_set_id|integer|exists:tests,id',
            'test_set_id' => 'required_without:test_id|integer|exists:test_sets,id',
        ]);

        if ($request->filled('test_id')) {
            $testSet = TestSet::where('test_id', $request->test_id)->firstOrFail();
        } else {
            $testSet = TestSet::findOrFail($request->test_set_id);
        }

        $existing = TestAttempt::where('user_id', $request->user()->id)
            ->where('test_set_id', $testSet->id)
            ->whereNull('completed_at')
            ->latest()
            ->first();

        if ($existing) {
            return response()->json(['data' => $this->formatAttempt($existing->load('test')), 'resumed' => true]);
        }

        $attempt = TestAttempt::create([
            'user_id'     => $request->user()->id,
            'test_set_id' => $testSet->id,
            'status'      => 'in_progress',
            'started_at'  => now(),
        ]);

        // New attempt — flush cached list so it appears immediately
        Cache::tags(["user-{$request->user()->id}"])->flush();

        return response()->json(['data' => $this->formatAttempt($attempt->load('test')), 'resumed' => false], 201);
    }

    public function finish(Request $request, int $id): JsonResponse
    {
        $attempt = TestAttempt::where('user_id', $request->user()->id)->findOrFail($id);

        if ($attempt->completed_at) {
            return response()->json(['error' => 'already_completed', 'message' => 'This exam is already finished.'], 409);
        }

        DB::transaction(function () use ($attempt) {
            $this->zeroFillIncompleteModules($attempt);
            $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        });

        // Flush list cache — completed attempt must appear in history immediately
        Cache::tags(["user-{$attempt->user_id}"])->flush();

        $attempt->load(['readingAttempt', 'listeningAttempt', 'aiWritingEvaluation', 'aiSpeakingEvaluation']);

        return response()->json([
            'success'       => true,
            'attempt_id'    => $attempt->id,
            'overall_band'  => $attempt->overall_band,
            'reading_band'  => $attempt->reading_band,
            'listening_band' => $attempt->listening_band,
            'writing_band'  => $attempt->writing_band,
            'speaking_band' => $attempt->speaking_band,
            'completed_at'  => $attempt->completed_at->toIso8601String(),
        ]);
    }

    public function recordViolation(Request $request, int $id): JsonResponse
    {
        $attempt = TestAttempt::where('user_id', $request->user()->id)->findOrFail($id);

        if ($attempt->completed_at) {
            return response()->json(['status' => 'already_completed'], 200);
        }

        $maxViolations = 3;

        DB::transaction(function () use ($attempt) {
            $fresh = TestAttempt::lockForUpdate()->find($attempt->id);
            if (! $fresh->completed_at) {
                $fresh->increment('proctoring_violations');
            }
        });

        $attempt->refresh();
        $violations = (int) $attempt->proctoring_violations;

        if ($violations >= $maxViolations && ! $attempt->completed_at) {
            DB::transaction(function () use ($attempt) {
                $fresh = TestAttempt::lockForUpdate()->find($attempt->id);
                if ($fresh->completed_at) {
                    return;
                }
                $this->zeroFillIncompleteModules($fresh);
                $fresh->update(['status' => 'completed', 'completed_at' => now()]);
            });

            return response()->json([
                'status'     => 'terminated',
                'violations' => $violations,
                'message'    => 'Exam terminated due to proctoring violations.',
            ]);
        }

        return response()->json([
            'status'     => 'warned',
            'violations' => $violations,
            'remaining'  => max(0, $maxViolations - $violations),
        ]);
    }

    public function evaluationStatus(Request $request, int $id): JsonResponse
    {
        $attempt = TestAttempt::with(['aiWritingEvaluation', 'aiSpeakingEvaluation'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return response()->json([
            'attempt_id' => $attempt->id,
            'writing'    => [
                'status'         => $attempt->aiWritingEvaluation?->evaluation_status ?? 'not_started',
                'band_score'     => $attempt->aiWritingEvaluation?->band_score,
                'failure_reason' => $attempt->aiWritingEvaluation?->failure_reason,
            ],
            'speaking'   => [
                'status'         => $attempt->aiSpeakingEvaluation?->evaluation_status ?? 'not_started',
                'band_score'     => $attempt->aiSpeakingEvaluation?->band_score,
                'failure_reason' => $attempt->aiSpeakingEvaluation?->failure_reason,
            ],
        ]);
    }

    public function evaluationStream(Request $request, int $id): StreamedResponse
    {
        $attempt = TestAttempt::where('user_id', $request->user()->id)->findOrFail($id);

        return response()->stream(function () use ($attempt): void {
            $deadline = time() + 300; // 5-minute hard cap

            while (time() < $deadline) {
                if (connection_aborted()) {
                    break;
                }

                $attempt->load(['aiWritingEvaluation', 'aiSpeakingEvaluation']);

                $writing  = $attempt->aiWritingEvaluation;
                $speaking = $attempt->aiSpeakingEvaluation;

                echo 'data: '.json_encode([
                    'writing'  => [
                        'status'     => $writing?->evaluation_status ?? 'not_started',
                        'band_score' => $writing?->band_score,
                    ],
                    'speaking' => [
                        'status'     => $speaking?->evaluation_status ?? 'not_started',
                        'band_score' => $speaking?->band_score,
                    ],
                ])."\n\n";

                if (ob_get_level() > 0) {
                    ob_flush();
                }
                flush();

                $writingDone  = $writing === null
                    || in_array($writing->evaluation_status, ['completed', 'failed'], true);
                $speakingDone = $speaking === null
                    || in_array($speaking->evaluation_status, ['completed', 'failed'], true);

                if ($writingDone && $speakingDone) {
                    echo "event: done\ndata: {}\n\n";
                    if (ob_get_level() > 0) {
                        ob_flush();
                    }
                    flush();
                    break;
                }

                sleep(3);
            }
        }, 200, [
            'Content-Type'      => 'text/event-stream',
            'Cache-Control'     => 'no-cache, no-store',
            'X-Accel-Buffering' => 'no',
            'Connection'        => 'keep-alive',
        ]);
    }

    private function zeroFillIncompleteModules(TestAttempt $attempt): void
    {
        $attempt->loadMissing(['listeningAttempt', 'readingAttempt', 'aiWritingEvaluation', 'aiSpeakingEvaluation']);

        $listening = $attempt->listeningAttempt;
        if (! $listening) {
            ListeningAttempt::create([
                'test_attempt_id' => $attempt->id,
                'user_id'         => $attempt->user_id,
                'test_set_id'     => $attempt->test_set_id,
                'status'          => 'completed',
                'total_correct'   => 0,
                'band_score'      => 0.0,
                'started_at'      => now(),
                'completed_at'    => now(),
            ]);
        } elseif ($listening->status !== 'completed') {
            $listening->update(['status' => 'completed', 'total_correct' => 0, 'band_score' => 0.0, 'completed_at' => now()]);
        }

        $reading = $attempt->readingAttempt;
        if (! $reading) {
            ReadingAttempt::create([
                'test_attempt_id' => $attempt->id,
                'user_id'         => $attempt->user_id,
                'test_set_id'     => $attempt->test_set_id,
                'status'          => 'completed',
                'total_correct'   => 0,
                'band_score'      => 0.0,
                'started_at'      => now(),
                'completed_at'    => now(),
            ]);
        } elseif ($reading->status !== 'completed') {
            $reading->update(['status' => 'completed', 'total_correct' => 0, 'band_score' => 0.0, 'completed_at' => now()]);
        }

        if (! $attempt->aiWritingEvaluation) {
            AiWritingEvaluation::create([
                'test_attempt_id'   => $attempt->id,
                'user_id'           => $attempt->user_id,
                'band_score'        => 0.0,
                'evaluation_status' => 'completed',
            ]);
        } elseif ($attempt->aiWritingEvaluation->band_score === null) {
            $attempt->aiWritingEvaluation->update(['band_score' => 0.0, 'evaluation_status' => 'completed', 'failure_reason' => null]);
        }

        if (! $attempt->aiSpeakingEvaluation) {
            AiSpeakingEvaluation::create([
                'test_attempt_id'   => $attempt->id,
                'user_id'           => $attempt->user_id,
                'band_score'        => 0.0,
                'evaluation_status' => 'completed',
            ]);
        } elseif ($attempt->aiSpeakingEvaluation->band_score === null) {
            $attempt->aiSpeakingEvaluation->update(['band_score' => 0.0, 'evaluation_status' => 'completed', 'failure_reason' => null]);
        }
    }

    private function formatAttempt(TestAttempt $a): array
    {
        return [
            'id'           => $a->id,
            'test_title'   => optional($a->test)->title ?? 'IELTS Mock',
            'status'       => $a->status,
            'overall_band' => $a->overall_band,
            'reading_band' => $a->reading_band,
            'listening_band' => $a->listening_band,
            'writing_band' => $a->writing_band,
            'speaking_band' => $a->speaking_band,
            'started_at'   => $a->started_at?->toIso8601String(),
            'completed_at' => $a->completed_at?->toIso8601String(),
            'time_spent'   => $a->time_spent,
        ];
    }

    private function formatAttemptDetail(TestAttempt $a): array
    {
        $base = $this->formatAttempt($a);

        $base['writing_answers'] = $a->writingAnswers->map(fn ($w) => [
            'task'       => optional($w->writingTask)->task_title,
            'word_count' => str_word_count($w->answer_text ?? ''),
            'band_score' => $w->band_score,
            'answer'     => $w->answer_text,
        ]);

        $base['speaking_answers'] = $a->speakingAnswers->map(fn ($s) => [
            'part'       => optional($s->question)->part,
            'question'   => optional($s->question)->question_text,
            'transcript' => $s->transcript_text,
            'band_score' => $s->band_score,
        ]);

        $base['ai_writing'] = $a->aiWritingEvaluation ? [
            'status'     => $a->aiWritingEvaluation->evaluation_status,
            'band_score' => $a->aiWritingEvaluation->band_score,
        ] : null;

        $base['ai_speaking'] = $a->aiSpeakingEvaluation ? [
            'status'     => $a->aiSpeakingEvaluation->evaluation_status,
            'band_score' => $a->aiSpeakingEvaluation->band_score,
        ] : null;

        return $base;
    }
}
