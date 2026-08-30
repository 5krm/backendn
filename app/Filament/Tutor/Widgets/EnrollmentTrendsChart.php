<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class EnrollmentTrendsChart extends ChartWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('tutor.widgets.enrollment_trends');
    }

    protected function getData(): array
    {
        $tutorId = auth()->user()->id;

        if (!$tutorId) {
            return ['datasets' => [], 'labels' => []];
        }

        $courseIds = Course::where('tutor_id', $tutorId)->pluck('id');

        // Get daily enrollments for last 30 days
        $enrollments = Enrollment::whereIn('course_id', $courseIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('count', 'date');

        // Fill in missing dates
        $dates = collect();
        $counts = collect();
        $cumulative = collect();
        $runningTotal = Enrollment::whereIn('course_id', $courseIds)
            ->where('created_at', '<', now()->subDays(29)->startOfDay())
            ->count();

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $count = $enrollments[$date] ?? 0;
            $runningTotal += $count;
            
            $dates->push(now()->subDays($i)->format('M d'));
            $counts->push($count);
            $cumulative->push($runningTotal);
        }

        return [
            'datasets' => [
                [
                    'label' => __('tutor.charts.new_enrollments'),
                    'data' => $counts->toArray(),
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                    'yAxisID' => 'y',
                ],
                [
                    'label' => __('tutor.charts.total_students'),
                    'data' => $cumulative->toArray(),
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'backgroundColor' => 'transparent',
                    'borderDash' => [5, 5],
                    'tension' => 0.3,
                    'yAxisID' => 'y1',
                ],
            ],
            'labels' => $dates->toArray(),
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'top',
                ],
            ],
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'position' => 'left',
                    'title' => [
                        'display' => true,
                        'text' => __('tutor.charts.daily_enrollments'),
                    ],
                ],
                'y1' => [
                    'beginAtZero' => true,
                    'position' => 'right',
                    'grid' => ['drawOnChartArea' => false],
                    'title' => [
                        'display' => true,
                        'text' => __('tutor.charts.total_students'),
                    ],
                ],
            ],
        ];
    }
}
