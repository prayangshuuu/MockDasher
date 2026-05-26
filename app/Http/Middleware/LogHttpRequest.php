<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogHttpRequest
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    public function terminate(Request $request, Response $response): void
    {
        // Skip health check and asset noise
        if ($request->is('up') || $request->is('_debugbar/*')) {
            return;
        }

        $start      = defined('LARAVEL_START') ? \LARAVEL_START : microtime(true);
        $durationMs = (int) ((microtime(true) - $start) * 1000);
        $status     = $response->getStatusCode();

        $context = [
            'method'      => $request->method(),
            'path'        => $request->path(),
            'status'      => $status,
            'duration_ms' => $durationMs,
            'memory_mb'   => round(memory_get_peak_usage(true) / 1_048_576, 1),
            'ip'          => $request->ip(),
            'user_id'     => $request->user()?->id,
            'user_agent'  => $request->userAgent(),
        ];

        // Emit at warning level for slow or error responses so Sentry captures them
        if ($status >= 500) {
            Log::error('http.response', $context);
        } elseif ($status >= 400 || $durationMs > 2000) {
            Log::warning('http.response', $context);
        } else {
            Log::info('http.response', $context);
        }
    }
}
