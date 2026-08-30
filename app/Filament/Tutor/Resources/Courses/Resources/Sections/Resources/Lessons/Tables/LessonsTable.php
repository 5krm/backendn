<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Tables;

use App\Enums\CourseStatus;
use App\Events\CourseLessonsUpdatedEvent;
use App\Events\LessonPublished;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\QuizResource;
use App\Jobs\UploadLessonToYoutube;
use App\Models\Lessons\Lesson;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use SpykApp\UppyUpload\Forms\Components\UppyUpload;

class LessonsTable
{
    public static function configure(Table $table): Table
    {

        return $table
            ->heading(__('tutor.tables.lessons'))

            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with(['course', 'courseSection']);
            })
            ->recordActions([
                Action::make("upload_video")
                    ->label(__("tutor.form.upload_video"))
                    ->schema([
                        UppyUpload::make('video_path')
                            ->label(__("tutor.form.video"))
                            // NOTE: for the video we always use the public disk.
                            ->disk('public')
                            ->directory('LessonVideos')
                            ->chunkSize(5 * 1024 * 1024)
                            ->screenCapture(false)
                            ->audio(false)
                            ->webcam(false)
                            ->multiple(false)
                            ->note(__("tutor.form.video_note")),
                    ])->action(function (Lesson $record, array $data) {
                        $record->video_path = $data["video_path"];
                        $record->is_ready = false;
                        $record->status = CourseStatus::draft;
                        $record->save();
                        UploadLessonToYoutube::dispatch($record);
                    })
                    ->button()
                    ->color("youtube")
                    ->icon(Heroicon::Play),
                Action::make('tutor.form.section_title')
                    ->label(__('tutor.table.manage_quiz'))
                    ->color('success')
                    ->url(function ($record) {
                        $record->load(['course', 'courseSection']);
                        return QuizResource::getUrl('index', [
                            'course' => $record->course->slug,
                            'course_section' => $record->section_id,
                            'lesson' => $record->getKey(),
                        ]);
                    })
                    ->openUrlInNewTab(false),

                ActionGroup::make([
                    EditAction::make()
                        ->mutateRecordDataUsing(function (array $data): array {
                            $data['status'] = $data['status'] === CourseStatus::published->value
                                || $data['status'] === CourseStatus::published;
                            return $data;
                        })
                        ->modalWidth('4xl')
                        ->after(function ($record) {
                            event(new CourseLessonsUpdatedEvent($record->course_id));
                        }),
                    DeleteAction::make()
                        ->after(function ($record) {
                            event(new CourseLessonsUpdatedEvent($record->course_id));
                        }),
                ]),

            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with(['course', 'courseSection']);
            })
            ->columns([
                TextColumn::make('title')
                    ->label(__('tutor.form.lesson_title'))
                    ->searchable(),
                TextColumn::make('trackings_count')
                    ->label(__('tutor.tables.views'))
                    ->counts('trackings'),
                TextColumn::make('duration')
                    ->label(__('tutor.form.duration'))
                    ->formatStateUsing(fn($state) => $state ? $state . ' ' . __('tutor.form.duration_minutes') : '-')
                    ->sortable(),

                IconColumn::make('is_ready')
                    ->label(__('tutor.table.video_status'))
                    ->icon(fn(Lesson $record): string => $record->is_ready ? 'heroicon-o-check-circle' : 'heroicon-o-arrow-path')
                    ->color(fn(Lesson $record): string => $record->is_ready ? 'success' : 'warning')
                    ->extraAttributes(fn(Lesson $record): array => [
                        'class' => $record->is_ready ? '' : '[&_svg]:animate-spin',
                    ])
                    ->tooltip(fn(Lesson $record): string => $record->is_ready
                        ? __('tutor.table.video_ready')
                        : __('tutor.table.video_uploading')),

                TextColumn::make('toggle_status')
                    ->label(__('tutor.table.status_toggle'))
                    ->state(function (Lesson $record): string {
                        return __('course.change_to_status', [
                            'status' => self::getToggleStatus($record)->getLabel(),
                        ]);
                    })
                    ->badge()
                    ->size("lg")
                    ->color(fn(Lesson $record): string => self::getToggleStatus($record)->getColor())
                    ->extraAttributes(fn(Lesson $record): array => [
                        'class' => 'lesson-status-toggle ' . ($record->is_ready ? 'is-ready' : 'is-disabled'),
                    ])
                    ->action(function (Lesson $record): void {
                        if (!$record->is_ready) return;
                        $record->status = self::getToggleStatus($record);
                        $record->save();
                        if ($record->status == CourseStatus::published) {
                            event(new LessonPublished($record));
                        }
                    })
                    ->tooltip(fn($record) => $record->is_ready ? false : __("tutor.table.status_toggle_note")),


                TextColumn::make('created_at')
                    ->label(__('tutor.table.created_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('tutor.filter.status'))
                    ->options([
                        CourseStatus::draft->value => __('tutor.form.draft'),
                        CourseStatus::published->value => __('tutor.form.published'),
                    ]),

            ])
            ->recordAction(EditAction::class)
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->after(function ($selectedRecords) {
                            event(new CourseLessonsUpdatedEvent($selectedRecords[0]->course_id));
                        })
                ]),
            ])
            ->reorderable('lesson_order')
            ->defaultSort('lesson_order', 'asc')
            ->poll(fn(Table $table) => $table->getRecords()->contains('is_ready', false) ? '10s' : null);
    }

    private static function getToggleStatus(Lesson $lesson): CourseStatus
    {
        return $lesson->status === CourseStatus::draft
            ? CourseStatus::published
            : CourseStatus::draft;
    }
}
