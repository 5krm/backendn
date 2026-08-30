<?php

namespace App\Listeners;

use App\Enums\PreferenceKey;
use App\Events\NewReplyPosted;
use App\Mail\Comments\NewReply;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendNewReplyEmail
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(NewReplyPosted $event): void
    {
        /** @var User $user */
        $user = $event->reply->parent->user;
        error_log('to send to the user:'.$user->email);
        $should_be_notified = $user->preferences()->where('key', PreferenceKey::NotificationEmail)->where('value', true)->exists();
        error_log('should be notified?'.$should_be_notified);
        if ($user->id == $event->reply->user_id || ! $should_be_notified) {
            error_log('failed');

            return;
        }

        error_log('sending to:'.$user->email);
        Mail::to($user)->send(new NewReply($user, $event->reply));
    }
}
