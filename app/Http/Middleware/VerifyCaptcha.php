<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class VerifyCaptcha
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('env') != 'production') {
            return $next($request);
        }

        $secret = config('app.recaptcha.secret');
        $response = $request->get('g-recaptcha-response');
        $result = Http::get('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => $secret,
            'response' => $response,
        ]);

        if (! $result->json('success') || $result->json('score') < 0.7) {
            return back()->withErrors(['recaptcha' => 'Failed reCAPTCHA verification']);
        }

        return $next($request);
    }
}
