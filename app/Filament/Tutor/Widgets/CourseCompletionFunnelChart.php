<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Models\Lessons\LessonTracking;
use Filament\Widgets\ChartWidget;

class CourseCompletionFunnelChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('tutor.widgets.completion_funnel');
    }

    protected function getData(): array
    {
        $tutorId = auth()->user()->id;

        if (!$tutorId) {
            return ['datasets' => [], 'labels' => []];
        }

        $courseIds = Course::where('tutor_id', $tutorId)->pluck('id');

        $totalEnrollments = Enrollment::whereIn('course_id', $courseIds)->count();

        $startedLearning = LessonTracking::whereIn('course_id', $courseIds)
            ->distinct('user_id')
            ->count('user_id');

        $progressOver50 = Enrollment::whereIn('course_id', $courseIds)
            ->where('progress', '>=', 50)
            ->count();

        $progressOver80 = Enrollment::whereIn('course_id', $courseIds)
            ->where('progress', '>=', 80)
            ->count();

        $completed = Enrollment::whereIn('course_id', $courseIds)
            ->whereNotNull('passed_at')
            ->count();

        return [
            'datasets' => [
                [
                    'label' => __('tutor.dashboard.students'),
                    'data' => [$totalEnrollments, $startedLearning, $progressOver50, $progressOver80, $completed],
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(16, 185, 129, 1)',
                    ],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => [
                __('tutor.funnel.enrolled'),
                __('tutor.funnel.started'),
                __('tutor.funnel.progress_50'),
                __('tutor.funnel.progress_80'),
                __('tutor.funnel.completed'),
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'plugins' => ['legend' => ['display' => false]],
            'scales' => ['x' => ['beginAtZero' => true]],
        ];
    }
}
