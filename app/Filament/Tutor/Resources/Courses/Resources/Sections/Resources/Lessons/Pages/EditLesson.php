<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Pages;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\LessonResource;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditLesson extends EditRecord
{
    protected static string $resource = LessonResource::class;

    protected function getHeaderActions(): array
    {
        return [

            CreateAction::make(),

        ];
    }
}
