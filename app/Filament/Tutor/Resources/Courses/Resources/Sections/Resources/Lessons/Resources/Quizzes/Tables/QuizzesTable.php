<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Table;

class QuizzesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make()
                    ->label(__('tutor.form.add_quiz'))
                    ->modalHeading(__('tutor.form.add_quiz'))
                    // ->modalDescription(__('tutor.form.create_quiz_description'))
                    ->modalWidth('4xl'),
            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query
                    ->with(['course', 'lesson'])
                    ->where('tutor_id', auth()->user()->id);
            })
            ->columns([
                TextColumn::make('question')
                    ->label(__('tutor.table.title'))
                    ->searchable()
                    ->sortable(),

                // TextColumn::make('course.title')
                //     ->label(__('tutor.resources.course'))
                //     ->sortable(),

                // TextColumn::make('lesson.title')
                //     ->label(__('tutor.resources.lesson'))
                //     ->sortable()
                //     ->default('—'),

                TextColumn::make('created_at')
                    ->label(__('tutor.table.created_at'))
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            // ->filters([
            //     SelectFilter::make('course')
            //         ->label(__('tutor.filter.course'))
            //         ->relationship('course', 'title'),
            // ])
            ->recordActions([
                // Edit Action opens in a modal
                EditAction::make()
                    ->modalHeading(__('tutor.form.edit_quiz'))
                    ->modalWidth('4xl')
                    ->slideOver(false),

                DeleteAction::make()->requiresConfirmation()
                    ->modalHeading(__('tutor.actions.delete_confirm'))
                    ->modalDescription(__('tutor.actions.delete_confirm_desc'))
                    ->action(function ($record) {
                        $record->delete();
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->reorderable('order')
            ->defaultSort('order', 'asc');
    }
}
