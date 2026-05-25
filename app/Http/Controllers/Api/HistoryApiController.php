<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TestAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HistoryApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $attempts = TestAttempt::with(['testSet.test', 'readingAttempt', 'listeningAttempt', 'aiWritingEvaluation', 'aiSpeakingEvaluation'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(15);

        $data = $attempts->map(fn (TestAttempt $a) => $this->formatSummary($a));

        return response()->json([
            'data' => $data,
            'meta' => [
                'current_page' => $attempts->currentPage(),
                'last_page'    => $attempts->lastPage(),
                'per_page'     => $attempts->perPage(),
                'total'        => $attempts->total(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $attempt = TestAttempt::with([
            'testSet.test',
            'writingAnswers.writingTask',
            'speakingAnswers.question',
            'readingAttempt.answers.question',
            'listeningAttempt.answers.question',
            'aiWritingEvaluation',
            'aiSpeakingEvaluation',
        ])->where('user_id', $request->user()->id)->findOrFail($id);

        return response()->json(['data' => $this->formatDetail($attempt)]);
    }

    // ── Formatters ─────────────────────────────────────────────────────────────

    private function formatSummary(TestAttempt $a): array
    {
        return [
            'id'              => $a->id,
            'test_title'      => optional(optional($a->testSet)->test)->title ?? 'IELTS Mock',
            'status'          => $a->status,
            'overall_band'    => $a->overall_band,
            'reading_band'    => $a->reading_band,
            'listening_band'  => $a->listening_band,
            'writing_band'    => $a->writing_band,
            'speaking_band'   => $a->speaking_band,
            'reading_score'   => $a->reading_score,
            'listening_score' => $a->listening_score,
            'started_at'      => $a->started_at?->toIso8601String(),
            'completed_at'    => $a->completed_at?->toIso8601String(),
            'time_spent'      => $a->time_spent,
        ];
    }

    private function formatDetail(TestAttempt $a): array
    {
        $base = $this->formatSummary($a);

        // Writing answers
        $base['writing'] = [
            'evaluation_status' => $a->aiWritingEvaluation?->evaluation_status ?? 'not_started',
            'band_score'        => $a->aiWritingEvaluation?->band_score,
            'answers'           => $a->writingAnswers->map(fn ($w) => [
                'task_number' => optional($w->writingTask)->task_number,
                'task_title'  => optional($w->writingTask)->task_title,
                'word_count'  => $w->word_count,
                'band_score'  => $w->band_score,
                'submitted_at' => $w->submitted_at?->toIso8601String(),
                'evaluation'  => $w->evaluation_json ? json_decode($w->evaluation_json, true) : null,
            ])->values(),
        ];

        // Speaking answers
        $base['speaking'] = [
            'evaluation_status' => $a->aiSpeakingEvaluation?->evaluation_status ?? 'not_started',
            'band_score'        => $a->aiSpeakingEvaluation?->band_score,
            'answers'           => $a->speakingAnswers->map(fn ($s) => [
                'part'           => optional($s->question)->part,
                'question_text'  => optional($s->question)->question_text,
                'transcript'     => $s->transcript_text,
                'duration_seconds' => $s->duration_seconds,
                'band_score'     => $s->band_score,
                'evaluation'     => $s->evaluation_json ? json_decode($s->evaluation_json, true) : null,
            ])->values(),
        ];

        // Listening result
        $la = $a->listeningAttempt;
        $base['listening'] = $la ? [
            'status'     => $la->status,
            'band_score' => $la->band_score,
            'score'      => $la->total_correct,
        ] : null;

        // Reading result
        $ra = $a->readingAttempt;
        $base['reading'] = $ra ? [
            'status'     => $ra->status,
            'band_score' => $ra->band_score,
            'score'      => $ra->total_correct,
        ] : null;

        return $base;
    }
}
