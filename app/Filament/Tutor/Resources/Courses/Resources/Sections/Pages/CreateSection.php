<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Pages;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\SectionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateSection extends CreateRecord
{
    protected static string $resource = SectionResource::class;

    public function getTitle(): string
    {
        return __('tutor.resources.section');
    }
}
