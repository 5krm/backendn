<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTutor
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();
        
        // Allow access if user is either an active tutor or an admin
        if (!$user || (! $user->isAdmin() && ! $user->isTutor())) {
            abort(403, 'Access denied. Only tutors or admins can access this panel.');
        }
        
        return $next($request);
    }
}
