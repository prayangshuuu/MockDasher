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

    /**
     * Create a new job instance.
     */
    public function __construct(int $testAttemptId)
    {
        $this->testAttemptId = $testAttemptId;
    }

    /**
     * Execute the job.
     */
    public function handle(GeminiEvaluationService $service): void
    {
        $attempt = TestAttempt::with(['writingAnswers.writingTask', 'testSet.writingTasks'])->find($this->testAttemptId);

        if (!$attempt) {
            Log::error("EvaluateWritingSubmission: TestAttempt {$this->testAttemptId} not found.");
            return;
        }

        $tasks = $attempt->testSet->writingTasks()->orderBy('task_number')->get();
        if ($tasks->isEmpty()) {
            return;
        }

        $task1 = $tasks->where('task_number', 1)->first();
        $task2 = $tasks->where('task_number', 2)->first();

        // Get answers
        $answers = $attempt->writingAnswers->keyBy('writing_task_id');
        
        $task1Answer = $task1 && $answers->has($task1->id) ? $answers->get($task1->id)->answer_text : null;
        $task2Answer = $task2 && $answers->has($task2->id) ? $answers->get($task2->id)->answer_text : null;

        $task1Prompt = $task1 ? $task1->task_prompt : 'No Task 1 assigned.';
        $task2Prompt = $task2 ? $task2->task_prompt : 'No Task 2 assigned.';

        // Ensure we strip tags as TinyMCE or similar might save HTML.
        $task1AnswerPlain = $task1Answer ? strip_tags($task1Answer) : 'No answer provided.';
        $task2AnswerPlain = $task2Answer ? strip_tags($task2Answer) : 'No answer provided.';

        try {
            $result = $service->evaluateWriting($task1Prompt, $task2Prompt, $task1AnswerPlain, $task2AnswerPlain);

            AiWritingEvaluation::updateOrCreate(
                [
                    'user_id' => $attempt->user_id,
                    'test_attempt_id' => $attempt->id,
                ],
                [
                    'task_1_answer' => $task1AnswerPlain,
                    'task_2_answer' => $task2AnswerPlain,
                    'evaluation_text' => $result['evaluation_text'],
                    'band_score' => $result['band_score'],
                ]
            );
        } catch (\Exception $e) {
            Log::error("Failed to process Writing AI Evaluation: " . $e->getMessage());
        }
    }
}
