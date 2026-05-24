<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\AiWritingEvaluation;
use App\Models\TestAttempt;
use App\Models\WritingAnswer;
use App\Models\WritingTask;
use App\Services\GeminiEvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WritingTestController extends Controller
{
    public function show(TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id()) {
            abort(403, 'Unauthorized access.');
        }

        if (! $attempt->started_at) {
            $attempt->update(['started_at' => now(), 'status' => 'writing']);
        }

        if ($attempt->status === 'completed' || $attempt->completed_at) {
            return redirect()->route('dashboard')->with('error', 'Test already completed.');
        }

        $elapsedSeconds   = now()->diffInSeconds($attempt->started_at);
        $totalSeconds     = 60 * 60;
        $remainingSeconds = max(0, $totalSeconds - $elapsedSeconds);

        if ($remainingSeconds <= 0) {
            return $this->forceSubmit($attempt);
        }

        $tasks   = $attempt->testSet->writingTasks()->with('images')->orderBy('task_number')->get();
        $answers = $attempt->writingAnswers->keyBy('writing_task_id');

        return view('user.writing-test.show', compact('attempt', 'tasks', 'answers', 'remainingSeconds'));
    }

    public function autosave(Request $request, TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized or completed'], 403);
        }

        foreach ($request->answers as $taskId => $text) {
            $existing = WritingAnswer::where([
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => $taskId,
            ])->first();

            // Don't overwrite if already submitted
            if ($existing && $existing->submitted_at) {
                continue;
            }

            WritingAnswer::updateOrCreate(
                ['user_id' => auth()->id(), 'test_attempt_id' => $attempt->id, 'writing_task_id' => $taskId],
                ['answer_text' => $text, 'word_count' => str_word_count(strip_tags((string) $text))]
            );
        }

        return response()->json(['success' => true]);
    }

    /**
     * Submit a single writing task. Locks the answer instantly, postponing Gemini AI evaluation.
     *
     * POST /tests/attempts/{attempt}/writing/tasks/{task}/submit
     * Body: { "answer": "..." }
     */
    public function submitTask(Request $request, TestAttempt $attempt, WritingTask $task)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Check not already submitted
        $existingSubmitted = WritingAnswer::where([
            'user_id'         => auth()->id(),
            'test_attempt_id' => $attempt->id,
            'writing_task_id' => $task->id,
        ])->whereNotNull('submitted_at')->first();

        if ($existingSubmitted) {
            return response()->json(['error' => 'Already submitted'], 409);
        }

        $request->validate(['answer' => 'nullable|string']);

        $answer    = trim($request->input('answer', ''));
        $wordCount = $answer ? str_word_count(strip_tags($answer)) : 0;

        // Save the answer and mark as submitted (locks input in taker workspace)
        $writingAnswer = WritingAnswer::updateOrCreate(
            ['user_id' => auth()->id(), 'test_attempt_id' => $attempt->id, 'writing_task_id' => $task->id],
            ['answer_text' => $answer, 'word_count' => $wordCount, 'submitted_at' => now()]
        );

        return response()->json([
            'success'    => true,
            'band_score' => null,
            'evaluation' => null,
            'message'    => 'Task answer locked successfully. Evaluation will be compiled on final submission.'
        ]);
    }

    /**
     * Final submission — mark the attempt as completed, compile batch AI reports, and compute overall band.
     */
    public function submit(Request $request, TestAttempt $attempt)
    {
        if ((int) $attempt->user_id !== (int) auth()->id() || $attempt->status === 'completed') {
            abort(403);
        }

        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        $this->evaluateAllAnswers($attempt);
        $this->saveOverallBand($attempt);

        return redirect()->route('dashboard')->with('success', 'Writing test completed successfully. Your AI evaluation report has been compiled.');
    }

    protected function forceSubmit(TestAttempt $attempt)
    {
        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        $this->evaluateAllAnswers($attempt);
        $this->saveOverallBand($attempt);

        return redirect()->route('dashboard')->with('success', 'Time expired. Writing test submitted. Your AI evaluation report has been compiled.');
    }

    /**
     * Batch compile and save Gemini evaluations for all un-graded tasks in the sitting.
     */
    protected function evaluateAllAnswers(TestAttempt $attempt): void
    {
        $user = $attempt->user;
        $apiKey = $user->getRawOriginal('gemini_api_key');
        if (empty($apiKey)) {
            return;
        }

        try {
            $service = GeminiEvaluationService::forUser($user);
            $tasks = $attempt->testSet->writingTasks()->with('images')->orderBy('task_number')->get();
            foreach ($tasks as $task) {
                $writingAnswer = WritingAnswer::where([
                    'test_attempt_id' => $attempt->id,
                    'writing_task_id' => $task->id,
                ])->first();

                // Skip if answer is empty or already evaluated
                if (!$writingAnswer || empty($writingAnswer->answer_text) || $writingAnswer->band_score !== null) {
                    continue;
                }

                $imageAltText = null;
                if ($task->task_number === 1) {
                    $firstImage = $task->images->first();
                    $imageAltText = $firstImage?->alt_text ?? $task->precontext ?? null;
                }

                $question = trim($task->task_prompt ?? $task->task_description ?? "Writing Task {$task->task_number}");

                $result = $service->evaluateWritingTask(
                    $task->task_number,
                    $question,
                    $imageAltText,
                    strip_tags($writingAnswer->answer_text)
                );

                // Save evaluation back to the writing_answer row
                $writingAnswer->update([
                    'evaluation_json' => $result['evaluation_text'],
                    'band_score'      => $result['band_score'],
                ]);

                // Also update the AiWritingEvaluation summary record for this attempt
                $this->updateAiWritingEvaluationRecord($attempt, $task->task_number, $result, $writingAnswer->answer_text);
            }
        } catch (\Exception $e) {
            Log::error('[WritingTestController] Batch Gemini evaluation failed', [
                'attempt' => $attempt->id,
                'error'   => $e->getMessage(),
            ]);
        }
    }

    /**
     * Update or create the AiWritingEvaluation summary record for an attempt.
     * Called after each task is evaluated so the record stays up to date.
     */
    protected function updateAiWritingEvaluationRecord(
        TestAttempt $attempt,
        int         $taskNumber,
        array       $result,
        string      $answerText
    ): void {
        $data = [
            "task_{$taskNumber}_evaluation_json" => $result['evaluation_text'],
            "task_{$taskNumber}_band_score"      => $result['band_score'],
            "task_{$taskNumber}_answer"          => substr($answerText, 0, 65535), // TEXT limit safety
        ];

        AiWritingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            $data
        );
    }

    /**
     * Compute and persist the overall writing band score (average of all tasks).
     */
    protected function saveOverallBand(TestAttempt $attempt): void
    {
        $scores = $attempt->writingAnswers()
            ->whereNotNull('band_score')
            ->pluck('band_score')
            ->toArray();

        if (empty($scores)) {
            return;
        }

        $overall = round((array_sum($scores) / count($scores)) * 2) / 2;

        AiWritingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            ['band_score' => $overall]
        );
    }
}
