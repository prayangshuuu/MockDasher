<?php

namespace Tests\Feature\User;

use App\Models\AiWritingEvaluation;
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
                'user_id'         => $user->id,
                'test_attempt_id' => $attempt->id,
                'writing_task_id' => $task->id,
                'answer_text'     => 'A complete answer.',
                'word_count'      => 3,
                'submitted_at'    => now(),
            ]);
        }

        $response = $this->actingAs($user)->post(route('user.writing.submit', $attempt));

        $response->assertRedirect(route('user.tests.start', $attempt->testSet->test_id));

        $attempt->refresh();
        $this->assertNull($attempt->completed_at);
        $this->assertSame('in_progress', $attempt->status);
        $this->assertSame(6.5, AiWritingEvaluation::where('test_attempt_id', $attempt->id)->value('band_score'));
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
        $this->assertDatabaseMissing('ai_writing_evaluations', [
            'test_attempt_id' => $attempt->id,
            'band_score'      => 0.0,
        ]);
    }

    private function writingAttempt(): array
    {
        $user = User::factory()->create();
        $test = Test::create([
            'book_number' => 19,
            'year'        => 2026,
            'exam_type'   => 'Academic',
            'status'      => 'published',
        ]);
        $testSet = TestSet::create(['test_id' => $test->id, 'set_number' => 1]);
        $attempt = TestAttempt::create([
            'user_id'            => $user->id,
            'test_set_id'        => $testSet->id,
            'status'             => 'writing',
            'started_at'         => now(),
            'writing_started_at' => now(),
        ]);

        $tasks = collect([1, 2])->map(fn (int $taskNumber) => WritingTask::create([
            'test_set_id'          => $testSet->id,
            'task_number'          => $taskNumber,
            'task_title'           => "Task {$taskNumber}",
            'task_prompt'          => "Prompt {$taskNumber}",
            'minimum_word_count'   => $taskNumber === 1 ? 150 : 250,
        ]));

        return [$user, $attempt, $tasks];
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
                                    'criteria_scores'    => [],
                                    'detailed_feedback'  => 'Good.',
                                ]),
                            ],
                        ],
                    ],
                ],
            ],
        ];
    }
}
