<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Pages\ListLessons;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Schemas\LessonForm;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Tables\LessonsTable;
use App\Filament\Tutor\Resources\Courses\Resources\Sections\SectionResource;
use App\Http\Resources\Lessons\QuizResource;
use App\Models\Courses\Course;
use App\Models\Courses\CourseSection;
use App\Models\Lessons\Lesson;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LessonResource extends Resource
{
    protected static ?string $model = Lesson::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $parentResource = SectionResource::class;

    protected static ?string $recordTitleAttribute = 'title';

    protected static ?string $relatedResource = QuizResource::class;

    public static function form(Schema $schema): Schema
    {
        return LessonForm::configure($schema);
    }

    public static function getNavigationLabel(): string
    {
        return __('tutor.resources.lessons');
    }

    public static function getModelLabel(): string
    {
        return __('tutor.resources.lesson');
    }

    public static function getPluralModelLabel(): string
    {
        return __('tutor.resources.lessons');
    }

    public static function table(Table $table): Table
    {
        return LessonsTable::configure($table);
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
            'index' => ListLessons::route('/'),

        ];
    }

    public static function getRecordRouteBindingEloquentQuery(): Builder
    {
        return parent::getRecordRouteBindingEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    // Keep this as a safety net, but it should rarely trigger if the Hidden fields work
    protected static function mutateFormDataBeforeCreate(array $data): array
    {
        // If for some reason the hidden fields weren't set (e.g. direct API call),
        // this ensures data integrity.
        if (empty($data['section_id']) && ! empty($data['course_id'])) {
            // This case is unlikely if the form is correct, but good for safety
            return $data;
        }

        if (empty($data['course_id']) && ! empty($data['section_id'])) {
            $section = CourseSection::find($data['section_id']);
            if ($section) {
                $data['course_id'] = $section->course_id;
            }
        }

        return $data;
    }

    public static function getParentId(): string
    {
        return 'course_section';
    }

    public static function getParent()
    {
        // Use the route parameter key defined in getParentId()
        $parentId = request()->route(static::getParentId());
        if (! $parentId) {
            return null;
        }

        return CourseSection::find($parentId);
    }

    public static function getCourse()
    {
        $courseId = request()->route()->parameter('course');

        return $courseId ? Course::where('slug', $courseId)->first() : null;
    }
}
