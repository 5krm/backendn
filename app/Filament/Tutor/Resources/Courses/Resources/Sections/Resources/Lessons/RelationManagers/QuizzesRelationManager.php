<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\RelationManagers;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\QuizResource;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Table; // Import Schema

class QuizzesRelationManager extends RelationManager
{
    protected static string $relationship = 'quizzes';

    protected static ?string $title = 'Quizzes';

    protected static ?string $relatedResource = QuizResource::class;

    public function form(Schema $schema): Schema
    {
        // Reuse the LessonResource form
        return QuizResource::form($schema);
    }

    public function table(Table $table): Table
    {
        // Reuse the LessonResource table
        return QuizResource::table($table);
    }
}
