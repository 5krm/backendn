<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;


enum CourseEmailType: string implements HasLabel, HasColor
{
    case welcome = 'welcome';
    case completion = 'completion';
    case inactivity = 'inactivity';
    case halfway = 'halfway';
    case NewCourse = 'new-course';
    case PublishedLesson = 'published-lesson';

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public static function titles(): array
    {
        return [
            self::welcome->value => __('course.emails.types.welcome'),
            self::completion->value => __('course.emails.types.completion'),
            self::inactivity->value => __('course.emails.types.inactivity'),
            self::halfway->value => __('course.emails.types.halfway'),
            self::NewCourse->value => __('course.emails.types.newCourse'),
            self::PublishedLesson->value => __('course.emails.types.publishedLesson'),
        ];
    }

    public function getLabel(): string
    {
        return match ($this) {
            self::welcome => __('course.emails.types.welcome'),
            self::completion => __('course.emails.types.completion'),
            self::inactivity => __('course.emails.types.inactivity'),
            self::halfway => __('course.emails.types.halfway'),
            self::NewCourse => __('course.emails.types.newCourse'),
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::welcome => 'info',
            self::completion => 'success',
            self::inactivity => 'warning',
            self::halfway => 'primary'
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::welcome => 'heroicon-m-hand-raised',
            self::completion => 'heroicon-m-check-circle',
            self::inactivity => 'heroicon-m-clock',
            self::halfway => 'heroicon-m-chart-bar',
        };
    }
}
