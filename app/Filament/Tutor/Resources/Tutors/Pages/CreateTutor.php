<?php

namespace App\Filament\Tutor\Resources\Tutors\Pages;

use App\Filament\Tutor\Resources\Tutors\TutorResource;
use App\Mail\TutorInvitation;
use App\Models\PasswordResetModel;
use App\Models\PasswordResetToken;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\UserToken;
use Carbon\Carbon;
use Exception;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;
use Filament\Notifications\Notification;
use phpDocumentor\Reflection\Types\Boolean;

class CreateTutor extends CreateRecord
{
    protected static string $resource = TutorResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $formState = $this->form->getState();

            DB::beginTransaction();

            $user = User::where('email', $data['email'])->first();
            if($user?->is_tutor) {
                throw ValidationException::withMessages([
                    'email' => __('tutor.tutors.duplicated_email'),
                ]);
            }
            if ($user) {
                // Update existing user to tutor
                $user->update([
                    'is_tutor' => true,
                ]);

                Log::info('Updated existing user to tutor');
            } else {
                // Create new tutor user
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => null,
                    'is_tutor' => true,
                ]);

                Log::info('Created new tutor user');
            }

            $this->sendTutorInvitation($user);

            DB::commit();
            return $user;
        } catch (Exception $e) {
            Log::error(__('tutor.tutors.invitation_error') . $e->getMessage());
            $user = User::withTrashed()->where('email', $data['email'])->first();
            if ($user && ($user->is_tutor == false || $user->trashed())) {
                $user->is_tutor = true;
                $user->restore();
                $this->sendTutorInvitation($user);
                DB::commit();
                return $user;
            }
            DB::rollBack();
            Notification::make()
                ->danger()
                ->title(__('tutor.tutors.invitation_failed'))
                ->body($e->getMessage())
                ->send();
            throw ValidationException::withMessages([
                'email' => __('tutor.tutors.invitation_failed_and_try'),
            ]);
        }
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
