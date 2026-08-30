<?php

namespace App\Filament\Tutor\Pages;

use App\Filament\Tutor\Widgets\AtRiskStudentsTable;
use App\Filament\Tutor\Widgets\EnrollmentTrendsChart;
use App\Filament\Tutor\Widgets\StudentStatsOverview;
use App\Filament\Tutor\Widgets\TopStudentsTable;
use BackedEnum;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;

class StudentReports extends Page
{
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static ?string $navigationLabel = null;

    protected static ?string $title = null;

    public static function getNavigationGroup(): string
    {
        return __('tutor.nav.reports_analytics');
    }

    public static function getNavigationLabel(): string
    {
        return __('tutor.reports.student_reports');
    }

    public function getTitle(): string
    {
        return __('tutor.reports.student_analytics');
    }

    protected static ?int $navigationSort = 3;

    protected string $view = 'filament.tutor.pages.student-reports';

    public function getHeaderWidgets(): array
    {
        return [
            StudentStatsOverview::class,
        ];
    }

    public function getFooterWidgets(): array
    {
        return [
            EnrollmentTrendsChart::class,
            TopStudentsTable::class,
            AtRiskStudentsTable::class,
        ];
    }

    public function getFooterWidgetsColumns(): int|array
    {
        return 1;
    }
}
