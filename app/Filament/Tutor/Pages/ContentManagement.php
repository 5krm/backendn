<?php

namespace App\Filament\Tutor\Pages;

use App\Filament\Tutor\Widgets\ContentOverviewStats;
use App\Filament\Tutor\Widgets\DraftContentWidget;
use App\Filament\Tutor\Widgets\RecentlyUpdatedWidget;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class ContentManagement extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPresentationChartBar;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    public static function getNavigationLabel(): string
    {
        return __('tutor.quick_actions.content_overview');
    }

    public function getTitle(): string
    {
        return __('tutor.quick_actions.content_overview');
    }

    protected static ?int $navigationSort = 2;

    protected string $view = 'filament.tutor.pages.content-management';

    public function getHeaderWidgets(): array
    {
        return [
            ContentOverviewStats::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            RecentlyUpdatedWidget::class,
            // \App\Filament\Tutor\Widgets\CoursesNeedingAttentionWidget::class,
            DraftContentWidget::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}
