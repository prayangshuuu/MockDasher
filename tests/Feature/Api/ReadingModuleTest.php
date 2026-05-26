<?php

namespace Tests\Feature\Api;

use App\Models\ReadingAttempt;
use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ReadingModuleTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function makeAttempt(User $user): TestAttempt
    {
        $test = Test::create([
            'book_number' => 1,
            'year'        => 2026,
            'exam_type'   => 'Academic',
            'status'      => 'published',
        ]);
        $testSet = TestSet::create(['test_id' => $test->id, 'set_number' => 1]);

        return TestAttempt::create([
            'user_id'     => $user->id,
            'test_set_id' => $testSet->id,
            'status'      => 'in_progress',
            'started_at'  => now(),
        ]);
    }

    private function makeReadingAttempt(TestAttempt $attempt): ReadingAttempt
    {
        return ReadingAttempt::create([
            'test_attempt_id' => $attempt->id,
            'user_id'         => $attempt->user_id,
            'test_set_id'     => $attempt->test_set_id,
            'status'          => 'in_progress',
            'started_at'      => now(),
        ]);
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function test_show_creates_reading_attempt_on_first_access(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/reading");

        $response->assertStatus(200)
            ->assertJsonStructure(['attempt_id', 'reading_attempt_id', 'remaining_seconds', 'passages']);

        $this->assertDatabaseHas('reading_attempts', ['test_attempt_id' => $attempt->id]);
        $this->assertSame('reading', $attempt->fresh()->status);
    }

    public function test_show_returns_403_for_other_user(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/reading");

        $response->assertStatus(403);
    }

    public function test_show_returns_409_if_already_completed(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        $ra      = $this->makeReadingAttempt($attempt);
        $ra->update(['status' => 'completed', 'completed_at' => now()]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/reading");

        $response->assertStatus(409)->assertJson(['error' => 'already_completed']);
    }

    // ── Autosave ───────────────────────────────────────────────────────────────

    public function test_autosave_returns_404_when_not_started(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/reading/autosave", [
            'answers' => [],
        ]);

        $response->assertStatus(404)->assertJson(['error' => 'not_started']);
    }

    public function test_autosave_returns_success(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        $this->makeReadingAttempt($attempt);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/reading/autosave", [
            'answers' => [],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);
    }

    // ── Submit ─────────────────────────────────────────────────────────────────

    public function test_submit_completes_reading_module(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        $this->makeReadingAttempt($attempt);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/reading/submit", [
            'answers' => [],
        ]);

        $response->assertStatus(200)->assertJson(['success' => true]);

        $this->assertDatabaseHas('reading_attempts', [
            'test_attempt_id' => $attempt->id,
            'status'          => 'completed',
        ]);
    }

    public function test_submit_returns_404_when_not_started(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/reading/submit", [
            'answers' => [],
        ]);

        $response->assertStatus(404);
    }

    public function test_submit_rejects_duplicate(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        $ra      = $this->makeReadingAttempt($attempt);
        $ra->update(['status' => 'completed', 'completed_at' => now()]);
        Sanctum::actingAs($user);

        $response = $this->postJson("/api/v1/attempts/{$attempt->id}/reading/submit", [
            'answers' => [],
        ]);

        $response->assertStatus(403);
    }

    // ── Result ─────────────────────────────────────────────────────────────────

    public function test_result_returns_409_when_not_completed(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        $this->makeReadingAttempt($attempt);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/reading/result");

        $response->assertStatus(409)->assertJson(['error' => 'not_completed']);
    }

    public function test_result_returns_scores_after_completion(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        $ra      = $this->makeReadingAttempt($attempt);
        $ra->update(['status' => 'completed', 'completed_at' => now(), 'band_score' => 7.0, 'total_correct' => 32]);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/reading/result");

        $response->assertStatus(200)
            ->assertJson(['score' => 32]);
        $this->assertEquals(7.0, $response->json('band_score'));
    }

    public function test_result_returns_403_for_other_user(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user);
        $ra      = $this->makeReadingAttempt($attempt);
        $ra->update(['status' => 'completed', 'completed_at' => now()]);

        Sanctum::actingAs(User::factory()->create());
        $response = $this->getJson("/api/v1/attempts/{$attempt->id}/reading/result");

        $response->assertStatus(403);
    }
}
