<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Str;

class QuizPassRateChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 1;

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('tutor.widgets.pass_rate_by_course');
    }

    protected function getData(): array
    {
        $tutorId = auth()->user()->id;

        if (! $tutorId) {
            return ['datasets' => [], 'labels' => []];
        }

        $courses = Course::where('tutor_id', $tutorId)
            ->withCount([
                // Students who attempted (score > 0)
                'students as attempted_students' => fn ($q) => $q->where('enrollments.score', '>', 0),
                // Students who passed
                'students as passed_students' => fn ($q) => $q->whereNotNull('enrollments.passed_at'),
            ])
            ->having('attempted_students', '>', 0)
            ->orderByDesc('attempted_students')
            ->limit(8)
            ->get();

        $passRates = $courses->map(function ($course) {
            return $course->attempted_students > 0
                ? round(($course->passed_students / $course->attempted_students) * 100, 1)
                : 0;
        });

        return [
            'datasets' => [
                [
                    'label' => __('tutor.charts.pass_rate_percent'),
                    'data' => $passRates->toArray(),
                    'backgroundColor' => $passRates->map(function ($rate) {
                        if ($rate >= 70) {
                            return 'rgba(16, 185, 129, 0.8)';
                        }
                        if ($rate >= 50) {
                            return 'rgba(245, 158, 11, 0.8)';
                        }

                        return 'rgba(239, 68, 68, 0.8)';
                    })->toArray(),
                    'borderRadius' => 6,
                ],
            ],
            'labels' => $courses->pluck('title')->map(fn ($t) => Str::limit($t, 15))->toArray(),
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
                'legend' => ['display' => false],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => 100,
                    'ticks' => ['callback' => "function(value) { return value + '%'; }"],
                ],
            ],
        ];
    }
}
