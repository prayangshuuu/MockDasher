<?php

namespace App\Jobs;

use App\Models\AiWritingEvaluation;
use App\Models\TestAttempt;
use App\Services\GeminiEvaluationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EvaluateWritingSubmission implements ShouldQueue
{
    use Queueable;

    protected int $testAttemptId;

    public function __construct(int $testAttemptId)
    {
        $this->testAttemptId = $testAttemptId;
    }

    public function handle(GeminiEvaluationService $service): void
    {
        $attempt = TestAttempt::with([
            'writingAnswers',
            'testSet.writingTasks',
        ])->find($this->testAttemptId);

        if (!$attempt) {
            Log::error("EvaluateWritingSubmission: TestAttempt {$this->testAttemptId} not found.");
            return;
        }

        $tasks = $attempt->testSet->writingTasks()->orderBy('task_number')->get();
        if ($tasks->isEmpty()) {
            return;
        }

        $task1 = $tasks->firstWhere('task_number', 1);
        $task2 = $tasks->firstWhere('task_number', 2);

        $answers = $attempt->writingAnswers->keyBy('writing_task_id');

        $task1Answer = ($task1 && $answers->has($task1->id))
            ? strip_tags($answers->get($task1->id)->answer_text)
            : null;

        $task2Answer = ($task2 && $answers->has($task2->id))
            ? strip_tags($answers->get($task2->id)->answer_text)
            : null;

        $task1Prompt     = $task1 ? ($task1->task_prompt ?? $task1->task_description ?? 'No Task 1 assigned.') : 'No Task 1 assigned.';
        $task1Precontext = $task1 ? $task1->precontext : null;
        $task2Prompt     = $task2 ? ($task2->task_prompt ?? $task2->task_description ?? 'No Task 2 assigned.') : 'No Task 2 assigned.';

        try {
            $result = $service->evaluateWriting(
                $task1Prompt,
                $task1Precontext,
                $task2Prompt,
                $task1Answer,
                $task2Answer
            );

            AiWritingEvaluation::updateOrCreate(
                [
                    'user_id'         => $attempt->user_id,
                    'test_attempt_id' => $attempt->id,
                ],
                [
                    'task_1_answer'   => $task1Answer,
                    'task_2_answer'   => $task2Answer,
                    'evaluation_text' => $result['evaluation_text'],
                    'band_score'      => $result['band_score'],
                ]
            );
        } catch (\Exception $e) {
            Log::error('EvaluateWritingSubmission failed: ' . $e->getMessage());
        }
    }
}
