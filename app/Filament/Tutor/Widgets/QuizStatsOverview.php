<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\Quizzes\Quiz;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class QuizStatsOverview extends StatsOverviewWidget
{
    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $tutorId = auth()->user()->id;

        if (! $tutorId) {
            return [];
        }

        $courseIds = Course::where('tutor_id', $tutorId)->pluck('id');

        // Total quizzes (questions)
        $totalQuizzes = Quiz::where('tutor_id', $tutorId)->count();

        // Students who passed (have passed_at set)
        $passedStudents = Enrollment::whereIn('course_id', $courseIds)
            ->whereNotNull('passed_at')
            ->count();

        // Total students who attempted (have score > 0)
        $attemptedStudents = Enrollment::whereIn('course_id', $courseIds)
            ->where('score', '>', 0)
            ->count();

        // Pass rate: passed / attempted (not total enrolled)
        $passRate = $attemptedStudents > 0
            ? round(($passedStudents / $attemptedStudents) * 100, 1)
            : 0;

        // Average score across attempted students only
        $avgScore = Enrollment::whereIn('course_id', $courseIds)
            ->where('score', '>', 0)
            ->avg('score') ?? 0;

        // Perfect scores (100%)
        $perfectScores = Enrollment::whereIn('course_id', $courseIds)
            ->where('score', 100)
            ->count();

        return [
            Stat::make(__('tutor.stats.total_quizzes'), number_format($totalQuizzes))
                ->description(__('tutor.stats.quiz_questions_created'))
                ->descriptionIcon('heroicon-m-question-mark-circle')
                ->color('primary'),

            Stat::make(__('tutor.stats.pass_rate'), $passRate.'%')
                ->description(__('tutor.stats.passed_of_attempted', ['passed' => $passedStudents, 'attempted' => $attemptedStudents]))
                ->descriptionIcon('heroicon-m-check-badge')
                ->color($passRate >= 70 ? 'success' : ($passRate >= 50 ? 'warning' : 'danger')),

            Stat::make(__('tutor.stats.avg_score'), number_format($avgScore, 1).'%')
                ->description(__('tutor.stats.across_all_attempts'))
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color('info'),

            Stat::make(__('tutor.stats.perfect_scores'), number_format($perfectScores))
                ->description(__('tutor.stats.students_with_100'))
                ->descriptionIcon('heroicon-m-star')
                ->color('success'),
        ];
    }
}
