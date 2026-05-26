<?php

namespace Tests\Feature\Api;

use App\Models\AiSpeakingEvaluation;
use App\Models\AiWritingEvaluation;
use App\Models\ListeningAttempt;
use App\Models\ReadingAttempt;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AttemptTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function makeTestSet(): TestSet
    {
        $test = Test::create([
            'book_number' => 1,
            'year'        => 2026,
            'exam_type'   => 'Academic',
            'status'      => 'published',
        ]);

        return TestSet::create(['test_id' => $test->id, 'set_number' => 1]);
    }

    private function makeAttempt(User $user, ?TestSet $testSet = null): TestAttempt
    {
        $testSet ??= $this->makeTestSet();

        return TestAttempt::create([
            'user_id'     => $user->id,
            'test_set_id' => $testSet->id,
            'status'      => 'in_progress',
            'started_at'  => now(),
        ]);
    }

    // ── Index ──────────────────────────────────────────────────────────────────

    public function test_index_returns_user_attempts_paginated(): void
    {
        $user = User::factory()->create();
        $this->makeAttempt($user);
        $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/attempts');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'meta' => ['current_page', 'last_page', 'per_page', 'total']])
            ->assertJsonPath('meta.total', 2);
    }

    public function test_index_does_not_return_other_users_attempts(): void
    {
        $alice = User::factory()->create();
        $bob   = User::factory()->create();
        $this->makeAttempt($bob);

        Sanctum::actingAs($alice);
        $response = $this->getJson('/api/v1/attempts');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 0);
    }

    // ── Start ──────────────────────────────────────────────────────────────────

    public function test_start_creates_attempt_by_test_set_id(): void
    {
        $user    = User::factory()->create();
        $testSet = $this->makeTestSet();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/attempts', ['test_set_id' => $testSet->id]);

        $response->assertStatus(201)
            ->assertJsonPath('resumed', false)
            ->assertJsonPath('data.status', 'in_progress');

        $this->assertDatabaseHas('test_attempts', [
            'user_id'     => $user->id,
            'test_set_id' => $testSet->id,
        ]);
    }

    public function test_start_creates_attempt_by_test_id(): void
    {
        $user    = User::factory()->create();
        $testSet = $this->makeTestSet();
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/attempts', ['test_id' => $testSet->test_id]);

        $response->assertStatus(201)
            ->assertJsonPath('resumed', false);
    }

    public function test_start_resumes_existing_incomplete_attempt(): void
    {
        $user    = User::factory()->create();
        $testSet = $this->makeTestSet();
        $attempt = $this->makeAttempt($user, $testSet);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/v1/attempts', ['test_set_id' => $testSet->id]);

        $response->assertStatus(200)
            ->assertJsonPath('resumed', true)
            ->assertJsonPath('data.id', $attempt->id);
    }

    public function test_start_validates_missing_ids(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/v1/attempts', []);

        $response->assertStatus(422);
    }

    public function test_start_rejects_nonexistent_test_set(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->postJson('/api/v1/attempts', ['test_set_id' => 99999]);

        $response->assertStatus(422);
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function test_show_returns_attempt_detail(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}");

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $attempt->id);
    }

    public function test_show_prevents_idor(): void
    {
        $alice   = User::factory()->create();
        $bob     = User::factory()->create();
        $attempt = $this->makeAttempt($bob);

        Sanctum::actingAs($alice);
        $response = $this->getJson("/api/v1/attempts/{$attempt->id}");

        $response->assertStatus(404);
    }

    // ── Finish ─────────────────────────────────────────────────────────────────

    public function test_finish_completes_attempt_and_zero_fills_modules(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/finish");

        $response->assertStatus(200)
            ->assertJson(['success' => true])
            ->assertJsonStructure(['overall_band', 'reading_band', 'listening_band', 'writing_band', 'speaking_band', 'completed_at']);

        $attempt->refresh();
        $this->assertSame('completed', $attempt->status);
        $this->assertNotNull($attempt->completed_at);
        $this->assertDatabaseHas('reading_attempts', ['test_attempt_id' => $attempt->id, 'band_score' => 0.0]);
        $this->assertDatabaseHas('listening_attempts', ['test_attempt_id' => $attempt->id, 'band_score' => 0.0]);
    }

    public function test_finish_returns_409_if_already_completed(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/finish");

        $response->assertStatus(409)
            ->assertJson(['error' => 'already_completed']);
    }

    public function test_finish_prevents_idor(): void
    {
        $alice   = User::factory()->create();
        $bob     = User::factory()->create();
        $attempt = $this->makeAttempt($bob);

        Sanctum::actingAs($alice);
        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/finish");

        $response->assertStatus(404);
    }

    // ── Violations / proctoring ────────────────────────────────────────────────

    public function test_violation_increments_counter(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/violation");

        $response->assertStatus(200)
            ->assertJson(['status' => 'warned', 'violations' => 1, 'remaining' => 2]);
    }

    public function test_violation_terminates_exam_on_third_violation(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        $attempt->update(['proctoring_violations' => 2]);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/violation");

        $response->assertStatus(200)
            ->assertJson(['status' => 'terminated']);

        $this->assertSame('completed', $attempt->fresh()->status);
    }

    public function test_violation_on_completed_attempt_returns_already_completed(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        $attempt->update(['status' => 'completed', 'completed_at' => now()]);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/violation");

        $response->assertStatus(200)
            ->assertJson(['status' => 'already_completed']);
    }

    // ── Evaluation status ──────────────────────────────────────────────────────

    public function test_evaluation_status_returns_not_started_when_no_evaluations(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/status");

        $response->assertStatus(200)
            ->assertJsonPath('writing.status', 'not_started')
            ->assertJsonPath('speaking.status', 'not_started');
    }

    public function test_evaluation_status_reflects_ai_evaluation_rows(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        AiWritingEvaluation::create([
            'test_attempt_id'   => $attempt->id,
            'user_id'           => $user->id,
            'evaluation_status' => 'pending',
        ]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/status");

        $response->assertStatus(200)
            ->assertJsonPath('writing.status', 'pending');
    }
}
