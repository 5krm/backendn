<?php

namespace App\Filament\Tutor\Widgets;

use App\Enums\CourseStatus;
use App\Models\Courses\Course;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class CoursesNeedingAttentionWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 1;

    protected static ?int $sort = 2;

    public function getHeading(): string
    {
        return __('tutor.widgets.courses_needing_attention');
    }

    public function table(Table $table): Table
    {
        $tutorId = auth()->user()->id;

        return $table
            ->query(
                Course::query()
                    ->where('tutor_id', $tutorId)
                    ->where(function ($query) {
                        $query->where('status', CourseStatus::draft)
                            ->orWhereDoesntHave('lessons')
                            ->orWhereDoesntHave('quizzes')
                            ->orWhereNull('description')
                            ->orWhere('description', '')
                            ->orWhereNull('objectives')
                            ->orWhere('objectives', '');
                    })
                    ->withCount(['lessons', 'quizzes', 'students'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(8)
            )
            ->heading(__('tutor.widgets.courses_needing_attention'))
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label(__('tutor.tables.course'))
                    ->limit(25)
                    ->url(fn($record) => route('filament.tutor.resources.courses.edit', $record))
                    ->color('primary'),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('tutor.tables.status'))
                    ->badge()
                    ->color(fn($state) => $state->getColor()),
                Tables\Columns\TextColumn::make('issue')
                    ->label(__('tutor.tables.issue'))
                    ->getStateUsing(function ($record) {
                        $issues = [];
                        if ($record->status === CourseStatus::draft) {
                            $issues[] = __('tutor.issues.draft');
                        }
                        if ($record->lessons_count === 0) {
                            $issues[] = __('tutor.issues.no_lessons');
                        }
                        if ($record->quizzes_count === 0) {
                            $issues[] = __('tutor.issues.no_quizzes');
                        }
                        if (empty($record->description)) {
                            $issues[] = __('tutor.issues.no_description');
                        }
                        if (empty($record->objectives)) {
                            $issues[] = __('tutor.issues.no_objectives');
                        }
                        return implode(', ', $issues) ?: __('tutor.issues.ok');
                    })
                    ->badge()
                    ->color('warning'),
                Tables\Columns\TextColumn::make('students_count')
                    ->label(__('tutor.tables.student'))
                    ->badge()
                    ->color('info'),
            ])
            ->paginated(false)
            ->emptyStateHeading(__('tutor.empty.all_courses_good'))
            ->emptyStateDescription(__('tutor.empty.no_courses_need_attention'))
            ->emptyStateIcon('heroicon-o-check-circle')
            ->recordUrl(fn($record) => route('filament.tutor.resources.courses.edit', $record));
    }
}
