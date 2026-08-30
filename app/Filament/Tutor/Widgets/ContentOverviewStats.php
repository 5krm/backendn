<?php

namespace App\Filament\Tutor\Widgets;

use App\Enums\CourseStatus;
use App\Models\Courses\Course;
use App\Models\Lessons\Lesson;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class ContentOverviewStats extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $tutorId = auth()->user()->id;

        if (! $tutorId) {
            return [];
        }

        // Draft courses
        $draftCourses = Course::where('tutor_id', $tutorId)
            ->where('status', CourseStatus::draft)
            ->count();

        // Published courses
        $publishedCourses = Course::where('tutor_id', $tutorId)
            ->where('status', CourseStatus::published)
            ->count();

        // Draft lessons
        $draftLessons = Lesson::whereHas('course', fn (Builder $q) => $q->where('tutor_id', $tutorId))
            ->where('status', CourseStatus::draft)
            ->count();

        // Published lessons
        $publishedLessons = Lesson::whereHas('course', fn (Builder $q) => $q->where('tutor_id', $tutorId))
            ->where('status', CourseStatus::published)
            ->count();

        // Courses without lessons
        $coursesNoLessons = Course::where('tutor_id', $tutorId)
            ->whereDoesntHave('lessons')
            ->count();

        // Courses without quizzes
        $coursesNoQuizzes = Course::where('tutor_id', $tutorId)
            ->whereDoesntHave('quizzes')
            ->count();

        // Recently updated (last 7 days)
        $recentlyUpdated = Lesson::whereHas('course', fn (Builder $q) => $q->where('tutor_id', $tutorId))
            ->where('updated_at', '>=', now()->subDays(7))
            ->count();

        return [
            Stat::make(__('tutor.content.draft_courses'), $draftCourses)
                ->description(__('tutor.stats.awaiting_publication'))
                ->descriptionIcon('heroicon-m-document')
                ->color($draftCourses > 0 ? 'warning' : 'success'),

            Stat::make(__('tutor.content.published_courses'), $publishedCourses)
                ->description(__('tutor.stats.live_and_available'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),

            Stat::make(__('tutor.content.draft_lessons'), $draftLessons)
                ->description(__('tutor.stats.ready_to_publish'))
                ->descriptionIcon('heroicon-m-document-text')
                ->color($draftLessons > 0 ? 'warning' : 'success'),

            Stat::make(__('tutor.content.published_lessons'), $publishedLessons)
                ->description(__('tutor.stats.available_to_students'))
                ->descriptionIcon('heroicon-m-play-circle')
                ->color('success'),

            Stat::make(__('tutor.content.courses_without_lessons'), $coursesNoLessons)
                ->description(__('tutor.stats.need_content'))
                ->descriptionIcon('heroicon-m-exclamation-triangle')
                ->color($coursesNoLessons > 0 ? 'danger' : 'success'),

            Stat::make(__('tutor.content.recently_updated'), $recentlyUpdated)
                ->description(__('tutor.stats.last_7_days'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('info'),
        ];
    }
}
