<?php

namespace App\Filament\Tutor\Resources\Courses\Resources\Sections\Pages;

use App\Filament\Tutor\Resources\Courses\Resources\Sections\SectionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\CreateAction;


class EditSection extends EditRecord
{
    protected static string $resource = SectionResource::class;

    public function getTitle(): string
    {
        return __('tutor.resources.section');
    }
    protected function getHeaderActions(): array
    {
        // return [
        //     DeleteAction::make(),
        //     ForceDeleteAction::make(),
        //     RestoreAction::make(),
        // ];
        return [

            CreateAction::make(),

        ];
    }
}
