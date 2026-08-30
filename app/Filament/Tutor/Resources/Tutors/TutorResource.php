<?php

namespace App\Filament\Tutor\Resources\Tutors;

use App\Filament\Tutor\RelationManagers\SocialLinksRelationManager;
use App\Filament\Tutor\Resources\Tutors\Pages\CreateTutor;
use App\Filament\Tutor\Resources\Tutors\Pages\EditTutor;
use App\Filament\Tutor\Resources\Tutors\Pages\ListTutors;
use App\Filament\Tutor\Resources\Tutors\Schemas\TutorForm;
use App\Filament\Tutor\Resources\Tutors\Tables\TutorsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TutorResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string
    {
        return __('tutor.nav.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('tutor.tutors._');
    }

    public static function getModelLabel(): string
    {
        return __('tutor.tutors.tutor');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tutor.tutors._');
    }

    public static function form(Schema $schema): Schema
    {
        return TutorForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TutorsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            SocialLinksRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTutors::route('/'),
            'create' => CreateTutor::route('/create'),
            'edit' => EditTutor::route('/{record}/edit'),
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

    // public static function getRecordRouteBindingEloquentQuery(): Builder
    // {
    //     return parent::getRecordRouteBindingEloquentQuery()
    //         ->withoutGlobalScopes([
    //             SoftDeletingScope::class,
    //         ]);
    // }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_tutor', true);
    }
}
