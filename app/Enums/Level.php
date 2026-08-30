<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum Level: string implements HasColor, HasLabel
{
    case Beginner = 'beginner';
    case Intermediate = 'intermediate';
    case Advanced = 'preview';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function titles(): array
    {
        return [
            self::Beginner->value => __('course.levels.beginner'),
            self::Intermediate->value => __('course.levels.intermediate'),
            self::Advanced->value => __('course.levels.advanced'),
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::Beginner => __('course.levels.beginner'),
            self::Intermediate => __('course.levels.intermediate'),
            self::Advanced => __('course.levels.advanced')
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::Beginner => 'info',
            self::Intermediate => 'info',
            self::Advanced => 'info',
        };
    }
}
