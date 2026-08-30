<?php

namespace App\Filament\Tutor\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class Dashboard extends BaseDashboard
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;

    protected string $view = 'filament.tutor.pages.dashboard';

    public static function getNavigationLabel(): string
    {
        return __('tutor.dashboard.title');
    }

    public function getTitle(): string
    {
        return __('tutor.dashboard.title');
    }

    public function getWidgets(): array
    {
        return [
            \App\Filament\Tutor\Widgets\TutorStatsOverview::class,
            \App\Filament\Tutor\Widgets\UpcomingMilestonesWidget::class,
            \App\Filament\Tutor\Widgets\RecentNotificationsWidget::class,
            \App\Filament\Tutor\Widgets\RecentCommentsWidget::class,
        ];
    }

    public function getColumns(): array|int
    {
        return 2;
    }
}
