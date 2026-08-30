<?php

namespace App\Http\Controllers\Api\Tutor;

use App\Enums\SocialPlatform;
use App\Http\Controllers\Controller;
use App\Models\Tutor;
use App\Models\User;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password as PasswordRule;

class TutorAuthController extends Controller
{
    use ApiResponse;

    public function register(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
            'phone' => ['nullable', 'string', 'max:20'],
            'device_name' => ['required', 'string', 'max:255'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'phone' => $data['phone'] ?? null,
            'is_tutor' => true,
        ]);

        Tutor::create([
            'user_id' => $user->id,
            'is_active' => false,
        ]);

        $token = $user->createToken($data['device_name'])->plainTextToken;

        return $this->created([
            'user' => $this->userPayload($user),
            'token' => ['access_token' => $token, 'token_type' => 'Bearer'],
        ], 'Registration successful');
    }

    public function login(Request $request): JsonResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
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
            'user' => $this->userPayload($user->load(['tutorProfile', 'country', 'socialLinks'])),
            'token' => ['access_token' => $token, 'token_type' => 'Bearer'],
        ], 'Login successful');
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user()->load(['tutorProfile', 'country', 'socialLinks']);

        return $this->success($this->userPayload($user));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()?->delete();

        return $this->success(null, 'Logged out successfully');
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'phone' => ['sometimes', 'nullable', 'string', 'max:20'],
            'bio' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'bio_en' => ['sometimes', 'nullable', 'string', 'max:1000'],
            'job_title' => ['sometimes', 'nullable', 'string', 'max:255'],
            'job_title_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'country_id' => ['sometimes', 'nullable', 'exists:countries,id'],
            'specialization' => ['sometimes', 'nullable', 'string', 'max:255'],
            'specialization_en' => ['sometimes', 'nullable', 'string', 'max:255'],
            'experience_years' => ['sometimes', 'nullable', 'integer', 'min:0'],
            'hourly_rate' => ['sometimes', 'nullable', 'numeric', 'min:0'],
        ]);

        $userData = collect($data)->only([
            'name', 'phone', 'bio', 'bio_en', 'job_title', 'job_title_en', 'country_id',
        ])->filter(fn ($v) => $v !== null)->toArray();

        if (! empty($userData)) {
            $user->update($userData);
        }

        $tutorData = collect($data)->only([
            'specialization', 'specialization_en', 'experience_years', 'hourly_rate',
        ])->filter(fn ($v) => $v !== null)->toArray();

        if (! empty($tutorData)) {
            $user->tutorProfile()->updateOrCreate(
                ['user_id' => $user->id],
                $tutorData
            );
        }

        return $this->success(
            $this->userPayload($user->fresh(['tutorProfile', 'country', 'socialLinks'])),
            'Profile updated successfully'
        );
    }

    public function uploadAvatar(Request $request): JsonResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'max:5120'], // 5 MB
        ]);

        $user = $request->user();
        $user->clearMediaCollection('avatars');
        $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');

        return $this->success(
            ['avatar' => $user->profile],
            'Avatar updated successfully'
        );
    }

    public function changePassword(Request $request): JsonResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'confirmed', PasswordRule::min(8)],
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            return $this->error('The provided current password does not match our records.', null, 422);
        }

        $user->update([
            'password' => Hash::make($data['password']),
        ]);

        return $this->success(null, 'Password changed successfully');
    }

    public function updateSocialLinks(Request $request): JsonResponse
    {
        $data = $request->validate([
            'links' => ['required', 'array'],
            'links.*.platform' => ['required', 'string'],
            'links.*.url' => ['required', 'url'],
        ]);

        $user = $request->user();

        $user->socialLinks()->delete();
        foreach ($data['links'] as $link) {
            $user->socialLinks()->create([
                'platform' => $link['platform'],
                'url' => $link['url'],
            ]);
        }

        return $this->success(
            $user->socialLinks()->get(),
            'Social links updated successfully'
        );
    }

    public function uploadKyc(Request $request): JsonResponse
    {
        $request->validate([
            'kyc_document' => ['required', 'file', 'max:10240'], // 10 MB limit
        ]);

        $user = $request->user();
        $tutor = $user->tutorProfile;

        if (! $tutor) {
            $tutor = Tutor::create([
                'user_id' => $user->id,
                'is_active' => false,
            ]);
        }

        $tutor->clearMediaCollection('kyc_documents');
        $tutor->addMediaFromRequest('kyc_document')->toMediaCollection('kyc_documents');

        return $this->success(
            ['kyc_document' => $tutor->getFirstMediaUrl('kyc_documents')],
            'KYC document uploaded successfully'
        );
    }

    private function userPayload(User $user): array
    {
        $tutor = $user->tutorProfile;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => $user->phone ?? null,
            'job_title' => $user->job_title ?? null,
            'job_title_en' => $user->job_title_en ?? null,
            'bio' => $user->bio ?? null,
            'bio_en' => $user->bio_en ?? null,
            'avatar' => $user->profile,
            'is_tutor' => $user->isTutor(),
            'is_admin' => $user->isAdmin(),
            'country' => $user->country ? [
                'id' => $user->country->id,
                'name' => $user->country->name,
            ] : null,
            'tutor_profile' => $tutor ? [
                'id' => $tutor->id,
                'specialization' => $tutor->specialization,
                'specialization_en' => $tutor->specialization_en,
                'experience_years' => $tutor->experience_years,
                'hourly_rate' => $tutor->hourly_rate,
                'is_active' => (bool) $tutor->is_active,
                'kyc_document' => $tutor->getFirstMediaUrl('kyc_documents') ?: null,
            ] : null,
            'social_links' => $user->socialLinks ? $user->socialLinks->map(fn ($link) => [
                'id' => $link->id,
                'platform' => $link->platform instanceof SocialPlatform ? $link->platform->value : (string) $link->platform,
                'url' => $link->url,
            ]) : [],
            'email_verified_at' => $user->email_verified_at?->toISOString(),
        ];
    }
}
