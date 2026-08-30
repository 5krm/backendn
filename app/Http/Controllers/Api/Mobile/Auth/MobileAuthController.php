<?php

namespace App\Http\Controllers\Api\Mobile\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Carbon\Carbon;

/**
 * Handles all authentication for the Flutter mobile app.
 *
 * Uses Sanctum personal access tokens — one token per device.
 */
class MobileAuthController extends Controller
{
    use ApiResponse;

    // ── Register ──────────────────────────────────────────────────────────────

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'confirmed', PasswordRule::min(8)],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'is_tutor'              => ['boolean'],
            'device_name'           => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
            'is_tutor' => $data['is_tutor'] ?? false,
        ]);

        $token = $user->createToken($data['device_name'])->plainTextToken;

        return $this->created([
            'user'  => $this->userPayload($user),
            'token' => ['access_token' => $token, 'token_type' => 'Bearer'],
        ], 'Registration successful');
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required', 'string'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $user = User::where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return $this->error('Invalid email or password.', null, 401);
        }

        // Revoke any existing token for this device to avoid duplicates
        $user->tokens()->where('name', $data['device_name'])->delete();

        $token = $user->createToken($data['device_name'])->plainTextToken;

        return $this->success([
            'user'  => $this->userPayload($user),
            'token' => ['access_token' => $token, 'token_type' => 'Bearer'],
        ], 'Login successful');
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    // ── Token Refresh ─────────────────────────────────────────────────────────

    public function refresh(Request $request): JsonResponse
    {
        $user        = $request->user();
        $currentToken = $user->currentAccessToken();
        $deviceName  = $currentToken->name;

        $currentToken->delete();
        $newToken = $user->createToken($deviceName)->plainTextToken;

        return $this->success([
            'token' => ['access_token' => $newToken, 'token_type' => 'Bearer'],
        ], 'Token refreshed');
    }

    // ── Me ────────────────────────────────────────────────────────────────────

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load('tutorProfile', 'country');

        return $this->success($this->userPayload($user));
    }

    // ── Update Profile ────────────────────────────────────────────────────────

    public function updateProfile(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'       => ['sometimes', 'string', 'max:255'],
            'phone'      => ['sometimes', 'nullable', 'string', 'max:20'],
            'bio'        => ['sometimes', 'nullable', 'string', 'max:1000'],
            'country_id' => ['sometimes', 'nullable', 'exists:countries,id'],
        ]);

        $request->user()->update($data);

        return $this->success($this->userPayload($request->user()->fresh('country')), 'Profile updated');
    }

    // ── Upload Avatar ─────────────────────────────────────────────────────────

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:5120'], // 5 MB
        ]);

        $user = $request->user();
        $user->clearMediaCollection('avatars');
        $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');

        return $this->success(
            ['avatar' => $user->getFirstMediaUrl('avatars')],
            'Avatar updated'
        );
    }

    // ── Save FCM Token ────────────────────────────────────────────────────────

    public function saveFcmToken(Request $request): JsonResponse
    {
        $request->validate([
            'fcm_token' => ['required', 'string'],
        ]);

        // Store on the token record so each device has its own FCM token
        $request->user()->currentAccessToken()->forceFill([
            'fcm_token' => $request->fcm_token,
        ])->save();

        return $this->success(null, 'FCM token saved');
    }

    // ── Forgot Password ───────────────────────────────────────────────────────

    public function forgotPassword(Request $request): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $user = User::where('email', $request->email)->first();
        if (! $user) {
            // Return success anyway to avoid email enumeration
            return $this->success(null, 'If that email exists, an OTP has been sent.');
        }

        $otp = str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token'      => Hash::make($otp),
                'created_at' => now(),
                'expired'    => false,
                'type'       => 'mobile_otp',
            ]
        );

        // Fire the email (reuse existing mail infrastructure)
        // Mail::to($user)->send(new OtpMail($otp)); // uncomment when mail configured

        return $this->success(
            app()->isLocal() ? ['otp' => $otp] : null, // expose OTP in local only
            'If that email exists, an OTP has been sent.'
        );
    }

    // ── Reset Password ────────────────────────────────────────────────────────

    public function resetPassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'otp'      => ['required', 'string', 'size:6'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $record = DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->where('type', 'mobile_otp')
            ->where('expired', false)
            ->where('created_at', '>=', now()->subMinutes(30))
            ->first();

        if (! $record || ! Hash::check($data['otp'], $record->token)) {
            return $this->error('Invalid or expired OTP.', null, 422);
        }

        $user = User::where('email', $data['email'])->first();
        if (! $user) {
            return $this->error('User not found.', null, 404);
        }

        $user->update(['password' => Hash::make($data['password'])]);

        // Invalidate all existing tokens for security
        $user->tokens()->delete();

        // Mark OTP as expired
        DB::table('password_reset_tokens')
            ->where('email', $data['email'])
            ->update(['expired' => true]);

        return $this->success(null, 'Password reset successfully. Please log in again.');
    }

    // ── Delete Account ────────────────────────────────────────────────────────
    
    public function deleteAccount(Request $request): JsonResponse
    {
        $user = $request->user();
        
        // Revoke all tokens
        $user->tokens()->delete();
        
        // Soft delete the user
        $user->delete();

        return $this->success(null, 'Account deleted successfully.');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Build the standard user payload for auth responses.
     */
    private function userPayload(User $user): array
    {
        return [
            'id'         => $user->id,
            'name'       => $user->name,
            'email'      => $user->email,
            'phone'      => $user->phone ?? null,
            'bio'        => $user->bio ?? null,
            'avatar'     => $user->profile, // uses the `profile` accessor
            'is_tutor'   => $user->isTutor(),
            'is_admin'   => $user->isAdmin(),
            'country'    => $user->country ? [
                'id'   => $user->country->id,
                'name' => $user->country->name,
            ] : null,
            'email_verified_at' => $user->email_verified_at?->toISOString(),
        ];
    }
}
