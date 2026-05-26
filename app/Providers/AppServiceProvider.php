<?php

namespace App\Providers;

use App\Models\TestAttempt;
use App\Policies\TestAttemptPolicy;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Gate::policy(TestAttempt::class, TestAttemptPolicy::class);

        $this->configureRateLimiting();
    }

    private function configureRateLimiting(): void
    {
        // ── POST /api/v1/auth/login ────────────────────────────────────────────
        // Two layers: per-email+IP (brute-force) and per-IP (enumeration).
        RateLimiter::for('api-login', function (Request $request) {
            $emailIpKey = strtolower((string) $request->input('email', '')).'|'.$request->ip();

            return [
                Limit::perMinute(5)->by($emailIpKey)->response(function () {
                    return response()->json([
                        'error'   => 'too_many_requests',
                        'message' => 'Too many login attempts. Please wait 60 seconds.',
                    ], 429);
                }),
                Limit::perMinute(20)->by('login-ip:'.$request->ip())->response(function () {
                    return response()->json([
                        'error'   => 'too_many_requests',
                        'message' => 'Too many requests from your IP. Please wait.',
                    ], 429);
                }),
            ];
        });

        // ── General authenticated API ──────────────────────────────────────────
        // 120 requests per minute per authenticated user; 30 per minute per IP
        // for unauthenticated requests (should not occur on protected routes).
        RateLimiter::for('api', function (Request $request) {
            return $request->user()
                ? Limit::perMinute(120)->by('user:'.$request->user()->id)
                : Limit::perMinute(30)->by('ip:'.$request->ip());
        });

        // ── AI evaluation submit (Writing / Speaking) ──────────────────────────
        // Gemini API calls are expensive; 6 per minute per user prevents abuse
        // while still allowing a retry within the same exam session.
        RateLimiter::for('api-ai-eval', function (Request $request) {
            return Limit::perMinute(6)->by('ai:'.$request->user()?->id)->response(function () {
                return response()->json([
                    'error'   => 'too_many_requests',
                    'message' => 'Too many evaluation requests. Please wait before resubmitting.',
                ], 429);
            });
        });

        // ── Audio upload (Speaking) ────────────────────────────────────────────
        // Large file uploads; 10 per minute per user is generous for exam use.
        RateLimiter::for('api-upload', function (Request $request) {
            return Limit::perMinute(10)->by('upload:'.$request->user()?->id)->response(function () {
                return response()->json([
                    'error'   => 'too_many_requests',
                    'message' => 'Too many upload requests. Please slow down.',
                ], 429);
            });
        });

        // ── Autosave (Writing / Listening / Reading) ───────────────────────────
        // Autosave fires frequently; 60 per minute gives 1/s which is ample.
        RateLimiter::for('api-autosave', function (Request $request) {
            return Limit::perMinute(60)->by('save:'.$request->user()?->id);
        });
    }
}
