<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\LessonTracking;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class CourseInsightsStats extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $tutorId = auth()->user()->id;

        if (!$tutorId) {
            return [];
        }

        $courseIds = Course::where('tutor_id', $tutorId)->pluck('id');

        // Total lesson views (trackings)
        $totalLessonViews = LessonTracking::whereIn('course_id', $courseIds)->count();

        // Completed lessons
        $completedLessons = LessonTracking::whereIn('course_id', $courseIds)
            ->whereNotNull('completed_at')
            ->count();

        // Course completions (enrollments with passed_at)
        $courseCompletions = Enrollment::whereIn('course_id', $courseIds)
            ->whereNotNull('passed_at')
            ->count();

        // Average course progress
        $avgProgress = Enrollment::whereIn('course_id', $courseIds)
            ->avg('progress') ?? 0;

        // This week's new enrollments
        $weeklyEnrollments = Enrollment::whereIn('course_id', $courseIds)
            ->where('created_at', '>=', now()->subWeek())
            ->count();

        // Average score
        $avgScore = Enrollment::whereIn('course_id', $courseIds)
            ->where('score', '>', 0)
            ->avg('score') ?? 0;

        return [
            Stat::make(__('tutor.stats.total_lesson_views'), number_format($totalLessonViews))
                ->description(__('tutor.stats.all_time_interactions'))
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),

            Stat::make(__('tutor.stats.lessons_completed'), number_format($completedLessons))
                ->description(__('tutor.stats.lessons_marked_complete'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('tutor.stats.course_completions'), number_format($courseCompletions))
                ->description(__('tutor.stats.students_finished_courses'))
                ->descriptionIcon('heroicon-m-trophy')
                ->color('success'),

            Stat::make(__('tutor.stats.avg_progress'), number_format($avgProgress, 1) . '%')
                ->description(__('tutor.stats.average_student_progress'))
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning'),

            Stat::make(__('tutor.stats.new_enrollments'), number_format($weeklyEnrollments))
                ->description(__('tutor.stats.this_week'))
                ->descriptionIcon('heroicon-m-user-plus')
                ->color('info'),

            Stat::make(__('tutor.stats.avg_score'), number_format($avgScore, 1) . '%')
                ->description(__('tutor.stats.average_student_score'))
                ->descriptionIcon('heroicon-m-academic-cap')
                ->color('primary'),
        ];
    }
}
