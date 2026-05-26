<?php

use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\FailedJobController;
use App\Http\Controllers\ApiDocsController;
use App\Http\Controllers\DocsController;
use App\Http\Controllers\Admin\AiContentController;
use App\Http\Controllers\Admin\ListeningSectionController;
use App\Http\Controllers\Admin\QuestionController;
use App\Http\Controllers\Admin\ReadingPassageController;
use App\Http\Controllers\Admin\ReadingQuestionGroupController;
use App\Http\Controllers\Admin\ResultController;
use App\Http\Controllers\Admin\SpeakingQuestionController;
use App\Http\Controllers\Admin\TestSetController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\WritingTaskController;
use App\Http\Controllers\User\ListeningTestController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\ReadingTestController;
use App\Http\Controllers\User\SpeakingTestController;
use App\Http\Controllers\User\TestController;
use App\Http\Controllers\User\TestHistoryController;
use App\Http\Controllers\User\UserDashboardController;
use App\Http\Controllers\User\WritingTestController;
use App\Http\Middleware\RoleMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }

    return view('welcome');
});

Route::get('/docs', [DocsController::class, 'index'])->name('docs');
Route::get('/api-docs', [ApiDocsController::class, 'index'])->name('api-docs');
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [UserDashboardController::class, 'index'])->name('dashboard');

    // User Test Routes
    Route::get('/tests', [TestController::class, 'index'])->name('user.tests.index');
    Route::match(['get', 'post'], '/tests/{test}/start', [TestController::class, 'start'])->name('user.tests.start');
    Route::post('/tests/attempts/{attempt}/finish', [TestController::class, 'finish'])->name('user.tests.finish');
    Route::post('/tests/attempts/{attempt}/violation', [TestController::class, 'recordViolation'])->name('user.tests.violation');

    Route::get('/tests/attempts/{attempt}/writing', [WritingTestController::class, 'show'])->name('user.writing.show');
    Route::post('/tests/attempts/{attempt}/writing/autosave', [WritingTestController::class, 'autosave'])->name('user.writing.autosave');
    Route::post('/tests/attempts/{attempt}/writing/tasks/{task}/submit', [WritingTestController::class, 'submitTask'])->name('user.writing.submitTask');
    Route::post('/tests/attempts/{attempt}/writing/submit', [WritingTestController::class, 'submit'])->name('user.writing.submit');
    Route::get('/tests/attempts/{attempt}/writing/result', [WritingTestController::class, 'result'])->name('user.writing.result');

    Route::get('/tests/attempts/{attempt}/speaking', [SpeakingTestController::class, 'show'])->name('user.speaking.show');
    Route::post('/tests/attempts/{attempt}/speaking/upload-audio', [SpeakingTestController::class, 'uploadAudio'])->name('user.speaking.uploadAudio');
    Route::post('/tests/attempts/{attempt}/speaking/questions/{question}/submit', [SpeakingTestController::class, 'submitQuestion'])->name('user.speaking.submitQuestion');
    Route::post('/tests/attempts/{attempt}/speaking/submit', [SpeakingTestController::class, 'submit'])->name('user.speaking.submit');
    Route::get('/tests/attempts/{attempt}/speaking/result', [SpeakingTestController::class, 'result'])->name('user.speaking.result');

    Route::get('/tests/attempts/{attempt}/listening', [ListeningTestController::class, 'show'])->name('user.listening.show');
    Route::post('/tests/attempts/{attempt}/listening/autosave', [ListeningTestController::class, 'autosave'])->name('user.listening.autosave');
    Route::post('/tests/attempts/{attempt}/listening/complete-section', [ListeningTestController::class, 'completeSection'])->name('user.listening.completeSection');
    Route::post('/tests/attempts/{attempt}/listening/submit', [ListeningTestController::class, 'submit'])->name('user.listening.submit');
    Route::get('/tests/attempts/{attempt}/listening/result', [ListeningTestController::class, 'result'])->name('user.listening.result');

    Route::get('/tests/attempts/{attempt}/reading', [ReadingTestController::class, 'show'])->name('user.reading.show');
    Route::post('/tests/attempts/{attempt}/reading/autosave', [ReadingTestController::class, 'autosave'])->name('user.reading.autosave');
    Route::post('/tests/attempts/{attempt}/reading/submit', [ReadingTestController::class, 'submit'])->name('user.reading.submit');
    Route::get('/tests/attempts/{attempt}/reading/result', [ReadingTestController::class, 'result'])->name('user.reading.result');

    // Profile & History Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/update', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');
    Route::put('/profile/gemini-key', [ProfileController::class, 'updateGeminiKey'])->name('profile.gemini.update');
    Route::delete('/profile/delete', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::get('/history', [TestHistoryController::class, 'index'])->name('user.history.index');
    Route::get('/history/{attempt}', [TestHistoryController::class, 'show'])->name('user.history.show');
    Route::get('/history/{attempt}/pdf', [TestHistoryController::class, 'exportPdf'])->name('user.history.pdf');

    // Admin Routes
    Route::middleware([RoleMiddleware::class.':Admin'])->prefix('admin')->name('admin.')->group(function () {
        // ── AI Content Generator (AJAX) ──────────────────────────────────────
        Route::post('/ai/generate-content', [AiContentController::class, 'generate'])->name('ai.generate');

        Route::resource('tests', App\Http\Controllers\Admin\TestController::class);

        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
        Route::get('/recent-attempts', [AdminDashboardController::class, 'recentAttempts'])->name('recent-attempts');

        Route::get('/test_sets/{test_set}', [TestSetController::class, 'show'])->name('test_sets.show');
        Route::post('/tests/{test}/test_sets', [TestSetController::class, 'store'])->name('test_sets.store');
        Route::delete('/test_sets/{test_set}', [TestSetController::class, 'destroy'])->name('test_sets.destroy');

        Route::get('/test_sets/{test_set}/writing-tasks/create', [WritingTaskController::class, 'create'])->name('writing-tasks.create');
        Route::post('/test_sets/{test_set}/writing-tasks', [WritingTaskController::class, 'store'])->name('writing-tasks.store');
        Route::get('/writing-tasks/{writing_task}/edit', [WritingTaskController::class, 'edit'])->name('writing-tasks.edit');
        Route::put('/writing-tasks/{writing_task}', [WritingTaskController::class, 'update'])->name('writing-tasks.update');
        Route::delete('/writing-tasks/{writing_task}', [WritingTaskController::class, 'destroy'])->name('writing-tasks.destroy');

        Route::get('/test_sets/{test_set}/speaking-questions/create', [SpeakingQuestionController::class, 'create'])->name('speaking-questions.create');
        Route::post('/test_sets/{test_set}/speaking-questions', [SpeakingQuestionController::class, 'store'])->name('speaking-questions.store');
        Route::get('/speaking-questions/{speaking_question}/edit', [SpeakingQuestionController::class, 'edit'])->name('speaking-questions.edit');
        Route::put('/speaking-questions/{speaking_question}', [SpeakingQuestionController::class, 'update'])->name('speaking-questions.update');
        Route::delete('/speaking-questions/{speaking_question}', [SpeakingQuestionController::class, 'destroy'])->name('speaking-questions.destroy');

        Route::get('/test_sets/{test_set}/listening-sections/create', [ListeningSectionController::class, 'create'])->name('listening-sections.create');
        Route::post('/test_sets/{test_set}/listening-sections', [ListeningSectionController::class, 'store'])->name('listening-sections.store');
        Route::get('/listening-sections/{listening_section}/edit', [ListeningSectionController::class, 'edit'])->name('listening-sections.edit');
        Route::put('/listening-sections/{listening_section}', [ListeningSectionController::class, 'update'])->name('listening-sections.update');
        Route::delete('/listening-sections/{listening_section}', [ListeningSectionController::class, 'destroy'])->name('listening-sections.destroy');

        Route::get('/test_sets/{test_set}/reading-passages/create', [ReadingPassageController::class, 'create'])->name('reading-passages.create');
        Route::post('/test_sets/{test_set}/reading-passages', [ReadingPassageController::class, 'store'])->name('reading-passages.store');
        Route::get('/reading-passages/{reading_passage}/edit', [ReadingPassageController::class, 'edit'])->name('reading-passages.edit');
        Route::put('/reading-passages/{reading_passage}', [ReadingPassageController::class, 'update'])->name('reading-passages.update');
        Route::delete('/reading-passages/{reading_passage}', [ReadingPassageController::class, 'destroy'])->name('reading-passages.destroy');

        Route::get('/reading-passages/{passageId}/question-groups/create', [ReadingQuestionGroupController::class, 'create'])->name('reading-question-groups.create');
        Route::post('/reading-passages/{passageId}/question-groups', [ReadingQuestionGroupController::class, 'store'])->name('reading-question-groups.store');
        Route::get('/reading-question-groups/{group}/edit', [ReadingQuestionGroupController::class, 'edit'])->name('reading-question-groups.edit');
        Route::put('/reading-question-groups/{group}', [ReadingQuestionGroupController::class, 'update'])->name('reading-question-groups.update');
        Route::delete('/reading-question-groups/{group}', [ReadingQuestionGroupController::class, 'destroy'])->name('reading-question-groups.destroy');

        Route::get('/{type}/{id}/questions/create', [QuestionController::class, 'create'])->name('questions.create');
        Route::post('/{type}/{id}/questions', [QuestionController::class, 'store'])->name('questions.store');
        Route::get('/questions/{question}/edit', [QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('/questions/{question}', [QuestionController::class, 'update'])->name('questions.update');
        Route::delete('/questions/{question}', [QuestionController::class, 'destroy'])->name('questions.destroy');

        Route::resource('users', UserController::class)->except(['show']);
        Route::get('results', [ResultController::class, 'index'])->name('results.index');
        Route::get('results/{result}', [ResultController::class, 'show'])->name('results.show');
        Route::get('results/{result}/pdf', [ResultController::class, 'exportPdf'])->name('results.pdf');

        // Failed Jobs / Queue Dashboard
        Route::get('failed-jobs', [FailedJobController::class, 'index'])->name('failed-jobs.index');
        Route::post('failed-jobs/{uuid}/retry', [FailedJobController::class, 'retry'])->name('failed-jobs.retry');
        Route::post('failed-jobs/retry-all', [FailedJobController::class, 'retryAll'])->name('failed-jobs.retry-all');
        Route::delete('failed-jobs/{uuid}', [FailedJobController::class, 'destroy'])->name('failed-jobs.destroy');
        Route::delete('failed-jobs', [FailedJobController::class, 'destroyAll'])->name('failed-jobs.destroy-all');
    });
});
