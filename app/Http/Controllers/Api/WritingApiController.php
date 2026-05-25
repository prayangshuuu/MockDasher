<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\EvaluateWritingSubmission;
use App\Models\AiWritingEvaluation;
use App\Models\TestAttempt;
use App\Models\WritingAnswer;
use App\Models\WritingTask;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WritingApiController extends Controller
{
    private const WRITING_LIMIT_SECONDS = 3600;

    private const GRACE_SECONDS = 60;

    public function show(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        if ($attempt->aiWritingEvaluation()->whereNotNull('band_score')->exists()) {
            return response()->json(['error' => 'already_completed', 'message' => 'Writing module already completed.'], 409);
        }

        if ($attempt->aiWritingEvaluation()->whereIn('evaluation_status', ['pending', 'evaluating'])->exists()) {
            return response()->json(['error' => 'evaluation_in_progress', 'message' => 'Your Writing evaluation is still in progress.'], 409);
        }

        if (! $attempt->writing_started_at) {
            $attempt->update(['writing_started_at' => now(), 'status' => 'writing']);
            $attempt->refresh();
        } elseif ($attempt->status !== 'writing') {
            $attempt->update(['status' => 'writing']);
        }

        $elapsed          = (int) now()->diffInSeconds($attempt->writing_started_at);
        $remainingSeconds = (int) max(0, self::WRITING_LIMIT_SECONDS - $elapsed);

        if ($remainingSeconds <= 0) {
            if (! $attempt->aiWritingEvaluation()->whereNotNull('band_score')->exists()) {
                AiWritingEvaluation::updateOrCreate(
                    ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
                    ['evaluation_status' => 'pending', 'failure_reason' => null]
                );
                EvaluateWritingSubmission::dispatch($attempt->id);
            }

            return response()->json([
                'error'   => 'time_expired',
                'message' => 'Writing time has expired. Please submit now.',
            ], 422);
        }

        $tasks   = $attempt->testSet->writingTasks()->with('images')->orderBy('task_number')->get();
        $answers = $attempt->writingAnswers->keyBy('writing_task_id');

        $taskData = $tasks->map(function (WritingTask $task) use ($answers) {
            $answer = $answers->get($task->id);

            return [
                'id'          => $task->id,
                'task_number' => $task->task_number,
                'task_title'  => $task->task_title,
                'task_prompt' => $task->task_prompt,
                'precontext'  => $task->precontext,
                'min_words'   => $task->minimum_word_count,
                'answer'      => $answer ? [
                    'text'       => $answer->answer_text,
                    'word_count' => $answer->word_count,
                    'submitted'  => $answer->submitted_at !== null,
                    'band_score' => $answer->band_score,
                ] : null,
            ];
        });

        return response()->json([
            'attempt_id'         => $attempt->id,
            'remaining_seconds'  => $remainingSeconds,
            'writing_started_at' => $attempt->writing_started_at?->toIso8601String(),
            'tasks'              => $taskData,
        ]);
    }

    public function autosave(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Already completed'], 403);
        }

        if ($this->isTimeExpired($attempt)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $request->validate([
            'answers'   => 'array|max:10',
            'answers.*' => 'nullable|string|max:65535',
        ]);

        $validTaskIds = $attempt->testSet->writingTasks()->pluck('id')->toArray();

        foreach ($request->input('answers', []) as $taskId => $text) {
            if (! in_array((int) $taskId, $validTaskIds, true)) {
                continue;
            }

            $alreadySubmitted = WritingAnswer::where([
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => (int) $taskId,
            ])->whereNotNull('submitted_at')->exists();

            if ($alreadySubmitted) {
                continue;
            }

            WritingAnswer::updateOrCreate(
                [
                    'user_id'         => $request->user()->id,
                    'test_attempt_id' => $attempt->id,
                    'writing_task_id' => (int) $taskId,
                ],
                [
                    'answer_text' => $text,
                    'word_count'  => str_word_count(strip_tags((string) $text)),
                ]
            );
        }

        return response()->json(['success' => true, 'saved_at' => now()->toTimeString()]);
    }

    public function submitTask(Request $request, TestAttempt $attempt, WritingTask $task): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Attempt already completed'], 403);
        }

        $validTaskIds = $attempt->testSet->writingTasks()->pluck('id')->toArray();
        if (! in_array($task->id, $validTaskIds, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($this->isTimeExpired($attempt)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $alreadySubmitted = WritingAnswer::where([
            'user_id'         => $request->user()->id,
            'test_attempt_id' => $attempt->id,
            'writing_task_id' => $task->id,
        ])->whereNotNull('submitted_at')->exists();

        if ($alreadySubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        $request->validate(['answer' => 'nullable|string|max:65535']);

        $answer    = trim($request->input('answer', ''));
        $wordCount = $answer ? str_word_count(strip_tags($answer)) : 0;

        WritingAnswer::updateOrCreate(
            [
                'user_id'         => $request->user()->id,
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => $task->id,
            ],
            [
                'answer_text'  => $answer,
                'word_count'   => $wordCount,
                'submitted_at' => now(),
            ]
        );

        return response()->json(['success' => true]);
    }

    public function submit(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        if ($attempt->completed_at !== null) {
            return response()->json(['error' => 'This exam has already been finished.'], 403);
        }

        AiWritingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            ['evaluation_status' => 'pending', 'failure_reason' => null]
        );

        EvaluateWritingSubmission::dispatch($attempt->id);

        return response()->json([
            'success'           => true,
            'evaluation_status' => 'pending',
            'message'           => 'Writing submitted. AI evaluation queued.',
        ]);
    }

    public function result(Request $request, TestAttempt $attempt): JsonResponse
    {
        if ((int) $attempt->user_id !== (int) $request->user()->id) {
            return response()->json(['error' => 'forbidden', 'message' => 'Unauthorized access.'], 403);
        }

        $evaluation = $attempt->aiWritingEvaluation;
        $tasks      = $attempt->testSet->writingTasks()->orderBy('task_number')->get();
        $answers    = $attempt->writingAnswers->keyBy('writing_task_id');

        $taskData = $tasks->map(function (WritingTask $task) use ($answers) {
            $answer = $answers->get($task->id);

            return [
                'task_number' => $task->task_number,
                'band_score'  => $answer?->band_score,
                'word_count'  => $answer?->word_count,
                'evaluation'  => $answer?->evaluation_json ? json_decode($answer->evaluation_json, true) : null,
            ];
        });

        return response()->json([
            'attempt_id'        => $attempt->id,
            'writing_band'      => $evaluation?->band_score,
            'evaluation_status' => $evaluation?->evaluation_status ?? 'not_started',
            'failure_reason'    => $evaluation?->failure_reason,
            'tasks'             => $taskData,
        ]);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function isTimeExpired(TestAttempt $attempt): bool
    {
        return $attempt->writing_started_at
            && now()->diffInSeconds($attempt->writing_started_at) > (self::WRITING_LIMIT_SECONDS + self::GRACE_SECONDS);
    }
}
