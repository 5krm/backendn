<?php

namespace App\Filament\Tutor\Pages;

use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use BackedEnum;

class CourseReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChartBar;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;
    
    public static function getNavigationLabel(): string
    {
        return __('tutor.reports.course_reports');
    }

    public static function getNavigationGroup(): string
    {
        return __('tutor.nav.reports_analytics');
    }

    public function getTitle(): string
    {
        return __('tutor.reports.course_analytics');
    }

    protected static ?int $navigationSort = 1;

    protected string $view = 'filament.tutor.pages.course-reports';

    public function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Tutor\Widgets\CourseInsightsStats::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            \App\Filament\Tutor\Widgets\PopularCoursesChart::class,
            \App\Filament\Tutor\Widgets\CourseCompletionFunnelChart::class,
            \App\Filament\Tutor\Widgets\LessonViewsChart::class,
            \App\Filament\Tutor\Widgets\TopLessonsTable::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 2;
    }
}
