<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\User\UserDashboardController::class, 'index'])->name('dashboard');

    // User Test Routes
    Route::post('/tests/{test}/start', [\App\Http\Controllers\User\TestController::class, 'start'])->name('user.tests.start');
    
    Route::get('/tests/attempts/{attempt}/writing', [\App\Http\Controllers\User\WritingTestController::class, 'show'])->name('user.writing.show');
    Route::post('/tests/attempts/{attempt}/writing/autosave', [\App\Http\Controllers\User\WritingTestController::class, 'autosave'])->name('user.writing.autosave');
    Route::post('/tests/attempts/{attempt}/writing/submit', [\App\Http\Controllers\User\WritingTestController::class, 'submit'])->name('user.writing.submit');
    
    Route::get('/tests/attempts/{attempt}/speaking', [\App\Http\Controllers\User\SpeakingTestController::class, 'show'])->name('user.speaking.show');
    Route::post('/tests/attempts/{attempt}/speaking/submit', [\App\Http\Controllers\User\SpeakingTestController::class, 'submit'])->name('user.speaking.submit');

    Route::get('/tests/attempts/{attempt}/listening', [\App\Http\Controllers\User\ListeningTestController::class, 'show'])->name('user.listening.show');
    Route::post('/tests/attempts/{attempt}/listening/autosave', [\App\Http\Controllers\User\ListeningTestController::class, 'autosave'])->name('user.listening.autosave');
    Route::post('/tests/attempts/{attempt}/listening/complete-section', [\App\Http\Controllers\User\ListeningTestController::class, 'completeSection'])->name('user.listening.completeSection');
    Route::post('/tests/attempts/{attempt}/listening/submit', [\App\Http\Controllers\User\ListeningTestController::class, 'submit'])->name('user.listening.submit');

    Route::get('/tests/attempts/{attempt}/reading', [\App\Http\Controllers\User\ReadingTestController::class, 'show'])->name('user.reading.show');
    Route::post('/tests/attempts/{attempt}/reading/autosave', [\App\Http\Controllers\User\ReadingTestController::class, 'autosave'])->name('user.reading.autosave');
    Route::post('/tests/attempts/{attempt}/reading/submit', [\App\Http\Controllers\User\ReadingTestController::class, 'submit'])->name('user.reading.submit');

    // Profile & History Routes
    Route::get('/profile', [\App\Http\Controllers\User\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile/update', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile/delete', [\App\Http\Controllers\User\ProfileController::class, 'destroy'])->name('profile.destroy');
    
    Route::get('/history', [\App\Http\Controllers\User\TestHistoryController::class, 'index'])->name('user.history.index');
    Route::get('/history/{attempt}', [\App\Http\Controllers\User\TestHistoryController::class, 'show'])->name('user.history.show');

    // Admin Routes
    Route::middleware([\App\Http\Middleware\RoleMiddleware::class.':Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('collections', \App\Http\Controllers\Admin\IeltsCollectionController::class);
        
        Route::resource('tests', \App\Http\Controllers\Admin\TestController::class);
        
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/tests/{test}/writing-tasks/create', [\App\Http\Controllers\Admin\WritingTaskController::class, 'create'])->name('writing-tasks.create');
        Route::post('/tests/{test}/writing-tasks', [\App\Http\Controllers\Admin\WritingTaskController::class, 'store'])->name('writing-tasks.store');
        Route::get('/writing-tasks/{writing_task}/edit', [\App\Http\Controllers\Admin\WritingTaskController::class, 'edit'])->name('writing-tasks.edit');
        Route::put('/writing-tasks/{writing_task}', [\App\Http\Controllers\Admin\WritingTaskController::class, 'update'])->name('writing-tasks.update');
        Route::delete('/writing-tasks/{writing_task}', [\App\Http\Controllers\Admin\WritingTaskController::class, 'destroy'])->name('writing-tasks.destroy');

        Route::get('/tests/{test}/speaking-questions/create', [\App\Http\Controllers\Admin\SpeakingQuestionController::class, 'create'])->name('speaking-questions.create');
        Route::post('/tests/{test}/speaking-questions', [\App\Http\Controllers\Admin\SpeakingQuestionController::class, 'store'])->name('speaking-questions.store');
        Route::get('/speaking-questions/{speaking_question}/edit', [\App\Http\Controllers\Admin\SpeakingQuestionController::class, 'edit'])->name('speaking-questions.edit');
        Route::put('/speaking-questions/{speaking_question}', [\App\Http\Controllers\Admin\SpeakingQuestionController::class, 'update'])->name('speaking-questions.update');
        Route::delete('/speaking-questions/{speaking_question}', [\App\Http\Controllers\Admin\SpeakingQuestionController::class, 'destroy'])->name('speaking-questions.destroy');

        Route::get('/tests/{test}/listening-sections/create', [\App\Http\Controllers\Admin\ListeningSectionController::class, 'create'])->name('listening-sections.create');
        Route::post('/tests/{test}/listening-sections', [\App\Http\Controllers\Admin\ListeningSectionController::class, 'store'])->name('listening-sections.store');
        Route::get('/listening-sections/{listening_section}/edit', [\App\Http\Controllers\Admin\ListeningSectionController::class, 'edit'])->name('listening-sections.edit');
        Route::put('/listening-sections/{listening_section}', [\App\Http\Controllers\Admin\ListeningSectionController::class, 'update'])->name('listening-sections.update');
        Route::delete('/listening-sections/{listening_section}', [\App\Http\Controllers\Admin\ListeningSectionController::class, 'destroy'])->name('listening-sections.destroy');

        Route::get('/tests/{test}/reading-passages/create', [\App\Http\Controllers\Admin\ReadingPassageController::class, 'create'])->name('reading-passages.create');
        Route::post('/tests/{test}/reading-passages', [\App\Http\Controllers\Admin\ReadingPassageController::class, 'store'])->name('reading-passages.store');
        Route::get('/reading-passages/{reading_passage}/edit', [\App\Http\Controllers\Admin\ReadingPassageController::class, 'edit'])->name('reading-passages.edit');
        Route::put('/reading-passages/{reading_passage}', [\App\Http\Controllers\Admin\ReadingPassageController::class, 'update'])->name('reading-passages.update');
        Route::delete('/reading-passages/{reading_passage}', [\App\Http\Controllers\Admin\ReadingPassageController::class, 'destroy'])->name('reading-passages.destroy');

        Route::get('/reading-passages/{passageId}/question-groups/create', [\App\Http\Controllers\Admin\ReadingQuestionGroupController::class, 'create'])->name('reading-question-groups.create');
        Route::post('/reading-passages/{passageId}/question-groups', [\App\Http\Controllers\Admin\ReadingQuestionGroupController::class, 'store'])->name('reading-question-groups.store');
        Route::get('/reading-question-groups/{group}/edit', [\App\Http\Controllers\Admin\ReadingQuestionGroupController::class, 'edit'])->name('reading-question-groups.edit');
        Route::put('/reading-question-groups/{group}', [\App\Http\Controllers\Admin\ReadingQuestionGroupController::class, 'update'])->name('reading-question-groups.update');
        Route::delete('/reading-question-groups/{group}', [\App\Http\Controllers\Admin\ReadingQuestionGroupController::class, 'destroy'])->name('reading-question-groups.destroy');

        Route::get('/{type}/{id}/questions/create', [\App\Http\Controllers\Admin\QuestionController::class, 'create'])->name('questions.create');
        Route::post('/{type}/{id}/questions', [\App\Http\Controllers\Admin\QuestionController::class, 'store'])->name('questions.store');
        Route::get('/questions/{question}/edit', [\App\Http\Controllers\Admin\QuestionController::class, 'edit'])->name('questions.edit');
        Route::put('/questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'update'])->name('questions.update');
        Route::delete('/questions/{question}', [\App\Http\Controllers\Admin\QuestionController::class, 'destroy'])->name('questions.destroy');

        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['create', 'store', 'show']);
        
        Route::get('results', [\App\Http\Controllers\Admin\ResultController::class, 'index'])->name('results.index');
        Route::get('results/{result}', [\App\Http\Controllers\Admin\ResultController::class, 'show'])->name('results.show');
    });
});
