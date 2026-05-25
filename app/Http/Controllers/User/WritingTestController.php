<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Jobs\EvaluateWritingSubmission;
use App\Models\AiWritingEvaluation;
use App\Models\TestAttempt;
use App\Models\WritingAnswer;
use App\Models\WritingTask;
use Illuminate\Http\Request;

class WritingTestController extends Controller
{
    private const WRITING_LIMIT_SECONDS = 3600; // 60 minutes

    private const GRACE_SECONDS = 60;

    public function show(TestAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->completed_at) {
            return redirect()->route('user.history.show', $attempt->id)
                ->with('error', 'This exam has already been finished.');
        }

        if ($attempt->aiWritingEvaluation()->whereNotNull('band_score')->exists()) {
            return redirect()->route('user.tests.start', $attempt->testSet->test_id)
                ->with('error', 'You have already completed the Writing module.');
        }

        if ($attempt->aiWritingEvaluation()->whereIn('evaluation_status', ['pending', 'evaluating'])->exists()) {
            return redirect()->route('user.tests.start', $attempt->testSet->test_id)
                ->with('error', 'Your Writing evaluation is still in progress.');
        }

        if (! $attempt->writing_started_at) {
            $attempt->update(['writing_started_at' => now(), 'status' => 'writing']);
            $attempt->refresh();
        } elseif ($attempt->status !== 'writing') {
            $attempt->update(['status' => 'writing']);
        }

        $elapsed = (int) now()->diffInSeconds($attempt->writing_started_at);
        $remainingSeconds = (int) max(0, self::WRITING_LIMIT_SECONDS - $elapsed);

        if ($remainingSeconds <= 0) {
            return $this->forceSubmit($attempt);
        }

        $tasks = $attempt->testSet->writingTasks()->with('images')->orderBy('task_number')->get();
        $answers = $attempt->writingAnswers->keyBy('writing_task_id');

        return view('user.writing-test.show', compact('attempt', 'tasks', 'answers', 'remainingSeconds'));
    }

    public function autosave(Request $request, TestAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Already completed'], 403);
        }

        if ($this->isTimeExpired($attempt)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $request->validate([
            'answers' => 'array|max:10',
            'answers.*' => 'nullable|string|max:65535',
        ]);

        $validTaskIds = $attempt->testSet->writingTasks()->pluck('id')->toArray();

        foreach ($request->input('answers', []) as $taskId => $text) {
            if (! in_array((int) $taskId, $validTaskIds, true)) {
                continue;
            }

            // Don't overwrite a task that has already been submitted
            $alreadySubmitted = WritingAnswer::where([
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => (int) $taskId,
            ])->whereNotNull('submitted_at')->exists();

            if ($alreadySubmitted) {
                continue;
            }

            WritingAnswer::updateOrCreate(
                [
                    'user_id' => auth()->id(),
                    'test_attempt_id' => $attempt->id,
                    'writing_task_id' => (int) $taskId,
                ],
                [
                    'answer_text' => $text,
                    'word_count' => str_word_count(strip_tags((string) $text)),
                ]
            );
        }

        return response()->json(['success' => true, 'saved_at' => now()->toTimeString()]);
    }

    /**
     * Lock a single writing task answer (no AI evaluation yet).
     * POST /tests/attempts/{attempt}/writing/tasks/{task}/submit
     */
    public function submitTask(Request $request, TestAttempt $attempt, WritingTask $task)
    {
        $this->authorizeAttempt($attempt);

        if ($attempt->status === 'completed') {
            return response()->json(['error' => 'Attempt already completed'], 403);
        }

        // Scope: task must belong to this attempt's test set
        $validTaskIds = $attempt->testSet->writingTasks()->pluck('id')->toArray();
        if (! in_array($task->id, $validTaskIds, true)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        if ($this->isTimeExpired($attempt)) {
            return response()->json(['error' => 'Time expired'], 403);
        }

        $alreadySubmitted = WritingAnswer::where([
            'user_id' => auth()->id(),
            'test_attempt_id' => $attempt->id,
            'writing_task_id' => $task->id,
        ])->whereNotNull('submitted_at')->exists();

        if ($alreadySubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        $request->validate(['answer' => 'nullable|string|max:65535']);

        $answer = trim($request->input('answer', ''));
        $wordCount = $answer ? str_word_count(strip_tags($answer)) : 0;

        WritingAnswer::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => $task->id,
            ],
            [
                'answer_text' => $answer,
                'word_count' => $wordCount,
                'submitted_at' => now(),
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Task answer locked. Evaluation will be compiled on final submission.',
        ]);
    }

    /**
     * Final submission — trigger AI evaluation and redirect.
     */
    public function submit(Request $request, TestAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);
        abort_if($attempt->completed_at !== null, 403, 'This exam has already been finished.');

        $evaluation = AiWritingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            ['evaluation_status' => 'pending', 'failure_reason' => null]
        );

        EvaluateWritingSubmission::dispatch($attempt->id);

        $evaluation->refresh();
        if ($evaluation->evaluation_status === 'failed') {
            return redirect()->route('user.writing.show', $attempt->id)
                ->with('error', $evaluation->failure_reason ?: 'Writing evaluation could not be completed.');
        }

        if ($evaluation->evaluation_status === 'completed') {
            $attempt->update(['status' => 'in_progress']);

            return redirect()->route('user.writing.result', $attempt->id)
                ->with('success', 'Writing test completed. Your AI evaluation report has been compiled.');
        }

        return redirect()->route('user.writing.result', $attempt->id)
            ->with('success', 'Writing test submitted. Your AI evaluation is in progress.');
    }

    public function result(TestAttempt $attempt)
    {
        $this->authorizeAttempt($attempt);

        if (!$attempt->completed_at && $attempt->status !== 'writing' && !$attempt->aiWritingEvaluation) {
            return redirect()->route('user.writing.show', $attempt->id);
        }

        $tasks = $attempt->testSet->writingTasks()->with('images')->orderBy('task_number')->get();
        $answers = $attempt->writingAnswers->keyBy('writing_task_id');
        $evaluation = $attempt->aiWritingEvaluation;

        return view('user.writing-test.result', compact('attempt', 'tasks', 'answers', 'evaluation'));
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    protected function forceSubmit(TestAttempt $attempt)
    {
        if (! $attempt->aiWritingEvaluation()->whereNotNull('band_score')->exists()) {
            $evaluation = AiWritingEvaluation::updateOrCreate(
                ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
                ['evaluation_status' => 'pending', 'failure_reason' => null]
            );

            EvaluateWritingSubmission::dispatch($attempt->id);
            $evaluation->refresh();

            if ($evaluation->evaluation_status === 'failed') {
                return redirect()->route('user.writing.show', $attempt->id)
                    ->with('error', $evaluation->failure_reason ?: 'Writing evaluation could not be completed.');
            }

            if ($evaluation->evaluation_status === 'completed') {
                $attempt->update(['status' => 'in_progress']);
            }
        }

        return redirect()->route('user.writing.result', $attempt->id)
            ->with('success', 'Time expired. Writing test submitted automatically.');
    }

    private function authorizeAttempt(TestAttempt $attempt): void
    {
        abort_unless((int) $attempt->user_id === (int) auth()->id(), 403, 'Unauthorized access.');
    }

    private function isTimeExpired(TestAttempt $attempt): bool
    {
        return $attempt->writing_started_at
            && now()->diffInSeconds($attempt->writing_started_at) > (self::WRITING_LIMIT_SECONDS + self::GRACE_SECONDS);
    }
}
