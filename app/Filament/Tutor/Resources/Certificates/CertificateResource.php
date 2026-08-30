<?php

namespace App\Filament\Tutor\Resources\Certificates;

use App\Filament\Tutor\Resources\Certificates\Pages\ListCertificates;
use App\Filament\Tutor\Resources\Certificates\Schemas\CertificateForm;
use App\Filament\Tutor\Resources\Certificates\Tables\CertificatesTable;
use App\Models\Certificate;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CertificateResource extends Resource
{
    protected static ?string $model = Certificate::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    public static function getNavigationLabel(): string
    {
        return __('tutor.resources.certificates');
    }

    public static function getModelLabel(): string
    {
        return __('tutor.resources.certificate');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tutor.resources.certificates');
    }

    protected static ?string $recordTitleAttribute = 'Certificate';

    public static function form(Schema $schema): Schema
    {
        return CertificateForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CertificatesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCertificates::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false; // Certificates are automatically generated
    }

    public static function canEdit($record): bool
    {
        return false; // Certificates cannot be edited
    }

    public static function canDelete($record): bool
    {
        return false; // Certificates cannot be deleted
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tutor_id', auth()->user()->id);
    }
}
