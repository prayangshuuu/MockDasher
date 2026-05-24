<?php

namespace App\Policies;

use App\Models\TestAttempt;
use App\Models\User;

/**
 * Issue 12: Centralised authorization for TestAttempt resources.
 *
 * Register this policy in AppServiceProvider (or AuthServiceProvider) with:
 *   Gate::policy(TestAttempt::class, TestAttemptPolicy::class);
 *
 * Then replace every manual  (int) $attempt->user_id !== (int) auth()->id()
 * check in controllers with:
 *   $this->authorize('interact', $attempt);
 */
class TestAttemptPolicy
{
    /**
     * The attempt owner can view or interact with the attempt.
     */
    public function interact(User $user, TestAttempt $attempt): bool
    {
        return (int) $attempt->user_id === (int) $user->id;
    }

    /**
     * The attempt owner can finish (force-complete) the attempt.
     */
    public function finish(User $user, TestAttempt $attempt): bool
    {
        return (int) $attempt->user_id === (int) $user->id
            && ! $attempt->completed_at;
    }
}
