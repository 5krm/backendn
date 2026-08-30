<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use Filament\Widgets\ChartWidget;

class CourseScoreDistributionChart extends ChartWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('tutor.widgets.score_distribution');
    }

    protected function getData(): array
    {
        $tutorId = auth()->user()->id;

        if (!$tutorId) {
            return ['datasets' => [], 'labels' => []];
        }

        $courseIds = Course::where('tutor_id', $tutorId)->pluck('id');

        // Only include students who actually attempted (score > 0)
        $baseQuery = fn() => Enrollment::whereIn('course_id', $courseIds)->where('score', '>', 0);

        $ranges = [
            '1-20%'   => [1,  20],
            '21-40%'  => [21, 40],
            '41-60%'  => [41, 60],
            '61-80%'  => [61, 80],
            '81-100%' => [81, 100],
        ];

        $distribution = [];
        foreach ($ranges as $label => [$min, $max]) {
            $distribution[$label] = $baseQuery()
                ->where('score', '>=', $min)
                ->where('score', '<=', $max)
                ->count();
        }

        return [
            'datasets' => [
                [
                    'label' => __('tutor.charts.students'),
                    'data' => array_values($distribution),
                    'backgroundColor' => [
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(34, 197, 94, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                    ],
                ],
            ],
            'labels' => array_keys($distribution),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                ],
            ],
        ];
    }
}
