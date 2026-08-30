<?php

namespace App\Filament\Tutor\Resources\Students\Pages;

use App\Filament\Tutor\Resources\Students\StudentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStudent extends CreateRecord
{
    protected static string $resource = StudentResource::class;
}
