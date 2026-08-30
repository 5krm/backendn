<?php

namespace App\Filament\Tutor\Pages;

use App\Filament\Tutor\Widgets\CourseCompletionFunnelChart;
use App\Filament\Tutor\Widgets\CourseInsightsStats;
use App\Filament\Tutor\Widgets\LessonViewsChart;
use App\Filament\Tutor\Widgets\PopularCoursesChart;
use App\Filament\Tutor\Widgets\TopLessonsTable;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

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
            CourseInsightsStats::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            PopularCoursesChart::class,
            CourseCompletionFunnelChart::class,
            LessonViewsChart::class,
            TopLessonsTable::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 2;
    }
}
