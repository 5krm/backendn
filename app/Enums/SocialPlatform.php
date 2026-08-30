<?php

namespace App\Enums;

use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SocialPlatform: string implements HasLabel, HasIcon
{
    case Website = 'website';
    case LinkedIn = 'linkedin';
    case Facebook = 'facebook';
    case X = 'x';
    case Instagram = 'instagram';
    case Youtube = 'youtube';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Website => __('profile.social.website'),
            self::LinkedIn => __('profile.social.linkedin'),
            self::Facebook => __('profile.social.facebook'),
            self::X => __('profile.social.x'),
            self::Instagram => __('profile.social.instagram'),
            self::Youtube => __('profile.social.youtube'),
        };
    }

    public function getIcon(): string
    {
        return match ($this) {
            self::Website => 'heroicon-o-globe-alt',
            self::LinkedIn => 'heroicon-o-briefcase',
            self::Facebook => 'heroicon-o-user-group',
            self::X => 'heroicon-o-at-symbol',
            self::Instagram => 'heroicon-o-camera',
            self::Youtube => 'heroicon-o-play',
        };
    }

    /**
     * Iconify/Tailwind class for the app frontend.
     * Keep class names as string literals so Tailwind can scan this file.
     */
    public function mdiIcon(): string
    {
        return match ($this) {
            self::Website => 'icon-[mdi--web]',
            self::LinkedIn => 'icon-[mdi--linkedin]',
            self::Facebook => 'icon-[mdi--facebook]',
            self::X => 'icon-[mdi--twitter]',
            self::Instagram => 'icon-[mdi--instagram]',
            self::Youtube => 'icon-[mdi--youtube]',
            default => 'icon-[mdi--link-variant]',
        };
    }
}
