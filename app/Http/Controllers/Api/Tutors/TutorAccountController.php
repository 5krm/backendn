<?php

namespace App\Http\Controllers\Api\Tutors;

use App\Http\Controllers\Controller;
use App\Models\PasswordResetToken;
use App\Models\Tutor;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;

class TutorAccountController extends Controller
{
    public function form($token)
    {
        $invitation = $this->findInvitationToken($token);

        if (! $invitation || $invitation->isExpired()) {
            return Redirect::to('/tutor/login')->withErrors([
                'token' => 'Your invitation link is invalid or has expired. Please contact your administrator for a new link.',
            ]);
        }

        return view('tutors.setup', [
            'token' => $token,
            'email' => $invitation->email,
        ]);
    }

    public function setup(Request $request, $token)
    {
        $invitation = $this->findInvitationToken($token);

        if (! $invitation || $invitation->isExpired()) {
            return Redirect::to('/tutor/login')->withErrors([
                'token' => 'Your invitation link is invalid or has expired. Please contact your administrator for a new link.',
            ]);
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::where('email', $invitation->email)->first();

        if (! $user) {
            return Redirect::to('/tutor/login')->withErrors([
                'token' => 'Unable to complete setup because the associated user account was not found.',
            ]);
        }

        $user->update([
            'name' => $data['name'],
            'password' => Hash::make($data['password']),
            'email_verified_at' => $user->email_verified_at ?? now(),
            'is_tutor' => true,
        ]);

        $invitation->update(['expired_at' => now()]);

        Tutor::create([
            'user_id' => $user->id,
            'experience_years' => 0,
            'is_active' => true,
            'is_verified' => true,
        ]);

        // Log the user in directly
        Auth::login($user);

        return Redirect::to('/tutor');
    }

    private function findInvitationToken(string $token): ?PasswordResetToken
    {
        return PasswordResetToken::where('token', hash('sha256', $token))
            ->where('type', 'invitation')
            ->first();
    }
}
