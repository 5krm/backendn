<?php

namespace App\Filament\Tutor\Resources\Organizations\Pages;

use App\Filament\Tutor\Resources\Organizations\OrganizationResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

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
