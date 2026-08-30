<?php

namespace App\Filament\Tutor\Resources\NotificationResource\Pages;

use App\Filament\Tutor\Resources\NotificationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;

class ListNotifications extends ListRecords
{
    protected static string $resource = NotificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('markAllAsRead')
                ->label(__('notifications.mark_all_as_read'))
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->action(function () {
                    auth()->user()->unreadNotifications->markAsRead();

                    Notification::make()
                        ->title(__('tutor.markedNotificationsRead'))
                        ->success()
                        ->send();
                })
                ->visible(fn() => auth()->user()->unreadNotifications->count() > 0),
        ];
    }
}
