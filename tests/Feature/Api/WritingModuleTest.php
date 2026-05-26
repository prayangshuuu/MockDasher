<?php

namespace Tests\Feature\Api;

use App\Jobs\EvaluateWritingSubmission;
use App\Models\AiWritingEvaluation;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;
use App\Models\WritingAnswer;
use App\Models\WritingTask;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class WritingModuleTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function makeContext(): array
    {
        $user = User::factory()->create();
        $test = Test::create([
            'book_number' => 1,
            'year'        => 2026,
            'exam_type'   => 'Academic',
            'status'      => 'published',
        ]);
        $testSet = TestSet::create(['test_id' => $test->id, 'set_number' => 1]);
        $attempt = TestAttempt::create([
            'user_id'     => $user->id,
            'test_set_id' => $testSet->id,
            'status'      => 'in_progress',
            'started_at'  => now(),
        ]);
        $task1 = WritingTask::create([
            'test_set_id'        => $testSet->id,
            'task_number'        => 1,
            'task_title'         => 'Task 1',
            'task_prompt'        => 'Describe the chart.',
            'minimum_word_count' => 150,
        ]);
        $task2 = WritingTask::create([
            'test_set_id'        => $testSet->id,
            'task_number'        => 2,
            'task_title'         => 'Task 2',
            'task_prompt'        => 'Write an essay.',
            'minimum_word_count' => 250,
        ]);

        return [$user, $attempt, $testSet, $task1, $task2];
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function test_show_sets_writing_started_at_and_returns_tasks(): void
    {
        [$user, $attempt, , $task1, $task2] = $this->makeContext();
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/writing");

        $response->assertStatus(200)
            ->assertJsonStructure(['attempt_id', 'remaining_seconds', 'tasks'])
            ->assertJsonPath('attempt_id', $attempt->id);

        $this->assertNotNull($attempt->fresh()->writing_started_at);
    }

    public function test_show_returns_403_for_other_users_attempt(): void
    {
        [$user, $attempt] = $this->makeContext();
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/writing");

        $response->assertStatus(403);
    }

    public function test_show_returns_409_if_already_evaluated(): void
    {
        [$user, $attempt] = $this->makeContext();
        AiWritingEvaluation::create([
            'test_attempt_id'   => $attempt->id,
            'user_id'           => $user->id,
            'band_score'        => 7.0,
            'evaluation_status' => 'completed',
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/writing");

        $response->assertStatus(409)
            ->assertJson(['error' => 'already_completed']);
    }

    // ── Autosave ───────────────────────────────────────────────────────────────

    public function test_autosave_stores_answers(): void
    {
        [$user, $attempt, , $task1, $task2] = $this->makeContext();
        $attempt->update(['writing_started_at' => now(), 'status' => 'writing']);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/writing/autosave", [
            'answers' => [$task1->id => 'Draft text for task one.'],
        ]);

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('writing_answers', [
            'test_attempt_id' => $attempt->id,
            'writing_task_id' => $task1->id,
        ]);
    }

    public function test_autosave_rejects_invalid_task_ids(): void
    {
        [$user, $attempt] = $this->makeContext();
        $attempt->update(['writing_started_at' => now(), 'status' => 'writing']);
        Sanctum::actingAs($user);

        // Task ID 99999 does not belong to this test set
        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/writing/autosave", [
            'answers' => [99999 => 'Injected answer.'],
        ]);

        // The endpoint silently skips invalid task IDs and returns success
        $response->assertStatus(200);
        $this->assertDatabaseMissing('writing_answers', ['writing_task_id' => 99999]);
    }

    // ── Submit task ────────────────────────────────────────────────────────────

    public function test_submit_task_marks_answer_as_submitted(): void
    {
        [$user, $attempt, , $task1] = $this->makeContext();
        $attempt->update(['writing_started_at' => now(), 'status' => 'writing']);
        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/attempts/{$attempt->id}/writing/tasks/{$task1->id}/submit",
            ['answer' => 'This is my answer to task 1.']
        );

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('writing_answers', [
            'test_attempt_id' => $attempt->id,
            'writing_task_id' => $task1->id,
        ]);
        $this->assertNotNull(
            WritingAnswer::where(['test_attempt_id' => $attempt->id, 'writing_task_id' => $task1->id])->first()?->submitted_at
        );
    }

    public function test_submit_task_rejects_duplicate_submission(): void
    {
        [$user, $attempt, , $task1] = $this->makeContext();
        $attempt->update(['writing_started_at' => now(), 'status' => 'writing']);
        WritingAnswer::create([
            'user_id'         => $user->id,
            'test_attempt_id' => $attempt->id,
            'writing_task_id' => $task1->id,
            'answer_text'     => 'First submission.',
            'submitted_at'    => now(),
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/attempts/{$attempt->id}/writing/tasks/{$task1->id}/submit",
            ['answer' => 'Second attempt.']
        );

        $response->assertStatus(409);
    }

    public function test_submit_task_rejects_task_from_another_test_set(): void
    {
        [$user, $attempt] = $this->makeContext();
        $attempt->update(['writing_started_at' => now(), 'status' => 'writing']);

        $otherTestSet = TestSet::create(['test_id' => $attempt->testSet->test_id, 'set_number' => 2]);
        $foreignTask  = WritingTask::create([
            'test_set_id'        => $otherTestSet->id,
            'task_number'        => 1,
            'task_title'         => 'Foreign Task',
            'task_prompt'        => 'Prompt.',
            'minimum_word_count' => 150,
        ]);
        Sanctum::actingAs($user);

        $response = $this->postJson(
            "/api/v1/attempts/{$attempt->id}/writing/tasks/{$foreignTask->id}/submit",
            ['answer' => 'Injected answer.']
        );

        $response->assertStatus(403);
    }

    // ── Submit (full module) ───────────────────────────────────────────────────

    public function test_submit_dispatches_evaluation_job(): void
    {
        Queue::fake();
        [$user, $attempt] = $this->makeContext();
        $attempt->update(['writing_started_at' => now(), 'status' => 'writing']);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/writing/submit");

        $response->assertStatus(200)
            ->assertJson(['success' => true, 'evaluation_status' => 'pending']);

        Queue::assertPushed(EvaluateWritingSubmission::class);

        $this->assertDatabaseHas('ai_writing_evaluations', [
            'test_attempt_id'   => $attempt->id,
            'evaluation_status' => 'pending',
        ]);
    }

    public function test_submit_returns_403_on_completed_attempt(): void
    {
        [$user, $attempt] = $this->makeContext();
        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/writing/submit");

        $response->assertStatus(403);
    }

    // ── Result ─────────────────────────────────────────────────────────────────

    public function test_result_returns_evaluation_data(): void
    {
        [$user, $attempt, , $task1] = $this->makeContext();
        AiWritingEvaluation::create([
            'test_attempt_id'   => $attempt->id,
            'user_id'           => $user->id,
            'band_score'        => 6.5,
            'evaluation_status' => 'completed',
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/writing/result");

        $response->assertStatus(200)
            ->assertJsonPath('writing_band', 6.5)
            ->assertJsonPath('evaluation_status', 'completed');
    }

    public function test_result_returns_403_for_other_user(): void
    {
        [$user, $attempt] = $this->makeContext();
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/writing/result");

        $response->assertStatus(403);
    }
}
