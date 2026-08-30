<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\Pages;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\Resources\Lessons\LessonResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLesson extends CreateRecord
{
    protected static string $resource = LessonResource::class;
}
