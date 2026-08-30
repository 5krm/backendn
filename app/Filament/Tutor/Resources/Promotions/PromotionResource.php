<?php

namespace App\Filament\Tutor\Resources\Promotions;

use App\Filament\Tutor\Resources\Promotions\Pages\CreatePromotion;
use App\Filament\Tutor\Resources\Promotions\Pages\EditPromotion;
use App\Filament\Tutor\Resources\Promotions\Pages\ListPromotions;
use App\Filament\Tutor\Resources\Promotions\Schemas\PromotionForm;
use App\Filament\Tutor\Resources\Promotions\Tables\PromotionsTable;
use App\Filament\Tutor\Resources\Promotions\RelationManagers\CoursesRelationManager;
use App\Filament\Tutor\Resources\Promotions\RelationManagers\MyCoursesRelationManager;
use App\Models\Promotion;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PromotionResource extends Resource
{
    protected static ?string $model = Promotion::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $recordTitleAttribute = 'Promotion';

    public static function getNavigationLabel(): string
    {
        return __('tutor.resources.promotions');
    }

    public static function getModelLabel(): string
    {
        return __('tutor.resources.promotion');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tutor.resources.promotions');
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDelete($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canForceDelete($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canForceDeleteAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canRestore($record): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canRestoreAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function form(Schema $schema): Schema
    {
        return PromotionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PromotionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            MyCoursesRelationManager::class,
            CoursesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPromotions::route('/'),
            'create' => CreatePromotion::route('/create'),
            'edit' => EditPromotion::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
