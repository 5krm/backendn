<?php

namespace App\Filament\Tutor\Resources\Organizations\RelationManagers;

use App\Filament\Tutor\Resources\Organizations\OrganizationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
 use Filament\Tables\Columns\ImageColumn;
use Illuminate\Database\Eloquent\Builder;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    protected static ?string $relatedResource = OrganizationResource::class;

    public function table(Table $table): Table
    {
         return $table
            ->heading(__('tutor.tables.organization_courses'))

            ->modifyQueryUsing(fn (Builder $query) => $query)

            ->columns([
                TextColumn::make('title')
                    ->label(__('tutor.table.title'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('tutor.table.created_at'))
                    ->dateTime()                    ->dateTime()
                    ->sortable(),

                // TextColumn::make('updated_at')
                //     ->label(__('tutor.table.updated_at'))
                //     ->dateTime()
                //     ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('organization.no_courses'))
            ->emptyStateDescription(__('tutor.empty.organization_courses_appear_here'))
            ->emptyStateIcon('heroicon-o-square-3-stack-3d');
    }
}