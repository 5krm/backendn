<?php

namespace App\Filament\Tutor\Resources\Courses;

use BackedEnum;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Models\Courses\Course;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Tutor\Resources\Courses\Pages\CreateCourse;
use App\Filament\Tutor\Resources\Courses\Pages\EditCourse;
use App\Filament\Tutor\Resources\Courses\Pages\ListCourses;
use App\Filament\Tutor\Resources\Courses\Schemas\CourseForm;
use App\Filament\Tutor\Resources\Courses\Schemas\CourseWizard;
use App\Filament\Tutor\Resources\Courses\Tables\CoursesTable;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\SectionResource;
use Filament\Schemas\Components\Form;

use App\Filament\Tutor\Resources\Courses\RelationManagers\SectionsRelationManager;

class CourseResource extends Resource
{
    protected static ?string $model = Course::class;
    protected static ?string $relatedResource = SectionResource::class;
    protected static ?string $recordTitleAttribute = 'title';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;
    public static function getNavigationLabel(): string
    {
        return __('tutor.resources.courses');
    }

    public static function getModelLabel(): string
    {
        return __('tutor.resources.course');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tutor.resources.courses');
    }

    public static function form(Schema $schema): Schema
    {
        $operation = $schema->getOperation();
        if ($operation == 'create') {
            return CourseWizard::configure($schema);
        }
        return CourseForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CoursesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCourses::route('/'),
            'create' => CreateCourse::route('/create'),
            'edit' => EditCourse::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('tutor_id', auth()->user()->id);
    }

    public function hasCombinedRelationManagerTabsWithContent(): bool
    {
        return true;
    }
}
