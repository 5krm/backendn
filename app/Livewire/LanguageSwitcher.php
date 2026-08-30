<?php

namespace App\Livewire;

use App\Enums\PreferenceKey;
use App\Models\User;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public function switchLanguage(): void
    {
        $currentLocale = app()->getLocale();
        $newLocale = $currentLocale === 'ar' ? 'en' : 'ar';

        // Update user preference if authenticated
        if (auth()->check()) {
            /** @var User $user */
            $user = auth()->user();
            $user->preferences()->updateOrCreate(
                ['key' => PreferenceKey::DisplayLanguage],
                ['value' => $newLocale]
            );
        }

        Session::put('locale', $newLocale);
        app()->setLocale($newLocale);

        // Use full page redirect to ensure locale is properly applied
        $this->redirect(request()->header('Referer', '/tutor'), navigate: false);
    }

    public function render()
    {
        return view('livewire.language-switcher');
    }
}
