<?php

namespace App\Filament\Tutor\Resources\Students;

use App\Filament\Tutor\Resources\Students\Pages\ListStudents;
use App\Filament\Tutor\Resources\Students\Pages\ViewStudent;
use App\Filament\Tutor\Resources\Students\Schemas\StudentForm;
use App\Filament\Tutor\Resources\Students\Schemas\StudentInfolist;
use App\Filament\Tutor\Resources\Students\Tables\StudentsTable;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StudentResource extends Resource
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
        return __('tutor.students._');
    }

    public static function getModelLabel(): string
    {
        return __('tutor.students.student');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tutor.students._');
    }

    public static function form(Schema $schema): Schema
    {
        return StudentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return StudentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return StudentsTable::configure($table);
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
            'index' => ListStudents::route('/'),
            // 'view' => ViewStudent::route('/{record}'),
        ];
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
        $user = auth()->user();
        if ($user->isAdmin()) {
            return parent::getEloquentQuery()
                ->with('courses')
                ->withCount('courses');
        }

        return parent::getEloquentQuery()
            ->whereHas('courses', fn ($q) => $q->where('tutor_id', $user->id))
            ->with(['courses' => fn ($q) => $q->where('tutor_id', $user->id)])
            ->withCount('courses');
    }
}
