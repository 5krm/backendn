<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\Lessons\LessonTracking;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class LessonViewsChart extends ChartWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 'full';

    protected ?string $maxHeight = '300px';

    public function getHeading(): string
    {
        return __('tutor.widgets.lesson_views');
    }

    protected function getData(): array
    {
        $tutorId = auth()->user()->id;

        if (!$tutorId) {
            return ['datasets' => [], 'labels' => []];
        }

        $courseIds = Course::where('tutor_id', $tutorId)->pluck('id');

        $views = LessonTracking::whereIn('course_id', $courseIds)
            ->where('created_at', '>=', now()->subDays(30))
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as views'),
                DB::raw('COUNT(CASE WHEN completed_at IS NOT NULL THEN 1 END) as completions')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $dates = collect();
        $viewsData = collect();
        $completionsData = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $dayData = $views->firstWhere('date', $date);

            $dates->push(now()->subDays($i)->format('M d'));
            $viewsData->push($dayData?->views ?? 0);
            $completionsData->push($dayData?->completions ?? 0);
        }

        return [
            'datasets' => [
                [
                    'label' => __('tutor.tables.views'),
                    'data' => $viewsData->toArray(),
                    'borderColor' => 'rgba(59, 130, 246, 1)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
                ],
                [
                    'label' => __('tutor.tables.completions'),
                    'data' => $completionsData->toArray(),
                    'borderColor' => 'rgba(16, 185, 129, 1)',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'fill' => true,
                    'tension' => 0.3,
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
                'legend' => ['display' => true, 'position' => 'top'],
            ],
            'scales' => [
                'y' => ['beginAtZero' => true],
            ],
        ];
    }
}
