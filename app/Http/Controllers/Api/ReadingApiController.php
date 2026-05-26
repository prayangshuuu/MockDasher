<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ReadingAnswer;
use App\Models\ReadingAttempt;
use App\Models\TestAttempt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ReadingApiController extends Controller
{
    private const READING_LIMIT_SECONDS = 3600;

    private const GRACE_SECONDS = 60;

    public function show(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        // Find or create the ReadingAttempt for this TestAttempt
        $ra = $attempt->readingAttempt;

        if (! $ra) {
            $ra = ReadingAttempt::create([
                'test_attempt_id' => $attempt->id,
                'user_id'         => $attempt->user_id,
                'test_set_id'     => $attempt->test_set_id,
                'status'          => 'in_progress',
                'started_at'      => now(),
            ]);
            $attempt->update(['status' => 'reading']);
        }

        if ($ra->status === 'completed') {
            return response()->json(['error' => 'already_completed', 'message' => 'Reading test already completed.'], 409);
        }

        if (! $ra->started_at) {
            $ra->update(['started_at' => now(), 'status' => 'in_progress']);
            $ra->refresh();
        }

        $elapsed          = (int) now()->diffInSeconds($ra->started_at);
        $remainingSeconds = (int) max(0, self::READING_LIMIT_SECONDS - $elapsed);

        if ($remainingSeconds <= 0) {
            if ($ra->status !== 'completed') {
                $ra->update(['status' => 'completed', 'completed_at' => now()]);
                $result = $ra->evaluate();

                return response()->json([
                    'error'      => 'time_expired',
                    'message'    => 'Reading time expired. Test submitted automatically.',
                    'band_score' => $result['band_score'] ?? null,
                    'score'      => $result['total_correct'] ?? null,
                ], 422);
            }
        }

        $passages = Cache::remember(
            "testset:{$ra->test_set_id}:reading-passages",
            3600,
            fn () => $ra->testSet->readingPassages()
                ->with(['questionGroups' => fn ($q) => $q->with(['questions.options'])])
                ->orderBy('passage_number')
                ->get()
        );

        $savedAnswers   = $ra->answers->pluck('answer_text', 'question_id')->toArray();
        $flaggedAnswers = $ra->answers->filter(fn ($a) => $a->is_flagged)->pluck('is_flagged', 'question_id')->toArray();

        $passageData = $passages->map(function ($passage) use ($savedAnswers, $flaggedAnswers) {
            return [
                'id'              => $passage->id,
                'passage_number'  => $passage->passage_number,
                'title'           => $passage->title ?? null,
                'content'         => $passage->content ?? null,
                'question_groups' => $passage->questionGroups->map(function ($group) use ($savedAnswers, $flaggedAnswers) {
                    return [
                        'id'          => $group->id,
                        'group_type'  => $group->group_type ?? null,
                        'instruction' => $group->instruction ?? null,
                        'questions'   => $group->questions->map(function ($q) use ($savedAnswers, $flaggedAnswers) {
                            return [
                                'id'            => $q->id,
                                'question_text' => $q->question_text ?? null,
                                'question_type' => $q->question_type ?? null,
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
                })->values(),
            ];
        });

        return response()->json([
            'attempt_id'         => $attempt->id,
            'reading_attempt_id' => $ra->id,
            'remaining_seconds'  => $remainingSeconds,
            'started_at'         => $ra->started_at?->toIso8601String(),
            'passages'           => $passageData,
        ]);
    }

    public function autosave(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $ra = $attempt->readingAttempt;
        if (! $ra) {
            return response()->json(['error' => 'not_started', 'message' => 'Reading module not started.'], 404);
        }

        if ($ra->status === 'completed') {
            return response()->json(['error' => 'Already completed'], 403);
        }

        if ($this->isTimeExpired($ra)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $request->validate([
            'answers'   => 'array|max:50',
            'answers.*' => 'nullable|string|max:1000',
            'flagged'   => 'array|max:50',
            'flagged.*' => 'nullable|boolean',
        ]);

        $this->upsertAnswers(
            $ra,
            $request->user()->id,
            $this->validQuestionIds($ra),
            $request->input('answers', []),
            $request->input('flagged', [])
        );

        return response()->json(['success' => true, 'saved_at' => now()->toTimeString()]);
    }

    public function submit(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden'], 403);
        }

        $ra = $attempt->readingAttempt;
        if (! $ra) {
            return response()->json(['error' => 'not_started'], 404);
        }

        if ($ra->status === 'completed') {
            return response()->json(['error' => 'Already completed'], 403);
        }

        $request->validate([
            'answers'   => 'array|max:50',
            'answers.*' => 'nullable|string|max:1000',
            'flagged'   => 'array|max:50',
            'flagged.*' => 'nullable|boolean',
        ]);

        $this->upsertAnswers(
            $ra,
            $request->user()->id,
            $this->validQuestionIds($ra),
            $request->input('answers', []),
            $request->input('flagged', [])
        );

        $ra->update(['status' => 'completed', 'completed_at' => now()]);
        $result = $ra->evaluate();

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

        $ra = $attempt->readingAttempt;
        if (! $ra) {
            return response()->json(['error' => 'not_started'], 404);
        }

        if ($ra->status !== 'completed') {
            return response()->json(['error' => 'not_completed', 'message' => 'Reading test not yet submitted.'], 409);
        }

        $data = Cache::remember(
            "reading-result:{$ra->id}",
            3600,
            function () use ($attempt, $ra) {
                $passages = Cache::remember(
                    "testset:{$ra->test_set_id}:reading-passages",
                    3600,
                    fn () => $ra->testSet->readingPassages()
                        ->with(['questionGroups' => fn ($q) => $q->with(['questions.options'])])
                        ->orderBy('passage_number')
                        ->get()
                );
                $answers        = $ra->answers()->with('question')->get()->keyBy('question_id');
                $totalQuestions = $passages
                    ->flatMap(fn ($p) => $p->questionGroups->flatMap(fn ($g) => $g->questions))
                    ->count();

                $passageData = $passages->map(function ($passage) use ($answers) {
                    return [
                        'id'              => $passage->id,
                        'passage_number'  => $passage->passage_number,
                        'title'           => $passage->title ?? null,
                        'question_groups' => $passage->questionGroups->map(function ($group) use ($answers) {
                            return [
                                'id'        => $group->id,
                                'questions' => $group->questions->map(function ($q) use ($answers) {
                                    $userAnswer = $answers->get($q->id);

                                    return [
                                        'id'            => $q->id,
                                        'question_text' => $q->question_text ?? null,
                                        'user_answer'   => $userAnswer?->answer_text,
                                        'is_correct'    => $userAnswer ? $this->isCorrect($userAnswer->answer_text, $q->correct_answer) : false,
                                    ];
                                })->values(),
                            ];
                        })->values(),
                    ];
                });

                return [
                    'attempt_id'      => $attempt->id,
                    'band_score'      => $ra->band_score,
                    'score'           => $ra->total_correct,
                    'total_questions' => $totalQuestions,
                    'passages'        => $passageData,
                ];
            }
        );

        return response()->json($data);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function isTimeExpired(ReadingAttempt $ra): bool
    {
        return $ra->started_at
            && now()->diffInSeconds($ra->started_at) > (self::READING_LIMIT_SECONDS + self::GRACE_SECONDS);
    }

    /** @return array<int> */
    private function validQuestionIds(ReadingAttempt $ra): array
    {
        return $ra->testSet
            ->readingPassages()
            ->with(['questionGroups.questions:id,questionable_id,questionable_type'])
            ->get()
            ->flatMap(fn ($p) => $p->questionGroups->flatMap(fn ($g) => $g->questions->pluck('id')))
            ->map(fn ($id) => (int) $id)
            ->toArray();
    }

    private function upsertAnswers(ReadingAttempt $ra, int $userId, array $validIds, array $answers, array $flagged): void
    {
        foreach ($answers as $questionId => $answerText) {
            if (! in_array((int) $questionId, $validIds, true)) {
                continue;
            }
            ReadingAnswer::updateOrCreate(
                ['user_id' => $userId, 'test_attempt_id' => $ra->id, 'question_id' => (int) $questionId],
                ['answer_text' => $answerText, 'is_flagged' => ! empty($flagged[$questionId])]
            );
        }

        foreach ($flagged as $questionId => $isFlagged) {
            if (! in_array((int) $questionId, $validIds, true) || array_key_exists($questionId, $answers)) {
                continue;
            }
            ReadingAnswer::updateOrCreate(
                ['user_id' => $userId, 'test_attempt_id' => $ra->id, 'question_id' => (int) $questionId],
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
