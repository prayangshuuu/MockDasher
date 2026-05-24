<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (! auth()->check()) {
            return redirect('login');
        }

        // Issue 11: guard against an empty role string and use a direct DB query
        // so soft-deleted roles are excluded and the full collection is never loaded.
        if (empty($role) || ! $request->user()->roles()->where('name', $role)->exists()) {
            abort(403, 'Unauthorized Access - '.$role.' Role Required.');
        }

        return $next($request);
    }
}
