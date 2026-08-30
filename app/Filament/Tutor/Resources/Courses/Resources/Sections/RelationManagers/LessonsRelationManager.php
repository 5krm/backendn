<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\RelationManagers;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\LessonResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table; // Import Schema

class LessonsRelationManager extends RelationManager
{
    protected static string $relationship = 'lessons';

    protected static ?string $relatedResource = LessonResource::class;

    public function form(Schema $schema): Schema
    {
        // Reuse the LessonResource form
        return LessonResource::form($schema);
    }

    public function table(Table $table): Table
    {
        // Reuse the LessonResource table
        return LessonResource::table($table);
    }
}
