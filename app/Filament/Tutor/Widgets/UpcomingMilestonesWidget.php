<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Enrollment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingMilestonesWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return __('tutor.widgets.upcoming_milestones');
    }

    public function table(Table $table): Table
    {
        $tutorId = auth()->user()->id;

        return $table
            ->query(
                Enrollment::query()
                    ->with(['user:id,name', 'course:id,title'])
                    ->whereHas('course', function ($query) use ($tutorId) {
                        $query->where('tutor_id', $tutorId);
                    })
                    ->whereBetween('progress', [80, 99.99])
                    ->orderByDesc('progress')
                    ->limit(10)
            )
            ->heading(__('tutor.widgets.upcoming_milestones'))
            ->columns([
                Tables\Columns\ImageColumn::make('user.profile')
                    ->label('')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(asset('assets/images/Logo_Icon.png')),

                Tables\Columns\TextColumn::make('user.name')
                    ->label(__('tutor.tables.student'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(25),

                Tables\Columns\TextColumn::make('course.title')
                    ->label(__('tutor.tables.course'))
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->course->title)
                    ->color('secondary'),

                Tables\Columns\TextColumn::make('progress')
                    ->label(__('tutor.tables.progress'))
                    ->formatStateUsing(fn ($state) => round($state).'%')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 95 => 'primary',
                        $state >= 90 => 'primary',
                        default => 'primary'
                    }),

                Tables\Columns\TextColumn::make('remaining')
                    ->label(__('tutor.tables.to_go'))
                    ->getStateUsing(fn ($record) => round(100 - $record->progress).'%')
                    ->badge()
                    ->color('secondary'),
            ])
            ->paginated(false)
            ->poll('60s')
            ->emptyStateHeading(__('tutor.empty.no_students_near_completion'))
            ->emptyStateDescription(__('tutor.empty.students_90_progress'))
            ->emptyStateIcon('heroicon-o-academic-cap');
    }
}
