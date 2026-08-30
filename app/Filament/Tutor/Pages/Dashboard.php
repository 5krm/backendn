<?php

namespace App\Filament\Tutor\Pages;

use App\Filament\Tutor\Widgets\RecentCommentsWidget;
use App\Filament\Tutor\Widgets\RecentNotificationsWidget;
use App\Filament\Tutor\Widgets\TutorStatsOverview;
use App\Filament\Tutor\Widgets\UpcomingMilestonesWidget;
use BackedEnum;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;

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
            TutorStatsOverview::class,
            UpcomingMilestonesWidget::class,
            RecentNotificationsWidget::class,
            RecentCommentsWidget::class,
        ];
    }

    public function getColumns(): array|int
    {
        return 2;
    }
}
