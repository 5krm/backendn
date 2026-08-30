<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StudentStatsOverview extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $tutorId = auth()->user()->id;

        if (!$tutorId) {
            return [];
        }

        $courseIds = Course::where('tutor_id', $tutorId)->pluck('id');

        // Total unique students
        $totalStudents = Enrollment::whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count('user_id');

        // New students this week
        $newThisWeek = Enrollment::whereIn('course_id', $courseIds)
            ->where('created_at', '>=', now()->subWeek())
            ->distinct('user_id')
            ->count('user_id');

        // New students last week (for comparison)
        $lastWeek = Enrollment::whereIn('course_id', $courseIds)
            ->whereBetween('created_at', [now()->subWeeks(2), now()->subWeek()])
            ->distinct('user_id')
            ->count('user_id');

        $weekChange = $lastWeek > 0 
            ? round((($newThisWeek - $lastWeek) / $lastWeek) * 100, 1)
            : ($newThisWeek > 0 ? 100 : 0);

        // Active students (with activity in last 7 days)
        $activeStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('updated_at', '>=', now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');

        // Completed students
        $completedStudents = Enrollment::whereIn('course_id', $courseIds)
            ->whereNotNull('passed_at')
            ->distinct('user_id')
            ->count('user_id');

        // At-risk students (enrolled but < 20% progress and no activity in 14 days)
        $atRiskStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('progress', '<', 20)
            ->whereNull('passed_at')
            ->where('updated_at', '<', now()->subDays(14))
            ->distinct('user_id')
            ->count('user_id');

        // Average courses per student
        $avgCoursesPerStudent = $totalStudents > 0
            ? round(Enrollment::whereIn('course_id', $courseIds)->count() / $totalStudents, 1)
            : 0;

        return [
            Stat::make(__('tutor.stats.total_students'), number_format($totalStudents))
                ->description(__('tutor.stats.unique_enrolled_students'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary'),

            Stat::make(__('tutor.stats.new_this_week'), number_format($newThisWeek))
                ->description(($weekChange >= 0 ? "+{$weekChange}% " : "{$weekChange}% ") . __('tutor.stats.vs_last_week'))
                ->descriptionIcon($weekChange >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($weekChange >= 0 ? 'success' : 'danger'),

            Stat::make(__('tutor.stats.active_students'), number_format($activeStudents))
                ->description(__('tutor.stats.active_in_7_days'))
                ->descriptionIcon('heroicon-m-bolt')
                ->color('info'),

            Stat::make(__('tutor.stats.completed'), number_format($completedStudents))
                ->description(__('tutor.stats.finished_at_least_1'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('success'),

            Stat::make(__('tutor.stats.at_risk'), number_format($atRiskStudents))
                ->description(__('tutor.stats.low_progress_inactive'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($atRiskStudents > 0 ? 'danger' : 'success'),

            Stat::make(__('tutor.stats.avg_courses_per_student'), $avgCoursesPerStudent)
                ->description(__('tutor.stats.courses_per_student'))
                ->descriptionIcon('heroicon-m-book-open')
                ->color('primary'),
        ];
    }
}
