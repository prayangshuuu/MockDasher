<?php

use App\Http\Controllers\Api\AttemptApiController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthController;
use App\Http\Controllers\Api\HistoryApiController;
use App\Http\Controllers\Api\ListeningApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\ReadingApiController;
use App\Http\Controllers\Api\SpeakingApiController;
use App\Http\Controllers\Api\TestApiController;
use App\Http\Controllers\Api\WritingApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MockDasher REST API — v1
|--------------------------------------------------------------------------
| Base:          /api/v1
| Auth:          Bearer token via Sanctum  →  POST /api/v1/auth/login
| Content-Type:  application/json
| Rate limits:   see AppServiceProvider::configureRateLimiting()
*/

// Root: machine-readable index (no auth, no throttle)
Route::get('/', fn () => response()->json([
    'name'     => 'MockDasher API',
    'version'  => 'v1',
    'base_url' => url('/api/v1'),
    'docs'     => url('/api-docs'),
]));

Route::prefix('v1')->group(function () {

    // ── Health check — public, no auth ────────────────────────────────────────
    Route::get('health', [HealthController::class, 'check']);

    // ── Auth — public ─────────────────────────────────────────────────────────
    Route::post('auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:api-login');

    // ── Auth — protected ──────────────────────────────────────────────────────
    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function () {

        Route::post('auth/logout',     [AuthController::class, 'logout']);
        Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);
        Route::get('auth/me',          [AuthController::class, 'me']);

        // ── Profile ───────────────────────────────────────────────────────────
        Route::get('profile',                  [ProfileApiController::class, 'show']);
        Route::put('profile',                  [ProfileApiController::class, 'update']);
        Route::put('profile/password',         [ProfileApiController::class, 'updatePassword']);
        Route::put('profile/gemini-key',       [ProfileApiController::class, 'updateGeminiKey']);
        Route::delete('profile',               [ProfileApiController::class, 'destroy']);

        // ── Tests (cached, read-only) ─────────────────────────────────────────
        Route::get('tests',      [TestApiController::class, 'index']);
        Route::get('tests/{id}', [TestApiController::class, 'show']);

        // ── Attempts ──────────────────────────────────────────────────────────
        Route::get('attempts',              [AttemptApiController::class, 'index']);
        Route::post('attempts',             [AttemptApiController::class, 'start']);
        Route::get('attempts/{id}',         [AttemptApiController::class, 'show']);
        Route::get('attempts/{id}/status',  [AttemptApiController::class, 'evaluationStatus']);
        Route::get('attempts/{id}/evaluation-stream', [AttemptApiController::class, 'evaluationStream']);
        Route::post('attempts/{id}/finish', [AttemptApiController::class, 'finish']);
        Route::post('attempts/{id}/violation', [AttemptApiController::class, 'recordViolation']);

        // ── Writing module ────────────────────────────────────────────────────
        Route::get('attempts/{attempt}/writing',        [WritingApiController::class, 'show']);
        Route::get('attempts/{attempt}/writing/result', [WritingApiController::class, 'result']);

        Route::post('attempts/{attempt}/writing/tasks/{task}/submit', [WritingApiController::class, 'submitTask']);

        Route::post('attempts/{attempt}/writing/autosave', [WritingApiController::class, 'autosave'])
            ->middleware('throttle:api-autosave');

        Route::post('attempts/{attempt}/writing/submit', [WritingApiController::class, 'submit'])
            ->middleware('throttle:api-ai-eval');

        // ── Speaking module ───────────────────────────────────────────────────
        Route::get('attempts/{attempt}/speaking',        [SpeakingApiController::class, 'show']);
        Route::get('attempts/{attempt}/speaking/result', [SpeakingApiController::class, 'result']);

        Route::post('attempts/{attempt}/speaking/questions/{question}/submit', [SpeakingApiController::class, 'submitQuestion']);

        Route::post('attempts/{attempt}/speaking/upload', [SpeakingApiController::class, 'uploadAudio'])
            ->middleware('throttle:api-upload');

        Route::post('attempts/{attempt}/speaking/submit', [SpeakingApiController::class, 'submit'])
            ->middleware('throttle:api-ai-eval');

        // ── Listening module ──────────────────────────────────────────────────
        Route::get('attempts/{attempt}/listening',                    [ListeningApiController::class, 'show']);
        Route::get('attempts/{attempt}/listening/result',             [ListeningApiController::class, 'result']);
        Route::post('attempts/{attempt}/listening/complete-section',  [ListeningApiController::class, 'completeSection']);

        Route::post('attempts/{attempt}/listening/autosave', [ListeningApiController::class, 'autosave'])
            ->middleware('throttle:api-autosave');

        Route::post('attempts/{attempt}/listening/submit', [ListeningApiController::class, 'submit']);

        // ── Reading module ────────────────────────────────────────────────────
        Route::get('attempts/{attempt}/reading',        [ReadingApiController::class, 'show']);
        Route::get('attempts/{attempt}/reading/result', [ReadingApiController::class, 'result']);

        Route::post('attempts/{attempt}/reading/autosave', [ReadingApiController::class, 'autosave'])
            ->middleware('throttle:api-autosave');

        Route::post('attempts/{attempt}/reading/submit', [ReadingApiController::class, 'submit']);

        // ── History ───────────────────────────────────────────────────────────
        Route::get('history',      [HistoryApiController::class, 'index']);
        Route::get('history/{id}', [HistoryApiController::class, 'show']);
    });
});
