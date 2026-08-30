<?php

namespace App\Filament\Tutor\Widgets;

use App\Models\Courses\Course;
use App\Models\Lessons\Lesson;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\LessonResource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentlyUpdatedWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 1;

    public function getHeading(): string
    {
        return __('tutor.widgets.recently_updated');
    }

    public function table(Table $table): Table
    {
        $tutorId = auth()->user()->id;

        return $table
            ->query(
                Lesson::query()
                    ->with('course')
                    ->whereHas('course', fn(Builder $q) => $q->where('tutor_id', $tutorId))
                    ->orderByDesc('updated_at')
                    ->limit(8)
            )
            ->heading(__('tutor.widgets.recently_updated'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tutor.tables.lesson'))
                    ->limit(30)
                    ->url(fn($record) => LessonResource::getUrl('index', [
                        'course' => $record->course->slug,
                        'course_section' => $record->section_id,
                        'lesson' => $record->getKey(), // Ensure 'section_id' exists on Lesson model
                    ]))
                    ->color('primary'),
                Tables\Columns\TextColumn::make('course.title')
                    ->label(__('tutor.tables.course'))
                    ->limit(20)
                    ->color('gray'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('tutor.tables.status'))
                    ->badge()
                    ->color(fn($state) => $state->getColor()),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('tutor.tables.updated'))
                    ->since()
                    ->color('gray'),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('tutor.empty.no_lessons'))
            ->emptyStateIcon('heroicon-o-book-open')->recordUrl(fn($record) => LessonResource::getUrl('index', [
                'course' => $record->course->slug,
                'course_section' => $record->section_id,
                'lesson' => $record->getKey(),
            ]));
    }
}
