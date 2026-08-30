<?php

namespace App\Filament\Tutor\Widgets;

use App\Enums\CourseStatus;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\LessonResource;
use App\Models\Lessons\Lesson;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class DraftContentWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 3;

    public function getHeading(): string
    {
        return __('tutor.widgets.draft_content');
    }

    public function table(Table $table): Table
    {
        $tutorId = auth()->user()->id;

        return $table
            ->query(
                Lesson::query()
                    ->with('course')
                    ->whereHas('course', fn (Builder $q) => $q->where('tutor_id', $tutorId))
                    ->where('status', CourseStatus::draft)
                    ->orderByDesc('updated_at')
                    ->limit(8)
            )
            ->heading(__('tutor.widgets.draft_content'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tutor.tables.lesson'))
                    ->limit(30)
                    ->url(fn ($record) => LessonResource::getUrl('index', [
                        'course' => $record->course->slug,
                        'course_section' => $record->section_id,
                        'lesson' => $record->getKey(), // Ensure 'section_id' exists on Lesson model
                    ]))->color('primary'),
                Tables\Columns\TextColumn::make('course.title')
                    ->label(__('tutor.tables.course'))
                    ->limit(20)
                    ->color('gray'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('tutor.tables.created'))
                    ->since()
                    ->color('gray'),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('tutor.empty.no_draft_content'))
            ->emptyStateDescription(__('tutor.empty.all_lessons_published'))
            ->emptyStateIcon('heroicon-o-check-circle')
            ->recordUrl(fn ($record) => LessonResource::getUrl('index', [
                'course' => $record->course->slug,
                'course_section' => $record->section_id,
                'lesson' => $record->getKey(),
            ]));
    }
}
