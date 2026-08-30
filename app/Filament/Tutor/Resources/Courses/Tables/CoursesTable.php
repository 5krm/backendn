<?php

namespace App\Filament\Tutor\Resources\Courses\Tables;

use App\Enums\CourseStatus;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\Action;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\SectionResource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;

class CoursesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->where('tutor_id', auth()->user()->id))
            ->columns([
                ImageColumn::make('cover_image')
                    ->label(__('tutor.table.cover'))
                    ->circular(),
                TextColumn::make('title')
                    ->label(__('tutor.table.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('category.name')
                    ->label(__('tutor.table.category'))
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('tutor.table.status'))
                    ->badge(),
                TextColumn::make('level')
                    ->label(__('tutor.table.level'))
                    ->badge(),

                ToggleColumn::make('is_free')
                    ->label(__('tutor.table.free')),

                IconColumn::make('is_free')
                    ->label(__('tutor.table.free'))
                    ->boolean(),
                TextColumn::make('price')
                    ->label(__('tutor.table.price'))
                    ->money('USD')
                    ->sortable(),

                TextColumn::make('students_count')
                    ->label(__('tutor.table.students_count'))
                    ->counts('students')
                    ->sortable(),

                TextColumn::make('lessons_count')
                    ->label(__('tutor.table.lessons_count'))
                    ->counts('lessons')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('tutor.table.created_at'))
                    ->dateTime()
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

                SelectFilter::make('category')
                    ->label(__('tutor.filter.category'))
                    ->relationship('category', 'name'),

                SelectFilter::make('is_free')
                    ->label(__('tutor.filter.type'))
                    ->options([
                        1 => __('tutor.table.free'),
                        0 => __('tutor.table.paid'),
                    ]),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make()->before(function (DeleteAction $action, $record) {
                    $hasEnrollments = $record->students()->exists();
                    if ($hasEnrollments) {
                        Notification::make()
                            ->title(__('tutor.delete.has_enrollments_title'))
                            ->body(__('tutor.delete.has_enrollments_body'))
                            ->danger()
                            ->send();

                        $action->cancel();
                    }
                }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->before(function (DeleteBulkAction $action, \Illuminate\Database\Eloquent\Collection $records) {
                            $hasEnrollments = $records->contains(fn($course) => $course->students()->exists());

                            if ($hasEnrollments) {
                                Notification::make()
                                    ->title(__('tutor.delete.has_enrollments_title'))
                                    ->body(__('tutor.delete.has_enrollments_body'))
                                    ->danger()
                                    ->send();

                                $action->cancel();
                            }
                        }),
                ]),
            ])
            ->emptyStateHeading(__('tutor.empty.no_courses'))
            ->defaultSort('created_at', 'desc');
    }
}
