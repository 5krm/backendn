<?php

namespace App\Http\Controllers\App\Profile;

use App\Data\ListItemData;
use App\Enums\SocialPlatform;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\RedirectResponse;
use App\Data\Profile\UpdateProfileData;
use App\Data\Profile\ChangePasswordData;
use App\Models\Country;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    public function index(): View
    {
        $language = app()->getLocale();
        $countries = Country::all();
        $countryItems = $countries->map(fn($c) => new ListItemData($c->id,  $language == 'ar' ? $c->name_ar : $c->name))->sort(fn($a, $b) => $a->text <=> $b->text);

        /** @var User $user */
        $user = auth()->user();
        $user->load(['socialLinks']);

        $socialByPlatform = $user->socialLinks->keyBy(
            fn ($link) => $link->platform?->value ?? ($link->getAttributes()['platform'] ?? '')
        );

        return view('app.profile.profile-info', [
            'user' => $user,
            'countries' => $countries,
            'countryItems' => $countryItems,
            'socialPlatforms' => SocialPlatform::cases(),
            'socialByPlatform' => $socialByPlatform,
        ]);
    }

    public function updateInfo(UpdateProfileData $data): RedirectResponse
    {
        /** @var User $user */
        $user = auth()->user();

        $user->update([
            'name' => $data->name,
            'email' => $data->email,
            'phone' => $data->phone,
            'country_id' => $data->country_id,
            'job_title' => $data->job_title,
            'job_title_en' => $data->job_title_en,
            'bio' => $data->bio,
            'bio_en' => $data->bio_en,
        ]);

        $this->syncSocialLinks($user, $data->social_links ?? []);

        return redirect()->route('app.profile');
    }

    /**
     * @param  array<int, array{platform?: string, url?: string}>  $links
     */
    private function syncSocialLinks(User $user, array $links): void
    {
        $keptPlatforms = [];

        foreach ($links as $link) {
            $platform = $link['platform'] ?? null;
            $url = trim($link['url'] ?? '');

            if (! $platform || $url === '') {
                continue;
            }

            $keptPlatforms[] = $platform;

            $user->socialLinks()->updateOrCreate(
                ['platform' => $platform],
                ['url' => $url]
            );
        }

        $user->socialLinks()
            ->when(
                $keptPlatforms !== [],
                fn ($query) => $query->whereNotIn('platform', $keptPlatforms),
                fn ($query) => $query
            )
            ->delete();
    }

    public function changeAvatar(Request $request): RedirectResponse
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:1024'],
        ]);

        /** @var User **/
        $user = auth()->user();
        $user->clearMediaCollection('avatars');
        $user->addMediaFromRequest('avatar')->toMediaCollection('avatars');

        return redirect()->route('app.profile');
    }

    public function changePassword(ChangePasswordData  $data): RedirectResponse
    {
        /** @var User **/
        $user = auth()->user();

        if (!Hash::check($data->current_password,  $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => trans('auth.wrong_password'),
            ]);
        }

        $user->password = Hash::make($data->new_password);
        $user->save();

        return redirect()->route('app.profile');
    }
}
