<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum CourseStatus: string implements HasColor, HasLabel
{
    case draft = 'draft';
    case published = 'published';
    case preview = 'preview';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::draft => __('course.status.draft'),
            self::published => __('course.status.published'),
            self::preview => __('course.status.preview'),
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::draft => 'gray',
            self::published => 'success',
            self::preview => 'warning',
        };
    }
}
