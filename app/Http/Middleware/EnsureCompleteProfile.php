<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompleteProfile
{
    public function handle(Request $request, Closure $next): Response
    {  
        $user = $request->user();

        if ($user && !$user->hasCompleteProfile()) {
            if (!$request->routeIs('auth.complete-profile', 'auth.complete-profile.store', 'auth.logout', 'languages.switch')) {
                return redirect()->route('auth.complete-profile');
            }
        }

        return $next($request);
    }
}
