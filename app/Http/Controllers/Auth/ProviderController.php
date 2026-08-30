<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Mail\SocialAccountGreeting;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class ProviderController extends Controller
{
    private const SUPPORTED_PROVIDERS = ['google'];

    public function redirect(string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::SUPPORTED_PROVIDERS, true), 404);

        return Socialite::driver($provider)->redirect();
    }

    public function callback(Request $request, string $provider): RedirectResponse
    {
        abort_unless(in_array($provider, self::SUPPORTED_PROVIDERS, true), 404);

        // Google returns ?error=... (no code) when the user denies access,
        // or bots/bookmarks hit the callback URL directly.
        if ($request->filled('error') || ! $request->filled('code')) {
            return redirect()
                ->route('auth.login')
                ->withErrors(['email' => __('auth.socialite_failed')]);
        }

        try {
            $socialUser = Socialite::driver($provider)->user();

            $user = User::where('email', $socialUser->email)->first();

            if ($user != null && $user->provider != $provider) {
                return redirect()
                    ->route('auth.login')
                    ->withInput(['email' => $socialUser->email])
                    ->withErrors([
                        'email' => __('auth.socialite_different_provider'),
                    ]);
            }

            if ($user != null && $user->provider_id != $socialUser->id) {
                return redirect()
                    ->route('auth.login')
                    ->withInput(['email' => $socialUser->email])
                    ->withErrors([
                        'email' => __('auth.socialite_account_issue'),
                    ]);
            }

            if ($user == null) {
                $user = $this->createUser($socialUser, $provider);
                Mail::to($user->email)->send(new SocialAccountGreeting($user));
            }

            Auth::login($user);

            return redirect()->route('dashboard');
        } catch (InvalidStateException $e) {
            dd($e);

            return redirect()
                ->route('auth.login')
                ->withErrors(['email' => __('auth.socialite_failed')]);
        } catch (\Exception $e) {
            dd($e);
            \Sentry\captureException($e);

            return redirect()
                ->route('auth.login')
                ->withErrors(['email' => __('auth.socialite_failed')]);
        }
    }

    private function createUser(\Laravel\Socialite\Contracts\User $socialUser, string $provider): User
    {
        $user = User::create([
            'name' => $socialUser->name,
            'email' => $socialUser->email,
            'provider_token' => $socialUser->token,
            'email_verified_at' => now(),
            'provider' => $provider,
            'provider_id' => $socialUser->id,
        ]);

        $user->clearMediaCollection('avatars');
        $avatar_url = $socialUser->getAvatar();
        if ($avatar_url != null) {
            $extension = pathinfo(parse_url($avatar_url, PHP_URL_PATH), PATHINFO_EXTENSION);
            $timestamp = now()->timestamp;
            $user->addMediaFromUrl($socialUser->getAvatar())
                ->usingFileName("avatar_{$timestamp}.{$extension}")
                ->toMediaCollection('avatars');
        }

        Event::dispatch(new Registered($user));

        return $user;
    }
}
