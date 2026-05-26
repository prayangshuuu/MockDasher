<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class HealthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::forget('queue:heartbeat');
    }

    public function test_health_check_is_publicly_accessible(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200);
    }

    public function test_health_check_returns_expected_structure(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'timestamp',
                'checks' => ['database', 'cache', 'queue'],
            ]);
    }

    public function test_health_check_database_and_cache_are_ok(): void
    {
        $response = $this->getJson('/api/v1/health');

        $response->assertJsonPath('checks.database.status', 'ok')
            ->assertJsonPath('checks.cache.status', 'ok');
    }

    public function test_health_check_reports_unknown_queue_when_no_heartbeat(): void
    {
        Cache::forget('queue:heartbeat');

        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJsonPath('checks.queue.status', 'unknown');
    }

    public function test_health_check_reports_ok_queue_with_fresh_heartbeat(): void
    {
        Cache::put('queue:heartbeat', time(), 150);

        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJsonPath('checks.queue.status', 'ok')
            ->assertJsonPath('status', 'healthy');
    }

    public function test_health_check_reports_stale_queue_with_old_heartbeat(): void
    {
        // Heartbeat recorded 130 seconds ago — exceeds the 120 s threshold.
        Cache::put('queue:heartbeat', time() - 130, 3600);

        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(200)
            ->assertJsonPath('checks.queue.status', 'stale')
            ->assertJsonPath('status', 'degraded');
    }

    public function test_health_check_returns_503_when_database_fails(): void
    {
        // Force the DB check to fail by mocking
        DB::shouldReceive('select')->once()->andThrow(new \RuntimeException('DB down'));

        $response = $this->getJson('/api/v1/health');

        $response->assertStatus(503)
            ->assertJsonPath('status', 'unhealthy')
            ->assertJsonPath('checks.database.status', 'fail');
    }
}
