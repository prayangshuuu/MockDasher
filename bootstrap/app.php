<?php

use App\Http\Middleware\LogHttpRequest;
use App\Jobs\QueueHeartbeat;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Sentry\Laravel\Integration;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
        $middleware->append(LogHttpRequest::class);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Route all unhandled exceptions to Sentry (no-op when DSN is unset)
        $exceptions->reportable(function (Throwable $e) {
            Integration::captureUnhandledException($e);
        });

        $isApiRequest = fn (Request $r): bool =>
            $r->is('api/*') || $r->expectsJson();

        // 401 — unauthenticated
        $exceptions->render(function (AuthenticationException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                return response()->json([
                    'error'   => 'unauthenticated',
                    'message' => 'Authentication required. Provide a valid Bearer token.',
                ], 401);
            }
        });

        // 429 — rate limit exceeded
        $exceptions->render(function (ThrottleRequestsException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                $retryAfter = (int) ($e->getHeaders()['Retry-After'] ?? 60);

                return response()->json([
                    'error'       => 'too_many_requests',
                    'message'     => 'Rate limit exceeded. Please slow down.',
                    'retry_after' => $retryAfter,
                ], 429)->withHeaders(['Retry-After' => $retryAfter]);
            }
        });

        // 404 — model not found (route model binding)
        $exceptions->render(function (ModelNotFoundException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                return response()->json([
                    'error'   => 'not_found',
                    'message' => 'The requested resource does not exist.',
                ], 404);
            }
        });

        // 404 — route not found
        $exceptions->render(function (NotFoundHttpException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                return response()->json([
                    'error'   => 'not_found',
                    'message' => 'The requested endpoint does not exist.',
                ], 404);
            }
        });

        // 422 — validation errors (consistent envelope)
        $exceptions->render(function (ValidationException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                return response()->json([
                    'error'   => 'validation_failed',
                    'message' => 'The given data was invalid.',
                    'errors'  => $e->errors(),
                ], 422);
            }
        });

        // Generic HTTP exceptions (403, 405, etc.)
        $exceptions->render(function (HttpException $e, Request $request) use ($isApiRequest) {
            if ($isApiRequest($request)) {
                return response()->json([
                    'error'   => 'http_error',
                    'message' => $e->getMessage() ?: 'An HTTP error occurred.',
                ], $e->getStatusCode());
            }
        });

    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->job(new QueueHeartbeat)->everyMinute();
    })
    ->create();
