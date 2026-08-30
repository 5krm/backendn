<?php

namespace App\Filament\Tutor\Resources\Promotions\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('tutor.promotions.courses');
    }
    public function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('cover')
                    ->label(__('tutor.table.cover'))
                    ->circular()
                    ->defaultImageUrl(url('/assets/images/default-course.png')),

                TextColumn::make('title')
                    ->label(__('tutor.table.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label(__('tutor.table.status'))
                    ->badge(),
                TextColumn::make('price')
                    ->label(__('tutor.table.price'))
                    ->money('USD')
                    ->sortable(),
            ]);
    }
}
