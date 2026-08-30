<?php

namespace App\Http\Controllers\App\Profile;

use App\Data\Profile\DisplaySettingData;
use App\Data\Profile\EmailPreferencesData;
use App\Enums\PreferenceKey;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\ViewModels\Settings\PreferenceView;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class SettingController extends Controller
{
    public function index(): View
    {
        /** @var User * */
        $user = auth()->user();

        return view('app.profile.settings', new PreferenceView($user));
    }

    public function updateDisplayLanguage(DisplaySettingData $data): RedirectResponse
    {
        /** @var User * */
        $user = auth()->user();
        $user->preferences()->updateOrCreate(
            ['key' => PreferenceKey::DisplayLanguage],
            ['value' => $data->displayLanguage->value]
        );

        $user->preferences()->updateOrCreate(
            ['key' => PreferenceKey::LearningLanguage],
            ['value' => $data->learningLanguage->value]
        );

        return redirect()->route('app.settings');
    }

    public function updateEmailPreferences(EmailPreferencesData $data): RedirectResponse
    {
        /** @var User * */
        $user = auth()->user();
        $user->preferences()->updateOrCreate(
            ['key' => PreferenceKey::FollowupEmail],
            ['value' => $data->receiveFollowupEmails]
        );

        $user->preferences()->updateOrCreate(
            ['key' => PreferenceKey::NotificationEmail],
            ['value' => $data->receiveNotificationEmails]
        );

        $user->preferences()->updateOrCreate(
            ['key' => PreferenceKey::UpdateEmail],
            ['value' => $data->receiveUpdatesEmails]
        );

        return redirect()->route('app.settings');
    }
}
