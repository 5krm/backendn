<?php

namespace App\Filament\Tutor\Resources\Tutors\Pages;

use App\Filament\Tutor\Resources\Tutors\TutorResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditTutor extends EditRecord
{
    protected static string $resource = TutorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }

    protected function beforeFill(): void
    {
        $this->record->tutorProfile()->firstOrCreate([]);
    }
}
