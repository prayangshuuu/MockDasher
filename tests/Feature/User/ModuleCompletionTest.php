<?php

namespace Tests\Feature\User;

use App\Models\ListeningAnswer;
use App\Models\ListeningAttempt;
use App\Models\Question;
use App\Models\ReadingAnswer;
use App\Models\ReadingAttempt;
use App\Models\SpeakingAnswer;
use App\Models\SpeakingQuestion;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;
use App\Models\WritingAnswer;
use App\Models\WritingTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class ModuleCompletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_writing_submission_completes_module_without_finishing_full_exam(): void
    {
        config(['services.gemini.key' => 'test-key']);
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::sequence()
                ->push($this->geminiJsonResponse(6.0))
                ->push($this->geminiJsonResponse(7.0)),
        ]);

        [$user, $attempt, $tasks] = $this->writingAttempt();

        foreach ($tasks as $task) {
            WritingAnswer::create([
                'user_id' => $user->id,
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => $task->id,
                'answer_text' => 'A complete answer.',
                'word_count' => 3,
                'submitted_at' => now(),
            ]);
        }

        $response = $this->actingAs($user)->post(route('user.writing.submit', $attempt));

        $response->assertRedirect(route('user.writing.result', $attempt));

        $attempt->refresh();
        $this->assertNull($attempt->completed_at);
        $this->assertSame('in_progress', $attempt->status);
        $this->assertDatabaseHas('ai_writing_evaluations', [
            'test_attempt_id' => $attempt->id,
            'band_score' => 6.5,
            'evaluation_status' => 'completed',
        ]);
    }

    public function test_writing_submission_without_api_key_does_not_complete_module_or_exam(): void
    {
        config(['services.gemini.key' => null]);

        [$user, $attempt] = $this->writingAttempt();

        $response = $this->actingAs($user)->post(route('user.writing.submit', $attempt));

        $response->assertRedirect(route('user.writing.show', $attempt));

        $attempt->refresh();
        $this->assertNull($attempt->completed_at);
        $this->assertNotSame('completed', $attempt->status);
        $this->assertDatabaseHas('ai_writing_evaluations', [
            'test_attempt_id' => $attempt->id,
            'evaluation_status' => 'failed',
        ]);
    }

    public function test_speaking_submission_completes_module_without_finishing_full_exam(): void
    {
        config(['services.gemini.key' => 'test-key']);
        Http::fake([
            'generativelanguage.googleapis.com/*' => Http::sequence()
                ->push($this->geminiJsonResponse(5.5))
                ->push($this->geminiJsonResponse(6.5)),
        ]);

        [$user, $attempt, $questions] = $this->speakingAttempt();

        foreach ($questions as $question) {
            SpeakingAnswer::create([
                'user_id' => $user->id,
                'test_attempt_id' => $attempt->id,
                'speaking_question_id' => $question->id,
                'transcript_text' => 'A complete spoken answer.',
                'duration_seconds' => 30,
                'submitted_at' => now(),
            ]);
        }

        $response = $this->actingAs($user)->post(route('user.speaking.submit', $attempt));

        $response->assertRedirect(route('user.speaking.result', $attempt));

        $attempt->refresh();
        $this->assertNull($attempt->completed_at);
        $this->assertSame('in_progress', $attempt->status);
        $this->assertDatabaseHas('ai_speaking_evaluations', [
            'test_attempt_id' => $attempt->id,
            'band_score' => 6.0,
            'evaluation_status' => 'completed',
        ]);
    }

    public function test_full_exam_finish_zero_fills_incomplete_modules(): void
    {
        [$user, $attempt] = $this->writingAttempt();

        $response = $this->actingAs($user)->post(route('user.tests.finish', $attempt));

        $response->assertRedirect(route('user.history.show', $attempt));

        $attempt->refresh();
        $this->assertSame('completed', $attempt->status);
        $this->assertNotNull($attempt->completed_at);
        $this->assertSame(0.0, (float) $attempt->readingAttempt->band_score);
        $this->assertSame(0.0, (float) $attempt->listeningAttempt->band_score);
        $this->assertSame(0.0, (float) $attempt->aiWritingEvaluation->band_score);
        $this->assertSame(0.0, (float) $attempt->aiSpeakingEvaluation->band_score);
    }

    public function test_answer_scoring_normalizes_punctuation_and_spacing(): void
    {
        [$user, $attempt] = $this->writingAttempt();

        $question = Question::create([
            'questionable_id' => $attempt->test_set_id,
            'questionable_type' => TestSet::class,
            'question_text' => 'What was opened?',
            'question_type' => 'short_answer',
            'correct_answer' => 'city centre|city center',
        ]);

        $readingAttempt = ReadingAttempt::create([
            'user_id' => $user->id,
            'test_set_id' => $attempt->test_set_id,
            'test_attempt_id' => $attempt->id,
            'status' => 'completed',
        ]);
        ReadingAnswer::create([
            'user_id' => $user->id,
            'test_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'answer_text' => ' City   Centre! ',
        ]);

        $listeningAttempt = ListeningAttempt::create([
            'user_id' => $user->id,
            'test_set_id' => $attempt->test_set_id,
            'test_attempt_id' => $attempt->id,
            'status' => 'completed',
        ]);
        ListeningAnswer::create([
            'user_id' => $user->id,
            'test_attempt_id' => $attempt->id,
            'question_id' => $question->id,
            'answer_text' => 'city-center',
        ]);

        $this->assertSame(1, $readingAttempt->calculateRawScore());
        $this->assertSame(1, $listeningAttempt->calculateRawScore());
    }

    private function writingAttempt(): array
    {
        $user = User::factory()->create();
        $test = Test::create([
            'book_number' => 19,
            'year' => 2026,
            'exam_type' => 'Academic',
            'status' => 'published',
        ]);
        $testSet = TestSet::create(['test_id' => $test->id, 'set_number' => 1]);
        $attempt = TestAttempt::create([
            'user_id' => $user->id,
            'test_set_id' => $testSet->id,
            'status' => 'writing',
            'started_at' => now(),
            'writing_started_at' => now(),
        ]);

        $tasks = collect([1, 2])->map(fn (int $taskNumber) => WritingTask::create([
            'test_set_id' => $testSet->id,
            'task_number' => $taskNumber,
            'task_title' => "Task {$taskNumber}",
            'task_prompt' => "Prompt {$taskNumber}",
            'minimum_word_count' => $taskNumber === 1 ? 150 : 250,
        ]));

        return [$user, $attempt, $tasks];
    }

    private function speakingAttempt(): array
    {
        [$user, $attempt] = $this->writingAttempt();
        $attempt->update([
            'status' => 'speaking',
            'speaking_started_at' => now(),
        ]);

        $questions = collect([1, 2])->map(fn (int $part) => SpeakingQuestion::create([
            'test_set_id' => $attempt->test_set_id,
            'part' => $part,
            'question_text' => "Question {$part}",
            'time_limit' => 60,
        ]));

        return [$user, $attempt, $questions];
    }

    private function geminiJsonResponse(float $score): array
    {
        return [
            'candidates' => [
                [
                    'content' => [
                        'parts' => [
                            [
                                'text' => json_encode([
                                    'overall_band_score' => $score,
                                    'criteria_scores' => [],
                                    'detailed_feedback' => 'Good.',
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
