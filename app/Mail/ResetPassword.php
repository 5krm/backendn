<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $direction = 'ltr';

    private $token;

    private User $user;

    public static $createUrlCallback;

    public function __construct(User $user, $token)
    {
        $this->user = $user;
        $this->token = $token;
        $this->direction = $this->user->displayLang() == 'en' ? 'ltr' : 'rtl';
        $this->locale($user->displayLang());
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('support@portal365.org', 'NGO Academy'),
            subject: __('emails.reset_password.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $resetLink = $this->resetUrl();

        return new Content(
            markdown: 'emails.reset-password',
            with: ['user' => $this->user, 'url' => $resetLink, 'direction' => $this->direction]
        );
    }

    protected function resetUrl()
    {
        if (static::$createUrlCallback) {
            return call_user_func(static::$createUrlCallback, $this->user, $this->token);
        }

        return url(route('password.reset', [
            'token' => $this->token,
            'email' => $this->user->getEmailForPasswordReset(),
        ], false));
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
