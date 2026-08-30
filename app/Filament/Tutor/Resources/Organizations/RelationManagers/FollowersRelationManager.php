<?php

namespace App\Filament\Tutor\Resources\Organizations\RelationManagers;

use App\Filament\Tutor\Resources\Organizations\OrganizationResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class FollowersRelationManager extends RelationManager
{
    protected static string $relationship = 'followers';

    protected static ?string $relatedResource = OrganizationResource::class;

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('tutor.form.tab_followers');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->heading(__('tutor.tables.organization_followers'))
            ->columns([
                ImageColumn::make('profile')
                    ->label('')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(asset('assets/images/default-user.png')),

                TextColumn::make('name')
                    ->label(__('tutor.students.student'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('email')
                    ->label(__('tutor.students.email'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('pivot.created_at')
                    ->label(__('tutor.form.followed_at'))
                    ->date()
                    ->sortable()
                    ->color('secondary'),
            ])
            ->headerActions([])
            ->recordActions([])
            ->toolbarActions([])
            ->defaultSort('organization_followers.created_at', 'desc')
            ->emptyStateHeading(__('tutor.empty.no_followers'))
            ->emptyStateDescription(__('tutor.empty.followers_appear_here'))
            ->emptyStateIcon('heroicon-o-user-group');
    }
}
