<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $direction = 'ltr';

    /**
     * Create a new message instance.
     */
    private User $user;

    public static $createUrlCallback;

    public function __construct(User $user)
    {
        $this->user = $user;
        $this->direction = $this->user->displayLang() == 'en' ? 'ltr' : 'rtl';
        $this->locale($user->displayLang());
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('support@portal365.org', 'NGO Academy'),
            subject: __('emails.verify_email.subject'),
        );
    }

    public function content(): Content
    {
        $verificationUrl = $this->verificationUrl();

        return new Content(
            markdown: 'emails.verify-email',
            with: ['user' => $this->user, 'url' => $verificationUrl]
        );
    }

    protected function verificationUrl()
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $this->user);
        }

        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $this->user->getKey(),
                'hash' => sha1($this->user->getEmailForVerification()),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
