<?php

namespace App\Filament\Tutor\Resources\Promotions\Tables;

use App\Enums\PreferenceKey;
use App\Jobs\SendPromotionEmailJob;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class PromotionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('tutor.promotions.title')),
                TextColumn::make('discount_percent')
                    ->label(__('tutor.promotions.discount_percent'))
                    ->prefix('%')
                    ->badge(),
                TextColumn::make('start')
                    ->label(__('tutor.promotions.start'))
                    ->dateTime()->toUserTimezone(),
                TextColumn::make('end')
                    ->label(__('tutor.promotions.end'))
                    ->dateTime()
                    ->toUserTimezone(),
                ToggleColumn::make('status')
                    ->label(__('tutor.promotions.status'))
                    ->disabled(fn (): bool => ! (auth()->user()?->isAdmin() ?? false)),

            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    Action::make('send_promotion_email')
                        ->label(__('tutor.promotions.send_email'))
                        ->icon('heroicon-o-envelope')
                        ->color('success')
                        ->visible(fn (): bool => auth()->user()?->isAdmin() ?? false)
                        ->requiresConfirmation()
                        ->modalDescription(function ($record): string {
                            return __('tutor.promotions.send_email_confirm', [
                                'count' => self::eligibleRecipientsQuery()->count(),
                            ]);
                        })
                        ->action(function ($record): void {
                            $count = 0;

                            self::eligibleRecipientsQuery()
                                ->select(['id', 'email'])
                                ->chunkById(100, function ($users) use ($record, &$count): void {
                                    foreach ($users as $user) {
                                        SendPromotionEmailJob::dispatch($user, $record);
                                        $count++;
                                    }
                                });

                            if ($count === 0) {
                                Notification::make()
                                    ->warning()
                                    ->title(__('tutor.promotions.send_email_empty'))
                                    ->send();

                                return;
                            }

                            Notification::make()
                                ->success()
                                ->title(__('tutor.promotions.send_email_success', ['count' => $count]))
                                ->send();
                        }),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }

    private static function eligibleRecipientsQuery()
    {
        return User::query()
            ->whereNotNull('email')
            ->whereHas(
                'preferences',
                fn ($q) => $q
                    ->where('key', PreferenceKey::FollowupEmail)
                    ->where('value', true)
            )
            ->whereDoesntHave(
                'tutorProfile',
                fn ($q) => $q->where('is_active', true)
            );
    }
}
