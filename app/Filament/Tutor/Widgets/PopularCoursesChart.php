<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use Filament\Widgets\ChartWidget;

class PopularCoursesChart extends ChartWidget
{
    public function getHeading(): string
    {
        return __('tutor.widgets.popular_courses');
    }

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $tutorId = auth()->user()->id;

        if (!$tutorId) {
            return [
                'datasets' => [],
                'labels' => [],
            ];
        }

        $courses = Course::where('tutor_id', $tutorId)
            ->withCount('students')
            ->orderByDesc('students_count')
            ->limit(8)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => __('tutor.tables.enrolled'),
                    'data' => $courses->pluck('students_count')->toArray(),
                    'backgroundColor' => [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                        'rgba(236, 72, 153, 0.8)',
                        'rgba(20, 184, 166, 0.8)',
                        'rgba(249, 115, 22, 0.8)',
                    ],
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $courses->pluck('title')->map(fn($title) => \Illuminate\Support\Str::limit($title, 20))->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => false,
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'stepSize' => 1,
                    ],
                ],
            ],
        ];
    }
}
