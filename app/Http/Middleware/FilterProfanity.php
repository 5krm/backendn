<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FilterProfanity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->isMethod('post') && $request->has('content')) {
            $content = strtolower($request->input('content'));
            $badWords = ['badword1', 'badword2', 'profanity', 'fuck', 'shit']; // typical bad words
            
            foreach ($badWords as $word) {
                if (str_contains($content, $word)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your content contains inappropriate language.'
                    ], 400);
                }
            }
        }

        return $next($request);
    }
}
