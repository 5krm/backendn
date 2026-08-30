<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\Pages;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\QuizResource;
use Filament\Resources\Pages\CreateRecord;

class CreateQuiz extends CreateRecord
{
    protected static string $resource = QuizResource::class;
}
