<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Gate middleware for tutor-only routes in the mobile API.
 *
 * Checks that the authenticated user has `is_tutor = true`.
 * Register this in bootstrap/app.php withMiddlewareAliases(['can:access-tutor-panel' => ...])
 * OR add it as a named middleware alias.
 */
class EnsureIsTutor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isTutor()) {
            return response()->json([
                'success' => false,
                'message' => 'Access restricted to tutors only.',
            ], 403);
        }

        return $next($request);
    }
}
