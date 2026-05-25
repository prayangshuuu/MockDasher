<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ListeningAnswer;
use App\Models\ListeningAttempt;
use App\Models\TestAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListeningApiController extends Controller
{
    private const LISTENING_LIMIT_SECONDS = 1800;

    private const TRANSFER_LIMIT_SECONDS = 600;

    private const GRACE_SECONDS = 60;

    public function show(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        // Find or create the ListeningAttempt for this TestAttempt
        $la = $attempt->listeningAttempt;

        if (! $la) {
            $testSet = $attempt->testSet;
            $la = ListeningAttempt::create([
                'test_attempt_id' => $attempt->id,
                'user_id'         => $attempt->user_id,
                'test_set_id'     => $attempt->test_set_id,
                'status'          => 'in_progress',
                'current_section' => 1,
                'started_at'      => now(),
            ]);
            $attempt->update(['status' => 'listening']);
        }

        if ($la->status === 'completed') {
            return response()->json(['error' => 'already_completed', 'message' => 'Listening test already completed.'], 409);
        }

        if (! $la->started_at) {
            $la->update(['started_at' => now(), 'status' => 'in_progress']);
            $la->refresh();
        }

        $remainingSeconds         = null;
        $transferRemainingSeconds = null;

        if ($la->status === 'transfer' && $la->transfer_started_at) {
            $elapsed                  = now()->diffInSeconds($la->transfer_started_at);
            $transferRemainingSeconds = (int) max(0, self::TRANSFER_LIMIT_SECONDS - $elapsed);

            if ($transferRemainingSeconds <= 0) {
                if ($la->status !== 'completed') {
                    $la->update(['status' => 'completed', 'completed_at' => now()]);
                    $result = $la->evaluate();

                    return response()->json([
                        'error'      => 'time_expired',
                        'message'    => 'Transfer time expired. Listening test submitted automatically.',
                        'band_score' => $result['band_score'] ?? null,
                        'score'      => $result['total_correct'] ?? null,
                    ], 422);
                }
            }
        } elseif ($la->started_at) {
            $elapsed          = now()->diffInSeconds($la->started_at);
            $remainingSeconds = (int) max(0, self::LISTENING_LIMIT_SECONDS - $elapsed);

            if ($remainingSeconds <= 0) {
                if ($la->status !== 'completed') {
                    $la->update(['status' => 'completed', 'completed_at' => now()]);
                    $result = $la->evaluate();

                    return response()->json([
                        'error'      => 'time_expired',
                        'message'    => 'Listening time expired. Test submitted automatically.',
                        'band_score' => $result['band_score'] ?? null,
                        'score'      => $result['total_correct'] ?? null,
                    ], 422);
                }
            }
        }

        $sections      = $la->testSet->listeningSections()
            ->with(['questions.options'])
            ->orderBy('section_number')
            ->get();

        $savedAnswers   = $la->answers->pluck('answer_text', 'question_id')->toArray();
        $flaggedAnswers = $la->answers->filter(fn ($a) => $a->is_flagged)->pluck('is_flagged', 'question_id')->toArray();

        $sectionData = $sections->map(function ($section) use ($savedAnswers, $flaggedAnswers) {
            return [
                'id'             => $section->id,
                'section_number' => $section->section_number,
                'title'          => $section->title ?? null,
                'audio_url'      => $section->audio_path
                    ? \Illuminate\Support\Facades\Storage::url($section->audio_path)
                    : null,
                'questions' => $section->questions->map(function ($q) use ($savedAnswers, $flaggedAnswers) {
                    return [
                        'id'            => $q->id,
                        'question_type' => $q->question_type ?? null,
                        'question_text' => $q->question_text ?? null,
                        'options'       => $q->options->map(fn ($o) => [
                            'id'    => $o->id,
                            'label' => $o->label ?? null,
                            'text'  => $o->option_text ?? null,
                        ])->values(),
                        'saved_answer' => $savedAnswers[$q->id] ?? null,
                        'is_flagged'   => ! empty($flaggedAnswers[$q->id]),
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'attempt_id'                 => $attempt->id,
            'listening_attempt_id'       => $la->id,
            'status'                     => $la->status,
            'current_section'            => $la->current_section,
            'remaining_seconds'          => $remainingSeconds,
            'transfer_remaining_seconds' => $transferRemainingSeconds,
            'sections'                   => $sectionData,
        ]);
    }

    public function autosave(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $la = $attempt->listeningAttempt;
        if (! $la) {
            return response()->json(['error' => 'not_started', 'message' => 'Listening module not started.'], 404);
        }

        if ($la->status === 'completed') {
            return response()->json(['error' => 'Already completed'], 403);
        }

        if ($this->isTimeExpired($la)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $request->validate([
            'answers'   => 'array|max:50',
            'answers.*' => 'nullable|string|max:1000',
            'flagged'   => 'array|max:50',
            'flagged.*' => 'nullable|boolean',
        ]);

        $this->upsertAnswers(
            $la,
            $request->user()->id,
            $this->validQuestionIds($la),
            $request->input('answers', []),
            $request->input('flagged', [])
        );

        return response()->json(['success' => true, 'saved_at' => now()->toTimeString()]);
    }

    public function completeSection(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $la = $attempt->listeningAttempt;
        if (! $la) {
            return response()->json(['error' => 'not_started'], 404);
        }

        $request->validate(['section' => 'required|integer|min:1|max:4']);
        $section = (int) $request->input('section');

        if ($section >= $la->current_section) {
            $nextSection = $section + 1;

            if ($nextSection > 4) {
                $la->update(['status' => 'transfer', 'transfer_started_at' => now()]);

                return response()->json([
                    'status'           => 'transfer',
                    'transfer_seconds' => self::TRANSFER_LIMIT_SECONDS,
                ]);
            }

            $la->update(['current_section' => $nextSection]);

            return response()->json(['status' => 'next', 'next_section' => $nextSection]);
        }

        return response()->json(['status' => 'ok']);
    }

    public function submit(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $la = $attempt->listeningAttempt;
        if (! $la) {
            return response()->json(['error' => 'not_started'], 404);
        }

        if ($la->status === 'completed') {
            return response()->json(['error' => 'Already completed'], 403);
        }

        $request->validate([
            'answers'   => 'array|max:50',
            'answers.*' => 'nullable|string|max:1000',
            'flagged'   => 'array|max:50',
            'flagged.*' => 'nullable|boolean',
        ]);

        $this->upsertAnswers(
            $la,
            $request->user()->id,
            $this->validQuestionIds($la),
            $request->input('answers', []),
            $request->input('flagged', [])
        );

        $la->update(['status' => 'completed', 'completed_at' => now()]);
        $result = $la->evaluate();

        return response()->json([
            'success'    => true,
            'band_score' => $result['band_score'] ?? null,
            'score'      => $result['total_correct'] ?? null,
        ]);
    }

    public function result(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $la = $attempt->listeningAttempt;
        if (! $la) {
            return response()->json(['error' => 'not_started'], 404);
        }

        if ($la->status !== 'completed') {
            return response()->json(['error' => 'not_completed', 'message' => 'Listening test not yet submitted.'], 409);
        }

        $sections       = $la->testSet->listeningSections()
            ->with(['questions.options'])
            ->orderBy('section_number')
            ->get();

        $answers        = $la->answers()->with('question')->get()->keyBy('question_id');
        $totalQuestions = $sections->flatMap(fn ($s) => $s->questions)->count();

        $sectionData = $sections->map(function ($section) use ($answers) {
            return [
                'id'             => $section->id,
                'section_number' => $section->section_number,
                'questions'      => $section->questions->map(function ($q) use ($answers) {
                    $userAnswer = $answers->get($q->id);

                    return [
                        'id'            => $q->id,
                        'question_text' => $q->question_text ?? null,
                        'user_answer'   => $userAnswer?->answer_text,
                        'is_correct'    => $userAnswer ? $this->isCorrect($userAnswer->answer_text, $q->correct_answer) : false,
                    ];
                })->values(),
            ];
        });

        return response()->json([
            'attempt_id'      => $attempt->id,
            'band_score'      => $la->band_score,
            'score'           => $la->total_correct,
            'total_questions' => $totalQuestions,
            'sections'        => $sectionData,
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function isTimeExpired(ListeningAttempt $la): bool
    {
        if ($la->status === 'transfer' && $la->transfer_started_at) {
            return now()->diffInSeconds($la->transfer_started_at)
                > (self::TRANSFER_LIMIT_SECONDS + self::GRACE_SECONDS);
        }

        if ($la->started_at) {
            return now()->diffInSeconds($la->started_at)
                > (self::LISTENING_LIMIT_SECONDS + self::GRACE_SECONDS);
        }

        return false;
    }

    /** @return array<int> */
    private function validQuestionIds(ListeningAttempt $la): array
    {
        return $la->testSet
            ->listeningSections()
            ->with('questions:id,questionable_id,questionable_type')
            ->get()
            ->flatMap(fn ($s) => $s->questions->pluck('id'))
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    private function upsertAnswers(ListeningAttempt $la, int $userId, array $validIds, array $answers, array $flagged): void
    {
        foreach ($answers as $questionId => $answerText) {
            if (! in_array((int) $questionId, $validIds, true)) {
                continue;
            }
            ListeningAnswer::updateOrCreate(
                ['user_id' => $userId, 'test_attempt_id' => $la->id, 'question_id' => (int) $questionId],
                ['answer_text' => $answerText, 'is_flagged' => ! empty($flagged[$questionId])]
            );
        }

        foreach ($flagged as $questionId => $isFlagged) {
            if (! in_array((int) $questionId, $validIds, true) || array_key_exists($questionId, $answers)) {
                continue;
            }
            ListeningAnswer::updateOrCreate(
                ['user_id' => $userId, 'test_attempt_id' => $la->id, 'question_id' => (int) $questionId],
                ['is_flagged' => (bool) $isFlagged]
            );
        }
    }

    private function isCorrect(?string $userAnswer, ?string $correctAnswer): bool
    {
        if ($userAnswer === null || $correctAnswer === null) {
            return false;
        }

        $normalize = function (string $s): string {
            $s = strtolower(strip_tags($s));
            $s = preg_replace('/[^\p{L}\p{N}\s]+/u', ' ', $s) ?? $s;

            return trim(preg_replace('/\s+/u', ' ', $s) ?? $s);
        };

        $userNorm     = $normalize($userAnswer);
        $validAnswers = array_map($normalize, explode('|', $correctAnswer));

        return in_array($userNorm, $validAnswers, true);
    }
}
