<?php

namespace App\Filament\Tutor\Resources\Organizations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class OrganizationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->heading(__('tutor.tables.organizations'))

            ->columns([
                TextColumn::make('name')
                    ->label(__('tutor.form.name'))
                    ->searchable(),
                TextColumn::make('slug')
                     ->label(__('tutor.form.slug'))
                    ->searchable(),
                TextColumn::make('category')
                    ->label(__('tutor.form.category'))
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('founded')
                    ->label(__('tutor.form.founded'))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('website')
                    ->label(__('tutor.form.website'))
                    ->url(fn ($record) => $record->website)
                    ->openUrlInNewTab()
                    ->limit(30)
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->label(__('tutor.form.active'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
