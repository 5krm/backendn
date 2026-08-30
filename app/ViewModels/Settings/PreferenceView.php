<?php

namespace App\ViewModels\Settings;

use App\Models\User;
use App\Enums\PreferenceKey;
use Illuminate\Contracts\Support\Arrayable;

class PreferenceView implements Arrayable
{
    public array $preferences;

    public function __construct(public User $user)
    {
        if (!$user->relationLoaded('preferences')) {
            $user->load('preferences');
        }

        foreach (PreferenceKey::cases() as $case) {
            $this->preferences[$case->value] = $user->preferences
                ->where('key', $case)
                ->first()->value ?? null;
        }
    }

    public function toArray(): array
    {
        return [
            'user' => $this->user,
            'preferences' => $this->preferences,
        ];
    }
}
