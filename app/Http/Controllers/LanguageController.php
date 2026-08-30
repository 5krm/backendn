<?php

namespace App\Http\Controllers;

use App\Enums\PreferenceKey;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class LanguageController extends Controller
{
    public function switch(Request $request, $lang)
    {
        if (! array_key_exists($lang, config('languages'))) {
            return redirect()->back();
        }

        if (auth()->check()) {
            /** @var User * */
            $user = auth()->user();
            $user->preferences()->updateOrCreate(
                ['key' => PreferenceKey::DisplayLanguage],
                ['value' => $lang]
            );
        } else {
            Session::put('locale', $lang);
        }

        // Get redirect URL from query parameter or referer
        $redirectUrl = $request->query('redirect') ?? $request->header('referer') ?? route('courses');

        // URL decode the redirect URL if it was encoded
        $redirectUrl = urldecode($redirectUrl);

        // Security: ensure redirect is to our domain (use request host for production compatibility)
        $currentHost = $request->getHost();
        $redirectHost = parse_url($redirectUrl, PHP_URL_HOST);

        // Allow redirect if it's to the same host, or if it's a relative URL (no host)
        if ($redirectHost !== null && $redirectHost !== $currentHost) {
            $redirectUrl = route('courses');
        }

        return redirect($redirectUrl);
    }
}
