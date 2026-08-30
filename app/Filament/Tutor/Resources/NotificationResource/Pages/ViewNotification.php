<?php

namespace App\Filament\Tutor\Resources\NotificationResource\Pages;

use App\Filament\Tutor\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewNotification extends ViewRecord
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markAsRead')
                ->label('Mark as Read')
                ->icon('heroicon-o-check')
                ->color('success')
                ->visible(fn () => ! $this->record->read_at)
                ->action(function () {
                    $this->record->markAsRead();
                    $this->redirect(NotificationResource::getUrl('index'));
                }),

            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Mark as read when viewing
        if (! $this->record->read_at) {
            $this->record->markAsRead();
        }

        return $data;
    }
}
