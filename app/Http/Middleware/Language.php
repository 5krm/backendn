<?php

namespace App\Http\Middleware;

use App\Enums\PreferenceKey;
use App\Models\UserPreference;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class Language
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && ! is_null($userLanguage = UserPreference::where('key', PreferenceKey::DisplayLanguage)->where('user_id', auth()->user()->id)->first()?->value)) {
            $this->set_locale($userLanguage);
        } elseif (Session::has('locale') && array_key_exists(Session::get('locale'), config('languages'))) {
            $this->set_locale(Session::get('locale'));
        } else {
            $this->set_locale(config('app.fallback_locale'));
        }

        return $next($request);
    }

    public function set_locale($locale)
    {
        App::setLocale($locale);
        Carbon::setlocale($locale);
        View::share('direction', $locale == 'en' ? 'ltr' : 'rtl');
    }
}
