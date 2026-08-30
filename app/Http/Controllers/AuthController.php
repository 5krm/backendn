<?php

namespace App\Http\Controllers;

use App\Data\Auth\LoginData;
use App\Data\Auth\RegisterData;
use App\Data\ListItemData;
use App\Mail\VerifyEmail;
use App\Models\Country;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function show()
    {
        $language = app()->getLocale();
        $countries = Country::all();
        $countryItems = $countries
            ->map(fn($c) => new ListItemData($c->id, $language == 'ar' ? $c->name_ar : $c->name))
            ->sort(fn($a, $b) => $a->text <=> $b->text);
        
        return view('auth.register', compact('countries', 'countryItems'));
    }

    public function register(RegisterData $data)
    {
        try {
            DB::beginTransaction();

            $hashedPassword = Hash::make($data->password);

            $existing = User::withTrashed()->where('email', $data->email)->first();

            if ($existing && $existing->trashed()) {
                $existing->restore();
                $existing->update([
                    'name' => $data->name,
                    'password' => $hashedPassword,
                ]);
                $user = $existing;
            } else {
                $user = User::create([
                    'name' => $data->name,
                    'email' => $data->email,
                    'password' => $hashedPassword,
                    'country_id' => $data->country_id,
                    'phone' => $data->phone
                ]);
            }

            Mail::to($user->email)->send(new VerifyEmail($user));
            Auth::login($user);

            DB::commit();

            return Redirect::guest(URL::route('verification.notice'));
        } catch (\Exception $e) {
            DB::rollBack();
            \Sentry\captureException($e);
            return back()->withErrors(['email' => __('auth.register.failed')]);
        }
    }

    public function login(LoginData $data)
    {
        if (! Auth::attempt($data->toArray(), remember: request()->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        return redirect()->intended(default: route('dashboard'));
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('auth.login'));
    }
}
