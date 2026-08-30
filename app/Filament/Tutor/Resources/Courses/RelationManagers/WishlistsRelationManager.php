<?php

namespace App\Filament\Tutor\Resources\Courses\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WishlistsRelationManager extends RelationManager
{
    protected static string $relationship = 'wishlists';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('tutor.form.tab_wishlist');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(
                fn (Builder $query) => $query->with(['user'])
            )
            ->columns([
                ImageColumn::make('user.profile')
                    ->label('')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(asset('assets/images/default-user.png')),

                TextColumn::make('user.name')
                    ->label(__('tutor.students.student'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('user.email')
                    ->label(__('tutor.students.email'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label(__('tutor.form.added_at'))
                    ->date()
                    ->sortable()
                    ->color('secondary'),
            ])
            ->actions([
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading(__('tutor.empty.no_wishlist'))
            ->emptyStateDescription(__('tutor.empty.wishlist_appear_here'))
            ->emptyStateIcon('heroicon-o-heart');
    }
}
