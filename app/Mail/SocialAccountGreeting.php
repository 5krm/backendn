<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class SocialAccountGreeting extends Mailable
{
    use Queueable, SerializesModels;
    public $direction = 'ltr';

    public function __construct(public User $user)
    {
        $this->direction = $this->user->displayLang() == 'ar' ? 'rtl' : 'ltr';
        App::setLocale($this->user->displayLang());
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: trans('emails.social_account.title'),
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.accounts.social-account-greeting',
        );
    }
}
