<?php

namespace App\Filament\Tutor\Widgets;

use App\Enums\CourseStatus;
use App\Models\Courses\Course;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TopCoursesWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 5;

    public function getHeading(): string
    {
        return __('tutor.widgets.top_courses');
    }

    public function table(Table $table): Table
    {
        $tutorId = auth()->user()->id;

        return $table
            ->query(
                Course::query()
                    ->where('tutor_id', $tutorId)
                    ->where('status', CourseStatus::published)
                    ->withCount('students')
                    ->orderByDesc('students_count')
                    ->limit(5)
            )
            ->heading(__('tutor.widgets.top_courses'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tutor.tables.course'))
                    ->limit(35)
                    ->tooltip(fn($record) => $record->title)
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('students_count')
                    ->label(__('tutor.tables.students'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label(__('tutor.tables.price'))
                    ->formatStateUsing(fn($state, $record) => $record->is_free 
                        ? __('tutor.tables.free') 
                        : '$' . number_format($state, 2))
                    ->badge()
                    ->color(fn($record) => $record->is_free ? 'primary' : 'secondary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('tutor.tables.created'))
                    ->date('M d, Y')
                    ->sortable()
                    ->color('secondary'),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('tutor.empty.no_published_courses'))
            ->emptyStateDescription(__('tutor.empty.publish_course_to_see'))
            ->emptyStateIcon('heroicon-o-academic-cap');
    }
}
