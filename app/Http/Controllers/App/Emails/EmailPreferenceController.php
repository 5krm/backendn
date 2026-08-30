<?php

namespace App\Http\Controllers\App\Emails;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class EmailPreferenceController extends Controller
{
    public function unsubscribe($token, $subscription_key)
    {
        $user = User::findByToken($token);
        if (!$user) {
            error_log('User not found by token: ' . $token);
            abort(404);
        }

        $user->preferences()->where('key', $subscription_key)->update(['value' => false]);

        return view('app.emails.unsubscribed', ['type' => $subscription_key, 'token' => $token]);
    }

    public function subscribe($token, $subscription_key)
    {
        $user = User::findByToken($token);
        if (!$user) {
            Log::error('User not found by token: ' . $token);
            abort(404);
        }

        $user->preferences()->where('key', $subscription_key)->update(['value' => true]);

        return redirect()->route('auth.login');
    }
}
