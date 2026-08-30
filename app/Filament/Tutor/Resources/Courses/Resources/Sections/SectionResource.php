<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections;

use App\Filament\Tutor\Resources\Courses\CourseResource;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\Pages\ListSections;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\LessonResource;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\Schemas\SectionForm;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\Tables\SectionsTable;
use App\Models\Courses\CourseSection;
use BackedEnum;
use Filament\Resources\ParentResourceRegistration;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SectionResource extends Resource
{
    protected static ?string $model = CourseSection::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = CourseResource::class;

    protected static ?string $slug = 'sections';

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $relatedResource = LessonResource::class;

    public static function form(Schema $schema): Schema
    {
        return SectionForm::configure($schema);
    }

    public static function getNavigationLabel(): string
    {
        return __('tutor.resources.sections');
    }

    public static function getModelLabel(): string
    {
        return __('tutor.resources.section');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tutor.resources.sections');
    }

    public static function getParentResourceRegistration(): ?ParentResourceRegistration
    {
        return CourseResource::asParent(childResource: static::class)
            ->relationship('sections');
    }

    public static function table(Table $table): Table
    {
        return SectionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [

            'index' => ListSections::route('/'),

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
