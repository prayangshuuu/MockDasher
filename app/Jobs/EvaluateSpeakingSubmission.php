<?php

namespace App\Jobs;

use App\Models\AiSpeakingEvaluation;
use App\Models\TestAttempt;
use App\Services\GeminiEvaluationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class EvaluateSpeakingSubmission implements ShouldQueue
{
    use Queueable;

    protected int $testAttemptId;
    protected string $transcript;

    /**
     * Create a new job instance.
     */
    public function __construct(int $testAttemptId, string $transcript)
    {
        $this->testAttemptId = $testAttemptId;
        $this->transcript = $transcript;
    }

    /**
     * Execute the job.
     */
    public function handle(GeminiEvaluationService $service): void
    {
        $attempt = TestAttempt::with(['test.speakingQuestions'])->find($this->testAttemptId);

        if (!$attempt) {
            Log::error("EvaluateSpeakingSubmission: TestAttempt {$this->testAttemptId} not found.");
            return;
        }

        $questionsRaw = $attempt->test->speakingQuestions()->orderBy('part')->get();
        if ($questionsRaw->isEmpty()) {
            return;
        }

        $questions = [];
        foreach ($questionsRaw as $q) {
            $questions[] = "Part {$q->part}: {$q->prompt_text}";
        }

        $transcriptToUse = empty(trim($this->transcript)) ? "No transcript provided by user." : strip_tags($this->transcript);

        try {
            $result = $service->evaluateSpeaking($questions, $transcriptToUse);

            AiSpeakingEvaluation::updateOrCreate(
                [
                    'user_id' => $attempt->user_id,
                    'test_attempt_id' => $attempt->id,
                ],
                [
                    'full_transcript' => $transcriptToUse,
                    'evaluation_text' => $result['evaluation_text'],
                    'band_score' => $result['band_score'],
                ]
            );
        } catch (\Exception $e) {
            Log::error("Failed to process Speaking AI Evaluation: " . $e->getMessage());
        }
    }
}
