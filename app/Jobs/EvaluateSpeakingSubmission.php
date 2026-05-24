<?php

namespace App\Jobs;

use App\Models\AiSpeakingEvaluation;
use App\Models\SpeakingAnswer;
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

    public function handle(): void
    {
        $attempt = TestAttempt::with([
            'testSet.speakingQuestions',
            'speakingAnswers',
            'user',
        ])->find($this->testAttemptId);

        if (! $attempt) {
            Log::error("EvaluateSpeakingSubmission: TestAttempt {$this->testAttemptId} not found.");

            return;
        }

        $summary = AiSpeakingEvaluation::updateOrCreate(
            ['user_id' => $attempt->user_id, 'test_attempt_id' => $attempt->id],
            ['evaluation_status' => 'evaluating', 'failure_reason' => null]
        );

        $questions = $attempt->testSet->speakingQuestions()
            ->orderBy('part')
            ->orderBy('id')
            ->get();

        if ($questions->isEmpty()) {
            $summary->update(['evaluation_status' => 'failed', 'failure_reason' => 'No speaking questions configured.']);

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

            foreach ($questions as $question) {
                $answer = SpeakingAnswer::firstOrCreate(
                    [
                        'user_id' => $attempt->user_id,
                        'test_attempt_id' => $attempt->id,
                        'speaking_question_id' => $question->id,
                    ],
                    [
                        'transcript_text' => '',
                        'duration_seconds' => 0,
                        'submitted_at' => now(),
                    ]
                );

                if ($answer->band_score === null) {
                    $result = $service->evaluateSpeakingQuestion(
                        (int) $question->part,
                        (string) $question->question_text,
                        trim((string) $answer->transcript_text)
                    );

                    if (! $result['success'] || $result['band_score'] === null) {
                        $summary->update([
                            'evaluation_status' => 'failed',
                            'failure_reason' => "Gemini returned no score for Speaking question {$question->id}.",
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
            }

            if (count($scores) !== $questions->count()) {
                $summary->update(['evaluation_status' => 'failed', 'failure_reason' => 'Not all speaking questions were scored.']);

                return;
            }

            $answers = $attempt->speakingAnswers()->with('question')->get();
            $transcript = $answers->map(function ($answer) {
                $question = $answer->question;

                return $question
                    ? "Part {$question->part} - Q: {$question->question_text}\nA: ".($answer->transcript_text ?: '[No answer]')
                    : null;
            })->filter()->implode("\n\n");

            $allEvaluations = $answers->whereNotNull('evaluation_json')->map(fn ($answer) => [
                'question_id' => $answer->speaking_question_id,
                'part' => $answer->question?->part,
                'question' => $answer->question?->question_text,
                'band_score' => $answer->band_score,
                'evaluation' => json_decode($answer->evaluation_json, true),
            ])->values()->toArray();

            $summary->update([
                'full_transcript' => $transcript,
                'evaluation_json' => json_encode($allEvaluations, JSON_UNESCAPED_UNICODE),
                'band_score' => round((array_sum($scores) / count($scores)) * 2) / 2,
                'evaluation_status' => 'completed',
                'failure_reason' => null,
            ]);
        } catch (\Exception $e) {
            Log::error('EvaluateSpeakingSubmission failed: '.$e->getMessage());
            $summary->update([
                'evaluation_status' => 'failed',
                'failure_reason' => $e->getMessage(),
            ]);
        }
    }
}
