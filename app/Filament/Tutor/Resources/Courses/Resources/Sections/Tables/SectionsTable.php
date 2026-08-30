<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Tables;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\LessonResource;
use App\Models\Lessons\Lesson;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class SectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading(__('tutor.tables.course_sections_heading'))
            ->emptyStateHeading(__('tutor.table.empty_sections_heading'))
            ->emptyStateDescription(__('tutor.table.empty_sections_description'))

            ->headerActions([
                // This triggers the modal
                CreateAction::make()->modalWidth('4xl')
                    ->label(__('tutor.form.add_section'))
                    ->modalWidth('4xl')
                    ->modalHeading(__('tutor.form.add_section')),
            ])
            ->recordActions([
                // Move this to the top of the actions list to ensure it's visible
                Action::make('tutor.form.section_title')
                    ->label(__('tutor.table.manage_lessons'))
                    // ->icon('heroicon-o-academic-cap')
                    ->color('success')
                    ->url(function ($record) {
                        $record->load('course');

                        return LessonResource::getUrl('index', [
                            'course' => $record->course->slug,         // Parameter 1: {course}
                            'course_section' => $record->getKey(),

                        ]);
                    }),

                EditAction::make()->modalHeading(__('tutor.form.edit_section')),
                DeleteAction::make()->requiresConfirmation()
                    ->modalHeading(__('tutor.actions.delete_confirm'))
                    ->modalDescription(__('tutor.actions.delete_confirm_desc'))
                    ->action(function ($record) {
                        $record->delete();
                    }),
            ])
            // ->recordUrl(fn($record) => LessonsTable::getUrl([
            //     'parent' => $record->getKey(),
            // ]))
            ->columns([
                TextColumn::make('title')
                    ->label(__('tutor.form.section_title'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('order')
                    ->label(__('tutor.table.order'))
                    ->sortable(),
                TextColumn::make('lessons_count')
                    ->label(__('tutor.table.lessons_count'))
                    ->counts('lessons')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('tutor.table.created_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // ->filters([
            //     TrashedFilter::make(),
            // ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->reorderable('order')
            ->afterReordering(function (array $order): void {
                foreach ($order as $index => $sectionId) {
                    Lesson::where('section_id', $sectionId)
                        ->update(['section_order' => $index + 1]);
                }
            })
            ->defaultSort('order', 'asc');
    }
}
