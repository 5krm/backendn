<?php

namespace App\Providers\Filament\Navigation;

use Filament\Actions\Action as ActionsAction;
use Filament\Forms\Components\Toggle;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\HtmlString as SupportHtmlString;

class UserMenuActions
{
    public static function getItems(): array
    {
        return [
            ActionsAction::make('adminAccess')
                ->label(fn () => __('tutor.admin_access.manage_admin_access'))
                ->modalHeading(fn () => __('tutor.admin_access.manage_admin_access'))
                ->icon('heroicon-o-cog-6-tooth')
                ->form([
                    Toggle::make('admin_access')
                        ->label(fn () => __('tutor.admin_access.admin_access'))
                        ->reactive()
                        ->helperText(function ($state) {
                            $message = $state ? __('tutor.admin_access.super_admin_allowed') : __('tutor.admin_access.super_admin_restricted');

                            return new SupportHtmlString('
                        <span style="font-size: 12px !important; line-height: 1 !important;" class="text-gray-400 dark:text-gray-500 block mt-0.5">'.$message.'</span>');
                        }),
                ])
                ->fillForm(fn (): array => auth()->user()->toArray())
                ->action(function (array $data): void {
                    /** @var User $user */
                    $user = auth()->user();

                    $user->update([
                        'admin_access' => $data['admin_access'],
                    ]);
                })
                ->modalWidth('md')
                ->modalFooterActionsAlignment(Alignment::End)
                ->action(function (array $data) {
                    auth()->user()->update([
                        'admin_access' => $data['admin_access'],
                    ]);
                }),
        ];
    }
}
