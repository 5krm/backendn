<?php

namespace App\Http\Controllers\App\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    public function create(Request $request): View
    {
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if ($record && Hash::check($request->token, $record->token)) {
            return view('auth.reset-password', ['request' => $request]);
        }

        return view('errors.invalid-token');
    }

    public function store(Request $request): RedirectResponse
    {

        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill(['password' => Hash::make($request->password)])->save();
                if (! empty($user->getRememberToken())) {
                    $user->setRememberToken(Str::random(60));
                }
                // event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('auth.login')->with('status', __($status))
            : back()->withErrors(['status' => __($status)])->withInput();
    }
}
