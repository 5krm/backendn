<?php

namespace App\Filament\Tutor\Widgets;

use App\Enums\CourseStatus;
use App\Models\Certificate;
use App\Models\Courses\Course;
use App\Models\Lessons\Lesson;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class TutorStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $tutorId = auth()->user()->id;

        if (! $tutorId) {
            return [];
        }

        // Optimized single query for course counts
        $courseStats = Course::where('tutor_id', $tutorId)
            ->selectRaw('
                COUNT(*) as total_courses,
                SUM(CASE WHEN status = ? THEN 1 ELSE 0 END) as published_courses
            ', [CourseStatus::published->value])
            ->first();

        // Optimized lesson count
        $lessonsCount = Lesson::whereHas('course', function ($query) use ($tutorId) {
            $query->where('tutor_id', $tutorId);
        })->count();

        // Optimized student count
        $studentsCount = User::whereHas('courses', function ($query) use ($tutorId) {
            $query->where('tutor_id', $tutorId);
        })->count();

        // Optimized certificate count
        $certificatesCount = Certificate::where('tutor_id', $tutorId)->count();

        // Optimized revenue calculation
        $totalRevenue = DB::table('courses')
            ->join('enrollments', 'courses.id', '=', 'enrollments.course_id')
            ->where('courses.tutor_id', $tutorId)
            ->where('courses.is_free', false)
            ->sum('courses.price');

        return [
            Stat::make(__('tutor.dashboard.total_courses'), $courseStats->total_courses ?? 0)
                ->description(__('tutor.dashboard.total_courses_desc'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary')
                ->chart([7, 12, 15, 18, 22, 25, $courseStats->total_courses ?? 0]),

            Stat::make(__('tutor.dashboard.published_courses'), $courseStats->published_courses ?? 0)
                ->description(__('tutor.dashboard.published_courses_desc'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary')
                ->chart([3, 6, 9, 12, 14, 16, $courseStats->published_courses ?? 0]),

            Stat::make(__('tutor.dashboard.total_lessons'), $lessonsCount)
                ->description(__('tutor.dashboard.total_lessons_desc'))
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('primary')
                ->chart([10, 25, 40, 55, 70, 85, $lessonsCount]),

            Stat::make(__('tutor.dashboard.students'), $studentsCount)
                ->description(__('tutor.dashboard.students_desc'))
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->chart([5, 15, 30, 50, 75, 100, $studentsCount]),

            Stat::make(__('tutor.dashboard.certificates_issued'), $certificatesCount)
                ->description(__('tutor.dashboard.certificates_issued_desc'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('primary')
                ->chart([2, 5, 10, 15, 22, 30, $certificatesCount]),

            Stat::make(__('tutor.dashboard.total_revenue'), '$'.number_format($totalRevenue ?? 0, 2))
                ->description(__('tutor.dashboard.total_revenue_desc'))
                ->descriptionIcon('heroicon-m-currency-dollar')
                ->color('primary')
                ->chart([100, 250, 500, 750, 1000, 1500, $totalRevenue ?? 0]),
        ];
    }
}
