<?php

namespace App\Providers;

use App\Models\TestAttempt;
use App\Policies\TestAttemptPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Issue 12: register the centralised TestAttempt authorization policy.
        // Controllers can now call $this->authorize('interact', $attempt) instead
        // of repeating the manual user_id comparison in every action.
        Gate::policy(TestAttempt::class, TestAttemptPolicy::class);
    }
}
