<?php

namespace App\Filament\Tutor\Resources\Tutors\Tables;

use App\Filament\Tutor\Pages\Settings;
use App\Mail\TutorInvitation;
use App\Models\PasswordResetToken;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Filament\Actions\Action as ActionsAction;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class TutorsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('tutor.tutors.tutor'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('tutor.tutors.email'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('phone')
                    ->label(__('tutor.tutors.phone'))
                    ->searchable(),

                ToggleColumn::make('tutorProfile.is_active')
                    ->label(__('tutor.tutors.is_active')),
                IconColumn::make('tutorProfile.is_verified')
                    ->label(__('tutor.tutors.is_verified'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->alignCenter(),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    // ActionsAction::make('edit_profile')
                    //     ->label('Edit Profile')
                    //     ->icon('heroicon-o-user')
                    //     ->url(
                    //         fn(User $record) =>
                    //         Settings::getUrl([
                    //             'user' => $record->id,
                    //         ])
                    //     ),
                    EditAction::make(),
                    ActionsAction::make('impersonate')
                        ->label(fn($record) => __('tutor.tutors.impersonate', ['user' =>$record->name]))
                        ->icon('heroicon-m-user-plus')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn(\App\Models\User $record) => auth()->user()->is_admin && auth()->user()->id != $record->id && !session()->has('impersonator_id') && $record->admin_access)
                        ->url(fn(\App\Models\User $record) => route('impersonation.start', $record->id)),
                    ActionsAction::make('resend_invitation')
                        ->label(__('tutor.tutors.resend_invitation'))
                        ->icon('heroicon-o-envelope')
                        ->color('warning')
                        ->visible(fn(User $record): bool => is_null($record->email_verified_at))
                        ->requiresConfirmation()
                        ->action(function (User $record) {
                            try {
                                $plainToken = Str::random(64);
                                $existingToken = PasswordResetToken::where('email', $record->email)->first();
                                if ($existingToken) {
                                    $existingToken->update([
                                        'token' => hash('sha256', $plainToken),
                                        'type' => 'invitation',
                                        'expired_at' => null,
                                    ]);
                                } else {
                                    PasswordResetToken::create([
                                        'email' => $record->email,
                                        'token' => hash('sha256', $plainToken),
                                        'type' => 'invitation',
                                        'created_at' => Carbon::now(),
                                    ]);
                                }

                                Mail::to($record->email)->send(new TutorInvitation($record, $plainToken));
                                Notification::make()
                                    ->success()
                                    ->title(__('tutor.tutors.resend_invitation_sent'))
                                    ->send();
                            } catch (Exception $e) {
                                Log::error('Resend tutor invitation error: ' . $e->getMessage());
                                Notification::make()
                                    ->danger()
                                    ->title(__('tutor.tutors.resend_invitation_failed'))
                                    ->send();
                            }
                        }),
                ])
                    ->color('grey')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->iconButton()
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    // DeleteBulkAction::make(),
                    // ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
