<?php

use App\Http\Controllers\Api\AttemptApiController;
use App\Http\Controllers\Api\AuthController;
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
| Base: /api/v1
| Auth: Bearer token via Laravel Sanctum (obtain at POST /api/v1/auth/login)
| Content-Type: application/json
*/

// Root: machine-readable index
Route::get('/', function () {
    return response()->json([
        'name'     => 'MockDasher API',
        'version'  => 'v1',
        'base_url' => url('/api/v1'),
        'docs'     => url('/api-docs'),
    ]);
});

Route::prefix('v1')->group(function () {

    // ── Auth (public) ─────────────────────────────────────────────────────────
    Route::post('auth/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {

        // ── Auth ─────────────────────────────────────────────────────────────
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);
        Route::post('auth/logout-all', [AuthController::class, 'logoutAll']);

        // ── Profile ───────────────────────────────────────────────────────────
        Route::get('profile', [ProfileApiController::class, 'show']);
        Route::put('profile', [ProfileApiController::class, 'update']);
        Route::put('profile/password', [ProfileApiController::class, 'updatePassword']);
        Route::put('profile/gemini-key', [ProfileApiController::class, 'updateGeminiKey']);
        Route::delete('profile', [ProfileApiController::class, 'destroy']);

        // ── Tests ─────────────────────────────────────────────────────────────
        Route::get('tests', [TestApiController::class, 'index']);
        Route::get('tests/{id}', [TestApiController::class, 'show']);

        // ── Attempts ──────────────────────────────────────────────────────────
        Route::get('attempts', [AttemptApiController::class, 'index']);
        Route::post('attempts', [AttemptApiController::class, 'start']);
        Route::get('attempts/{id}', [AttemptApiController::class, 'show']);
        Route::get('attempts/{id}/status', [AttemptApiController::class, 'evaluationStatus']);
        Route::post('attempts/{id}/finish', [AttemptApiController::class, 'finish']);
        Route::post('attempts/{id}/violation', [AttemptApiController::class, 'recordViolation']);

        // ── Writing module ────────────────────────────────────────────────────
        Route::get('attempts/{attempt}/writing', [WritingApiController::class, 'show']);
        Route::post('attempts/{attempt}/writing/autosave', [WritingApiController::class, 'autosave']);
        Route::post('attempts/{attempt}/writing/tasks/{task}/submit', [WritingApiController::class, 'submitTask']);
        Route::post('attempts/{attempt}/writing/submit', [WritingApiController::class, 'submit']);
        Route::get('attempts/{attempt}/writing/result', [WritingApiController::class, 'result']);

        // ── Speaking module ───────────────────────────────────────────────────
        Route::get('attempts/{attempt}/speaking', [SpeakingApiController::class, 'show']);
        Route::post('attempts/{attempt}/speaking/upload', [SpeakingApiController::class, 'uploadAudio']);
        Route::post('attempts/{attempt}/speaking/questions/{question}/submit', [SpeakingApiController::class, 'submitQuestion']);
        Route::post('attempts/{attempt}/speaking/submit', [SpeakingApiController::class, 'submit']);
        Route::get('attempts/{attempt}/speaking/result', [SpeakingApiController::class, 'result']);

        // ── Listening module ──────────────────────────────────────────────────
        Route::get('attempts/{attempt}/listening', [ListeningApiController::class, 'show']);
        Route::post('attempts/{attempt}/listening/autosave', [ListeningApiController::class, 'autosave']);
        Route::post('attempts/{attempt}/listening/complete-section', [ListeningApiController::class, 'completeSection']);
        Route::post('attempts/{attempt}/listening/submit', [ListeningApiController::class, 'submit']);
        Route::get('attempts/{attempt}/listening/result', [ListeningApiController::class, 'result']);

        // ── Reading module ────────────────────────────────────────────────────
        Route::get('attempts/{attempt}/reading', [ReadingApiController::class, 'show']);
        Route::post('attempts/{attempt}/reading/autosave', [ReadingApiController::class, 'autosave']);
        Route::post('attempts/{attempt}/reading/submit', [ReadingApiController::class, 'submit']);
        Route::get('attempts/{attempt}/reading/result', [ReadingApiController::class, 'result']);

        // ── History ───────────────────────────────────────────────────────────
        Route::get('history', [HistoryApiController::class, 'index']);
        Route::get('history/{id}', [HistoryApiController::class, 'show']);
    });
});
