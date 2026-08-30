<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\Lessons\Lesson;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopLessonsTable extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 5;

    public function getHeading(): string
    {
        return __('tutor.widgets.top_lessons');
    }

    public function table(Table $table): Table
    {
        $tutorId = auth()->user()->id;

        return $table
            ->heading(__('tutor.widgets.top_lessons'))
            ->query(
                Lesson::query()
                    ->whereHas('course', fn(Builder $query) => $query->where('tutor_id', $tutorId))
                    ->withCount('trackings')
                    ->withCount(['trackings as completions_count' => fn($q) => $q->whereNotNull('completed_at')])
                    ->orderByDesc('trackings_count')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tutor.tables.lesson'))
                    ->limit(40)
                    ->searchable(),
                Tables\Columns\TextColumn::make('course.title')
                    ->label(__('tutor.tables.course'))
                    ->limit(30),
                Tables\Columns\TextColumn::make('trackings_count')
                    ->label(__('tutor.tables.views'))
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('completions_count')
                    ->label(__('tutor.tables.completions'))
                    ->sortable()
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('completion_rate')
                    ->label(__('tutor.tables.completion_rate'))
                    ->getStateUsing(function ($record) {
                        if ($record->trackings_count === 0) return '0%';
                        return round(($record->completions_count / $record->trackings_count) * 100, 1) . '%';
                    })
                    ->badge()
                    ->color(fn($state) => (float)$state >= 70 ? 'success' : ((float)$state >= 40 ? 'warning' : 'danger')),
            ])
            ->emptyStateHeading(__('tutor.empty.no_lessons'))
            ->emptyStateIcon('heroicon-o-book-open')
            ->paginated(false);
    }
}
