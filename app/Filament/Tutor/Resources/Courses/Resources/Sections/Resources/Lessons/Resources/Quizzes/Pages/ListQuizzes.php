<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\Pages;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\QuizResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\CreateAction;


class ListQuizzes extends ListRecords
{
    protected static string $resource = QuizResource::class;
    protected function getHeaderActions(): array
    {
        return [
            // This is required to make the "Create" button appear on the new page
            // CreateAction::make(),
        ];
    }
}
