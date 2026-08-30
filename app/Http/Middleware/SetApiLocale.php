<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Resolves the request locale for the stateless mobile/JSON API.
 *
 * The session-based {@see SetLocale} middleware is useless for token APIs, so
 * the locale is taken from (in order of priority):
 *
 *   1. `X-Locale: ar`            — explicit header sent by the Flutter app
 *   2. `?locale=ar`              — query-string override (handy for debugging)
 *   3. `Accept-Language: ar-SA`  — standard HTTP negotiation
 *   4. the authenticated user's display-language preference
 *   5. `config('app.locale')`    — fallback
 *
 * The resolved value is echoed back as `Content-Language` so clients can
 * verify what the server actually used.
 */
class SetApiLocale
{
    /** Locales the platform ships translations/content for. */
    public const SUPPORTED = ['en', 'ar'];

    public function handle(Request $request, Closure $next): Response
    {
        $locale = $this->resolve($request);

        app()->setLocale($locale);

        $response = $next($request);
        $response->headers->set('Content-Language', $locale);

        return $response;
    }

    private function resolve(Request $request): string
    {
        $candidates = [
            $request->header('X-Locale'),
            $request->query('locale'),
            $request->header('Accept-Language'),
        ];

        foreach ($candidates as $candidate) {
            if ($normalized = $this->normalize($candidate)) {
                return $normalized;
            }
        }

        // Fall back to the signed-in user's stored preference (same source the
        // Blade site uses) so an app without the header still behaves sanely.
        $user = $request->user('sanctum');
        if ($user && method_exists($user, 'displayLang')) {
            if ($normalized = $this->normalize($user->displayLang())) {
                return $normalized;
            }
        }

        return $this->normalize(config('app.locale')) ?? 'en';
    }

    /**
     * Turns `ar`, `AR`, `ar-SA`, `ar-SA,en;q=0.9` … into `ar`.
     */
    private function normalize(?string $value): ?string
    {
        if (blank($value)) {
            return null;
        }

        // Only look at the highest-priority tag of an Accept-Language list.
        $primary = strtolower(trim(explode(',', $value)[0]));
        $primary = trim(explode(';', $primary)[0]);
        $primary = trim(explode('-', $primary)[0]);
        $primary = trim(explode('_', $primary)[0]);

        return in_array($primary, self::SUPPORTED, true) ? $primary : null;
    }
}
