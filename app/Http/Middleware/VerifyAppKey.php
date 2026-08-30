<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAppKey
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $api_key = config('app.app_key');
        $isValid = $request->header('X-APP-KEY') === $api_key;

        return $isValid ? $next($request) : response('Access Denied!', 403);
    }
}
