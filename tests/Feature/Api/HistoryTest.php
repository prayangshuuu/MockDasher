<?php

namespace Tests\Feature\Api;

use App\Models\Test;
use App\Models\TestAttempt;
use App\Models\TestSet;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class HistoryTest extends TestCase
{
    use RefreshDatabase;

    // ── Helpers ────────────────────────────────────────────────────────────────

    private function makeAttempt(User $user, bool $completed = false): TestAttempt
    {
        $test = Test::create([
            'book_number' => 1,
            'year'        => 2026,
            'exam_type'   => 'Academic',
            'status'      => 'published',
        ]);
        $testSet = TestSet::create(['test_id' => $test->id, 'set_number' => 1]);

        return TestAttempt::create([
            'user_id'      => $user->id,
            'test_set_id'  => $testSet->id,
            'status'       => $completed ? 'completed' : 'in_progress',
            'started_at'   => now()->subHour(),
            'completed_at' => $completed ? now() : null,
        ]);
    }

    // ── Index ──────────────────────────────────────────────────────────────────

    public function test_index_returns_paginated_history(): void
    {
        $user = User::factory()->create();
        $this->makeAttempt($user, true);
        $this->makeAttempt($user, true);
        Sanctum::actingAs($user);

        $response = $this->getJson('/api/v1/history');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'test_title', 'status', 'overall_band', 'started_at', 'completed_at']],
                'meta' => ['current_page', 'last_page', 'per_page', 'total'],
            ])
            ->assertJsonPath('meta.total', 2);
    }

    public function test_index_does_not_return_other_users_history(): void
    {
        $alice = User::factory()->create();
        $bob   = User::factory()->create();
        $this->makeAttempt($bob, true);

        Sanctum::actingAs($alice);
        $response = $this->getJson('/api/v1/history');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 0);
    }

    public function test_index_requires_authentication(): void
    {
        $response = $this->getJson('/api/v1/history');

        $response->assertStatus(401);
    }

    // ── Show ───────────────────────────────────────────────────────────────────

    public function test_show_returns_attempt_detail(): void
    {
        $user    = User::factory()->create();
        $attempt = $this->makeAttempt($user, true);
        Sanctum::actingAs($user);

        $response = $this->getJson("/api/v1/history/{$attempt->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id', 'test_title', 'status', 'started_at', 'completed_at',
                    'writing', 'speaking', 'listening', 'reading',
                ],
            ])
            ->assertJsonPath('data.id', $attempt->id);
    }

    public function test_show_prevents_idor(): void
    {
        $alice   = User::factory()->create();
        $bob     = User::factory()->create();
        $attempt = $this->makeAttempt($bob, true);

        Sanctum::actingAs($alice);
        $response = $this->getJson("/api/v1/history/{$attempt->id}");

        $response->assertStatus(404);
    }

    public function test_show_returns_404_for_nonexistent_attempt(): void
    {
        Sanctum::actingAs(User::factory()->create());

        $response = $this->getJson('/api/v1/history/99999');

        $response->assertStatus(404);
    }
}
