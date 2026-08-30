<?php

namespace App\Filament\Tutor\Resources\Organizations;

use App\Filament\Tutor\Resources\Organizations\Pages\CreateOrganization;
use App\Filament\Tutor\Resources\Organizations\Pages\EditOrganization;
use App\Filament\Tutor\Resources\Organizations\Pages\ListOrganizations;
use App\Filament\Tutor\Resources\Organizations\RelationManagers\CoursesRelationManager;
use App\Filament\Tutor\Resources\Organizations\RelationManagers\UsersRelationManager;
use App\Filament\Tutor\Resources\Organizations\Schemas\OrganizationForm;
use App\Filament\Tutor\Resources\Organizations\Tables\OrganizationsTable;
use App\Models\Organization;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class OrganizationResource extends Resource
{
    protected static ?string $model = Organization::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string
    {
        return __('tutor.nav.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('tutor.nav.organizations');
    }

    public static function form(Schema $schema): Schema
    {
        return OrganizationForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OrganizationsTable::configure($table);
    }

    public static function getModelLabel(): string
    {
        return __('tutor.resources.organization');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tutor.resources.organizations');
    }
    // public static function getRelations(): array
    // {
    //      return [
    //             UsersRelationManager::class,
    //             CoursesRelationManager::class,
    // ];
    // }

    public static function getPages(): array
    {
        return [
            'index' => ListOrganizations::route('/'),
            'create' => CreateOrganization::route('/create'),
            'edit' => EditOrganization::route('/{record}/edit'),
        ];
    }

    public static function canViewAny(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canCreate(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public static function canEdit($record): bool
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
}
