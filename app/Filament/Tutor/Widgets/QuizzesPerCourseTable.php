<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class QuizzesPerCourseTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 4;

    public function getHeading(): string
    {
        return __('tutor.widgets.quiz_summary');
    }

    public function table(Table $table): Table
    {
        $tutorId = auth()->user()->id;

        return $table
            ->query(
                Course::query()
                    ->where('tutor_id', $tutorId)
                    ->withCount('quizzes')
                    ->withCount([
                        // Students who attempted (score > 0)
                        'students as attempted_count' => fn ($q) => $q->where('enrollments.score', '>', 0),
                        // Students who passed
                        'students as passed_count' => fn ($q) => $q->whereNotNull('enrollments.passed_at'),
                    ])
                    ->withAvg(
                        ['students as avg_score' => fn ($q) => $q->where('enrollments.score', '>', 0)],
                        'enrollments.score'
                    )
                    ->orderByDesc('attempted_count')
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tutor.tables.course'))
                    ->limit(35)
                    ->searchable(),
                Tables\Columns\TextColumn::make('quizzes_count')
                    ->label(__('tutor.tables.quizzes'))
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('attempted_count')
                    ->label(__('tutor.tables.students'))
                    ->sortable()
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('passed_count')
                    ->label(__('tutor.tables.passed'))
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('pass_rate')
                    ->label(__('tutor.tables.pass_rate'))
                    ->getStateUsing(function ($record) {
                        if ($record->attempted_count === 0) {
                            return '0%';
                        }

                        return round(($record->passed_count / $record->attempted_count) * 100, 1).'%';
                    })
                    ->badge()
                    ->color(fn ($state) => (float) $state >= 70 ? 'success' : ((float) $state >= 50 ? 'warning' : 'danger')),
                Tables\Columns\TextColumn::make('avg_score')
                    ->label(__('tutor.tables.avg_score'))
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1).'%' : 'N/A')
                    ->sortable(),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('tutor.empty.no_courses_with_quizzes'))
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->heading(__('tutor.widgets.quiz_summary'));
    }
}
