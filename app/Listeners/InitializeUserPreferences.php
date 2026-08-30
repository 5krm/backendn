<?php

namespace App\Listeners;

use App\Enums\PreferenceKey;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Session;

class InitializeUserPreferences
{
    public function handle(Registered $event): void
    {

        /** @var User */
        $user = $event->user;
        $lang = config('app.fallback_locale');

        if (Session::has('locale') && array_key_exists(Session::get('locale'), config('languages'))) {
            $lang = Session::get('locale', 'ar');
        }

        $existingKeys = $user->preferences()
            ->pluck('key')
            ->map(fn ($key) => $key instanceof PreferenceKey ? $key->value : (string) $key)
            ->all();

        $defaults = collect([
            ['key' => PreferenceKey::DisplayLanguage, 'value' => $lang],
            ['key' => PreferenceKey::LearningLanguage, 'value' => $lang],
            ['key' => PreferenceKey::FollowupEmail, 'value' => true],
            ['key' => PreferenceKey::NotificationEmail, 'value' => true],
            ['key' => PreferenceKey::UpdateEmail, 'value' => true],
        ])->reject(fn (array $preference) => in_array($preference['key']->value, $existingKeys, true));

        if ($defaults->isEmpty()) {
            return;
        }

        $user->preferences()->insert($defaults->map(fn (array $preference) => [
            'user_id' => $user->id,
            'key' => $preference['key']->value,
            'value' => $preference['value'],
        ])->all());
    }
}
