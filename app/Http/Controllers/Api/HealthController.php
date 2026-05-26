<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class HealthController extends Controller
{
    public function check(): JsonResponse
    {
        $checks  = [];
        $overall = 'healthy';

        // ── Database ──────────────────────────────────────────────────────────
        try {
            $start = microtime(true);
            DB::select('SELECT 1');
            $checks['database'] = [
                'status'     => 'ok',
                'latency_ms' => (int) ((microtime(true) - $start) * 1000),
            ];
        } catch (\Throwable) {
            $checks['database'] = ['status' => 'fail'];
            $overall             = 'unhealthy';
        }

        // ── Cache / Redis ─────────────────────────────────────────────────────
        try {
            $start = microtime(true);
            $key   = 'health:ping:'.uniqid('', true);
            Cache::put($key, 1, 5);
            $hit = Cache::get($key) === 1;
            Cache::forget($key);
            $checks['cache'] = [
                'status'     => $hit ? 'ok' : 'fail',
                'latency_ms' => (int) ((microtime(true) - $start) * 1000),
            ];
            if (! $hit) {
                $overall = 'degraded';
            }
        } catch (\Throwable) {
            $checks['cache'] = ['status' => 'fail'];
            $overall          = $overall === 'unhealthy' ? 'unhealthy' : 'degraded';
        }

        // ── Queue worker ──────────────────────────────────────────────────────
        // QueueHeartbeat job is scheduled every minute and writes this key when
        // processed. If no key exists the worker has never run or just started.
        // If the key is stale (> 120 s) the worker is likely down.
        $heartbeatTs = Cache::get('queue:heartbeat');
        if ($heartbeatTs !== null) {
            $ageSeconds      = max(0, time() - (int) $heartbeatTs);
            $queueStatus     = $ageSeconds <= 120 ? 'ok' : 'stale';
            $checks['queue'] = [
                'status'         => $queueStatus,
                'last_heartbeat' => date('c', (int) $heartbeatTs),
                'age_seconds'    => $ageSeconds,
            ];
            if ($queueStatus === 'stale') {
                $overall = $overall === 'unhealthy' ? 'unhealthy' : 'degraded';
            }
        } else {
            $checks['queue'] = ['status' => 'unknown'];
        }

        return response()->json([
            'status'    => $overall,
            'timestamp' => now()->toIso8601String(),
            'checks'    => $checks,
        ], $overall === 'unhealthy' ? 503 : 200);
    }
}
