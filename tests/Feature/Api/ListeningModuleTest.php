<?php

namespace Tests\Feature\Api;

use App\Models\ListeningAttempt;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ListeningModuleTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function makeAttempt(User $user): array
    {
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

        return [$attempt, $testSet];
    }

    private function makeListeningAttempt(TestAttempt $attempt): ListeningAttempt
    {
        return ListeningAttempt::create([
            'test_attempt_id' => $attempt->id,
            'user_id'         => $attempt->user_id,
            'test_set_id'     => $attempt->test_set_id,
            'status'          => 'in_progress',
            'current_section' => 1,
            'started_at'      => now(),
        ]);
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function test_show_creates_listening_attempt_when_none_exists(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/listening");

        $response->assertStatus(200)
            ->assertJsonStructure(['attempt_id', 'listening_attempt_id', 'status', 'sections']);

        $this->assertDatabaseHas('listening_attempts', ['test_attempt_id' => $attempt->id]);
    }

    public function test_show_returns_403_for_other_user(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/listening");

        $response->assertStatus(403);
    }

    public function test_show_returns_409_for_completed_listening_attempt(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        $la = $this->makeListeningAttempt($attempt);
        $la->update(['status' => 'completed', 'completed_at' => now()]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/listening");

        $response->assertStatus(409)->assertJson(['error' => 'already_completed']);
    }

    // ── Autosave ───────────────────────────────────────────────────────────────

    public function test_autosave_returns_404_when_not_started(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/listening/autosave", [
            'answers' => [],
        ]);

        $response->assertStatus(404)->assertJson(['error' => 'not_started']);
    }

    public function test_autosave_returns_success(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        $this->makeListeningAttempt($attempt);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/listening/autosave", [
            'answers' => [],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    // ── Complete section ───────────────────────────────────────────────────────

    public function test_complete_section_advances_current_section(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        $this->makeListeningAttempt($attempt);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/listening/complete-section", [
            'section' => 1,
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'next', 'next_section' => 2]);

        $this->assertDatabaseHas('listening_attempts', [
            'test_attempt_id' => $attempt->id,
            'current_section' => 2,
        ]);
    }

    public function test_complete_section_4_enters_transfer_phase(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        $la = $this->makeListeningAttempt($attempt);
        $la->update(['current_section' => 4]);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/listening/complete-section", [
            'section' => 4,
        ]);

        $response->assertStatus(200)
            ->assertJson(['status' => 'transfer']);

        $this->assertDatabaseHas('listening_attempts', [
            'test_attempt_id' => $attempt->id,
            'status'          => 'transfer',
        ]);
    }

    public function test_complete_section_validates_section_number(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        $this->makeListeningAttempt($attempt);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/listening/complete-section", [
            'section' => 5,
        ]);

        $response->assertStatus(422);
    }

    // ── Submit ─────────────────────────────────────────────────────────────────

    public function test_submit_completes_listening_module(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        $this->makeListeningAttempt($attempt);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/listening/submit", [
            'answers' => [],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('listening_attempts', [
            'test_attempt_id' => $attempt->id,
            'status'          => 'completed',
        ]);
    }

    public function test_submit_returns_404_when_not_started(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/listening/submit", [
            'answers' => [],
        ]);

        $response->assertStatus(404);
    }

    // ── Result ─────────────────────────────────────────────────────────────────

    public function test_result_returns_409_when_not_completed(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        $this->makeListeningAttempt($attempt);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/listening/result");

        $response->assertStatus(409)->assertJson(['error' => 'not_completed']);
    }

    public function test_result_returns_scores_after_completion(): void
    {
        $user = User::factory()->create();
        [$attempt] = $this->makeAttempt($user);
        $la = $this->makeListeningAttempt($attempt);
        $la->update(['status' => 'completed', 'completed_at' => now(), 'band_score' => 6.5, 'total_correct' => 26]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/listening/result");

        $response->assertStatus(200)
            ->assertJsonPath('band_score', 6.5)
            ->assertJsonPath('score', 26);
    }
}
