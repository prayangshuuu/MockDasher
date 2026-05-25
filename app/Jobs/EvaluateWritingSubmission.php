<?php

namespace App\Jobs;

use App\Models\AiWritingEvaluation;
use App\Models\TestAttempt;
use App\Models\WritingAnswer;
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

    public function handle(): void
    {
        $attempt = TestAttempt::with([
            'writingAnswers',
            'testSet.writingTasks.images',
            'user',
        ])->find($this->testAttemptId);

        if (! $attempt) {
            Log::error("EvaluateWritingSubmission: TestAttempt {$this->testAttemptId} not found.");

            return;
        }

        $summary = AiWritingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            ['evaluation_status' => 'evaluating', 'failure_reason' => null]
        );

        $tasks = $attempt->testSet->writingTasks()->orderBy('task_number')->get();
        if ($tasks->isEmpty()) {
            $summary->update(['evaluation_status' => 'failed', 'failure_reason' => 'No writing tasks configured.']);

            return;
        }

        $apiKey = $attempt->user?->getRawOriginal('gemini_api_key') ?: config('services.gemini.key');
        if (empty($apiKey)) {
            $summary->update(['evaluation_status' => 'failed', 'failure_reason' => 'Missing Gemini API key.']);

            return;
        }

        try {
            $service = GeminiEvaluationService::forUser($attempt->user);
            $scores = [];

            foreach ($tasks as $task) {
                $answer = WritingAnswer::firstOrCreate(
                    [
                        'user_id' => $attempt->user_id,
                        'test_attempt_id' => $attempt->id,
                        'writing_task_id' => $task->id,
                    ],
                    [
                        'answer_text' => '',
                        'word_count' => 0,
                        'submitted_at' => now(),
                    ]
                );

                if ($answer->band_score === null) {
                    $imageAltText = null;
                    if ((int) $task->task_number === 1) {
                        $firstImage = $task->images->first();
                        $imageAltText = $firstImage?->alt_text ?? $task->precontext ?? null;
                    }

                    $question = trim($task->task_prompt ?? $task->task_description ?? "Writing Task {$task->task_number}");
                    $result = $service->evaluateWritingTask(
                        (int) $task->task_number,
                        $question,
                        $imageAltText,
                        strip_tags((string) $answer->answer_text)
                    );

                    if (! $result['success'] || $result['band_score'] === null) {
                        Log::warning("EvaluateWritingSubmission: Gemini failed for Task {$task->task_number}, attempt {$attempt->id}. Retrying once.");

                        // One additional retry with a short pause before giving up
                        sleep(5);
                        $result = $service->evaluateWritingTask(
                            (int) $task->task_number,
                            $question,
                            $imageAltText,
                            strip_tags((string) $answer->answer_text)
                        );
                    }

                    if (! $result['success'] || $result['band_score'] === null) {
                        $summary->update([
                            'evaluation_status' => 'failed',
                            'failure_reason' => "Gemini returned no score for Writing Task {$task->task_number}.",
                        ]);

                        return;
                    }

                    $answer->update([
                        'evaluation_json' => $result['evaluation_text'],
                        'band_score' => $result['band_score'],
                        'submitted_at' => $answer->submitted_at ?? now(),
                    ]);
                }

                $scores[] = (float) $answer->fresh()->band_score;

                $summary->fill([
                    "task_{$task->task_number}_answer" => substr((string) $answer->answer_text, 0, 65535),
                    "task_{$task->task_number}_evaluation_json" => $answer->fresh()->evaluation_json,
                    "task_{$task->task_number}_band_score" => $answer->fresh()->band_score,
                ])->save();
            }

            if (count($scores) !== $tasks->count()) {
                $summary->update(['evaluation_status' => 'failed', 'failure_reason' => 'Not all writing tasks were scored.']);

                return;
            }

            $summary->update([
                'band_score' => round((array_sum($scores) / count($scores)) * 2) / 2,
                'evaluation_status' => 'completed',
                'failure_reason' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('EvaluateWritingSubmission failed: '.$e->getMessage());
            $summary->update([
                'evaluation_status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);
        }
    }
}
