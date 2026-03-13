<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\User\UserDashboardController::class, 'index'])->name('dashboard');

    // User Test Routes
    Route::post('/tests/{test}/start', [\App\Http\Controllers\User\TestController::class, 'start'])->name('user.tests.start');
    
    Route::get('/tests/attempts/{attempt}/writing', [\App\Http\Controllers\User\WritingTestController::class, 'show'])->name('user.writing.show');
    Route::post('/tests/attempts/{attempt}/writing/autosave', [\App\Http\Controllers\User\WritingTestController::class, 'autosave'])->name('user.writing.autosave');
    Route::post('/tests/attempts/{attempt}/writing/submit', [\App\Http\Controllers\User\WritingTestController::class, 'submit'])->name('user.writing.submit');

    // Admin Routes
    Route::middleware([\App\Http\Middleware\RoleMiddleware::class.':Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/collections', [\App\Http\Controllers\Admin\IeltsCollectionController::class, 'index'])->name('collections.index');
        Route::get('/collections/create', [\App\Http\Controllers\Admin\IeltsCollectionController::class, 'create'])->name('collections.create');
        Route::post('/collections', [\App\Http\Controllers\Admin\IeltsCollectionController::class, 'store'])->name('collections.store');

        Route::get('/collections/{collection}/tests/create', [\App\Http\Controllers\Admin\TestController::class, 'create'])->name('tests.create');
        Route::post('/collections/{collection}/tests', [\App\Http\Controllers\Admin\TestController::class, 'store'])->name('tests.store');
        
        Route::get('/dashboard', [\App\Http\Controllers\Admin\AdminDashboardController::class, 'index'])->name('dashboard');

        Route::get('/tests/{test}/writing-tasks/create', [\App\Http\Controllers\Admin\WritingTaskController::class, 'create'])->name('writing-tasks.create');
        Route::post('/tests/{test}/writing-tasks', [\App\Http\Controllers\Admin\WritingTaskController::class, 'store'])->name('writing-tasks.store');
    });
});
