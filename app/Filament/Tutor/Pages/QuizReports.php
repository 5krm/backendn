<?php

namespace App\Filament\Tutor\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;
use UnitEnum;

class QuizReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedQuestionMarkCircle;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    public static function getNavigationGroup(): string
    {
        return __('tutor.nav.reports_analytics');
    }
    public static function getNavigationLabel(): string
    {
        return __('tutor.reports.quiz_reports');
    }

    public function getTitle(): string
    {
        return __('tutor.reports.quiz_analytics');
    }

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.tutor.pages.quiz-reports';

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Tutor\Widgets\QuizStatsOverview::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            \App\Filament\Tutor\Widgets\QuizPassRateChart::class,
            \App\Filament\Tutor\Widgets\QuizzesPerCourseTable::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}
