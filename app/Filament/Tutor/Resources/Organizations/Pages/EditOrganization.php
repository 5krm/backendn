<?php

namespace App\Filament\Tutor\Resources\Organizations\Pages;

use App\Filament\Tutor\Resources\Organizations\OrganizationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions\Action;


class EditOrganization extends EditRecord
{
    protected static string $resource = OrganizationResource::class;

    // protected function getHeaderActions(): array
    // {
    //     return [
    //         Action::make('save')
    //             ->label(__('tutor.form.save_changes'))
    //             ->color('primary')
    //             ->action('save'),
    //     ];
    // }

    // protected function getFormActions(): array
    // {
    //     return [];
    // }
}
