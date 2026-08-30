<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\Pages;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Resources\Quizzes\QuizResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditQuiz extends EditRecord
{
    protected static string $resource = QuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
