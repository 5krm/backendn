<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\RelationManagers;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\QuizResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Schemas\Schema; // Import Schema
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;


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
