<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\User;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class TopStudentsTable extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public function getHeading(): string
    {
        return __('tutor.widgets.top_students');
    }

    public function table(Table $table): Table
    {
        $tutorId = auth()->user()->id;
        $courseIds = Course::where('tutor_id', $tutorId)->pluck('id');

        return $table
            ->heading(__('tutor.widgets.top_students'))
            ->query(
                User::query()
                    ->whereHas('courses', fn(Builder $q) => $q->whereIn('courses.id', $courseIds))
                    ->withCount([
                        'courses as completed_courses' => fn($q) => $q
                            ->whereIn('courses.id', $courseIds)
                            ->whereNotNull('enrollments.passed_at')
                    ])
                    ->whereHas('courses', fn(Builder $q) => $q
                        ->whereIn('courses.id', $courseIds)
                        ->whereNotNull('enrollments.passed_at')
                    )
                    ->withCount(['courses as enrolled_courses' => fn($q) => $q->whereIn('courses.id', $courseIds)])
                    ->withAvg(['courses as avg_score' => fn($q) => $q->whereIn('courses.id', $courseIds)], 'enrollments.score')
                    ->orderByDesc('completed_courses')
                    ->orderByDesc('avg_score')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\ImageColumn::make('profile')
                    ->label('')
                    ->circular()
                    ->size(32)
                    ->defaultImageUrl(asset('assets/images/Logo_Icon.png')),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('tutor.tables.student'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('enrolled_courses')
                    ->label(__('tutor.tables.enrolled'))
                    ->badge()
                    ->color('info'),
                Tables\Columns\TextColumn::make('completed_courses')
                    ->label(__('tutor.stats.completed'))
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('avg_score')
                    ->label(__('tutor.tables.avg_score'))
                    ->formatStateUsing(fn($state) => $state ? number_format($state, 1) . '%' : 'N/A'),
            ])
            ->emptyStateHeading(__('tutor.empty.no_data'))
            ->emptyStateIcon('heroicon-o-academic-cap')
            ->paginated(false);
    }
}
