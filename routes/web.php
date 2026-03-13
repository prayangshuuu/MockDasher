<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return view('dashboard');
    })->name('dashboard');

    // User Test Routes
    Route::get('/tests/attempts/{attempt}/writing', [\App\Http\Controllers\User\WritingTestController::class, 'show'])->name('user.writing.show');
    Route::post('/tests/attempts/{attempt}/writing/autosave', [\App\Http\Controllers\User\WritingTestController::class, 'autosave'])->name('user.writing.autosave');
    Route::post('/tests/attempts/{attempt}/writing/submit', [\App\Http\Controllers\User\WritingTestController::class, 'submit'])->name('user.writing.submit');

    // Admin Routes
    Route::middleware([\App\Http\Middleware\RoleMiddleware::class.':Admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/dashboard', function () {
            // Placeholder: Should ideally fetch collections/tests
            return view('admin.dashboard', ['tests' => \App\Models\Test::all()]);
        })->name('dashboard');

        Route::get('/tests/{test}/writing-tasks/create', [\App\Http\Controllers\Admin\WritingTaskController::class, 'create'])->name('writing-tasks.create');
        Route::post('/tests/{test}/writing-tasks', [\App\Http\Controllers\Admin\WritingTaskController::class, 'store'])->name('writing-tasks.store');
    });
});
