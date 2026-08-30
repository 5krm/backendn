<?php

namespace App\Filament\Tutor\Resources\Organizations\RelationManagers;

use App\Filament\Tutor\Resources\Organizations\OrganizationResource;
use App\Models\Courses\Course;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Carbon\Carbon;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;
use App\Mail\TutorInvitation;
use App\Models\PasswordResetToken;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;


class UsersRelationManager extends RelationManager
{
    protected static string $relationship = 'users';
    protected static ?string $title = 'Organization Users';
    // protected static ?string $title = null;

    protected static ?string $relatedResource = OrganizationResource::class;

    public function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }
    public function table(Table $table): Table
    {
        return $table
            ->heading(__('tutor.tables.organization_users'))
            ->columns([
                TextColumn::make('name')
                    ->label(__('tutor.tutors.tutor'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('email')
                    ->label(__('tutor.tutors.email'))
                    ->searchable()
                    ->sortable(),


            ])
            ->headerActions([
                // CreateAction::make()
                // ->label('Assign Users')
                //  ->modalHeading('Assign Users to Organization'),
                Action::make('assignUsers')
                    ->label(__('tutor.form.assign_users'))
                    ->modalSubmitActionLabel(__('tutor.form.save'))
                    ->form([
                        Select::make('selected_users')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(fn() => User::query()
                                ->whereNull('organization_id')
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->toArray())
                            ->getOptionLabelsUsing(fn(array $values): array => User::query()
                                ->whereKey($values)
                                ->pluck('name', 'id')
                                ->toArray())

                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required(),

                                TextInput::make('email')
                                    ->email()
                                    ->required(),
                            ])

                            ->createOptionUsing(function (array $data): int {
                                $email = Str::lower(trim($data['email']));

                                /** @var User|null $existing */
                                $existing = User::query()->where('email', $email)->first();

                                if ($existing) {
                                    // No duplicates: reuse the existing user.
                                    // If name is missing/placeholder, update it.
                                    if (blank($existing->name)) {
                                        $existing->forceFill(['name' => $data['name']])->save();
                                    }

                                    return $existing->id;
                                }

                                $user = User::create([
                                    'name' => $data['name'],
                                    'email' => $email,
                                    'password' => null,
                                    'is_tutor' => true,
                                ]);

                                return $user->id;
                            })->required(),
                    ])
                    ->action(function (array $data) {

                        $organization = $this->getOwnerRecord();

                        $userIds = collect($data['selected_users'] ?? [])
                            ->filter()
                            ->unique()
                            ->values()
                            ->all();

                        $users = User::whereIn('id', $userIds)->get();
                        
                        foreach ($users as $user) {
                            $shouldInvite = is_null($user->email_verified_at) || is_null($user->password);

                            // Always attach to organization (no duplicates / no-op if already set)
                            $user->forceFill([
                                'organization_id' => $organization->id,
                            ]);



                            // Promote existing active users to tutor when needed
                            if (! $user->is_tutor) {
                                $user->forceFill(['is_tutor' => true]);
                            }

                            $user->save();

                            if ($user->tutorProfile?->id) {
                                $user->tutorCourses()
                                    ->update(['organization_id' => $organization->id]);
                            }

                            // Only send invitation if user is new / not active yet
                            if ($shouldInvite) {
                                $this->sendTutorInvitation($user);
                            }
                        }

                        Notification::make()
                            ->success()
                            ->title(__('tutor.form.users_assigned_successfully'))
                            ->send();
                    })

            ])
            ->recordActions([
                Action::make('removeFromOrganization')
                    ->label(__('tutor.actions.delete'))
                    ->color('danger')
                    ->icon('heroicon-o-trash')
                    ->requiresConfirmation()
                    ->action(function (User $record) {

                        $record->update([
                            'organization_id' => null,
                        ]);
                        Course::where('tutor_id', $record->id)
                            ->update(['organization_id' => null]);

                        Notification::make()
                            ->success()
                            ->title(__('tutor.form.user_removed_from_organization'))
                            ->send();
                    })->successRedirectUrl(request()->header('Referer')),
            ]);
    }
    protected function sendTutorInvitation(User $user): void
    {
        $plainToken = Str::random(64);
        $existingToken = PasswordResetToken::where('email', $user->email)->first();
        if ($existingToken) {
            $existingToken->update([
                'token' => hash('sha256', $plainToken),
                'type' => 'invitation',
                'expired_at' => null
            ]);
        } else {
            PasswordResetToken::create([
                'email' => $user->email,
                'token' => hash('sha256', $plainToken),
                'type' => 'invitation',
                'created_at' => Carbon::now(),
            ]);
        }

        Log::info('created token');

        Mail::to($user->email)->send(new TutorInvitation($user, $plainToken));

        Log::info('sent email');

        Notification::make()
            ->success()
            ->title(__('tutor.tutors.invitation_sent'))
            ->send();
    }

    //     public function form(Schema $schema): Schema
    // {
    //     return $schema->components([
    //         TextInput::make('name')
    //             ->required(),

    //         TextInput::make('email')
    //             ->email()
    //             ->required(),
    //     ]);
    // }
}
