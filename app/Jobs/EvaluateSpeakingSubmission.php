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

    public function __construct(int $testAttemptId)
    {
        $this->testAttemptId = $testAttemptId;
    }

    public function handle(GeminiEvaluationService $service): void
    {
        $attempt = TestAttempt::with([
            'testSet.speakingQuestions',
            'speakingAnswers',
        ])->find($this->testAttemptId);

        if (!$attempt) {
            Log::error("EvaluateSpeakingSubmission: TestAttempt {$this->testAttemptId} not found.");
            return;
        }

        $questions = $attempt->testSet->speakingQuestions()
            ->orderBy('part')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            return;
        }

        // Map answers by question id for O(1) lookup
        $answersByQuestion = $attempt->speakingAnswers
            ->keyBy('speaking_question_id');

        $qaItems = [];
        foreach ($questions as $q) {
            $answer = $answersByQuestion->get($q->id);
            $qaItems[] = [
                'part'     => $q->part,
                'question' => $q->question_text,
                'answer'   => $answer ? trim($answer->transcript_text ?? '') : '',
            ];
        }

        try {
            $result = $service->evaluateSpeaking($qaItems);

            AiSpeakingEvaluation::updateOrCreate(
                [
                    'user_id'         => $attempt->user_id,
                    'test_attempt_id' => $attempt->id,
                ],
                [
                    'full_transcript'  => json_encode(array_column($qaItems, 'answer', 'question'), JSON_UNESCAPED_UNICODE),
                    'evaluation_text'  => $result['evaluation_text'],
                    'band_score'       => $result['band_score'],
                ]
            );
        } catch (\Exception $e) {
            Log::error('EvaluateSpeakingSubmission failed: ' . $e->getMessage());
        }
    }
}
