<?php

namespace App\Mail;

use App\Enums\PreferenceKey;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\Courses\Course;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\App;

class TutorInvitation extends Mailable
{
    use Queueable, SerializesModels;

    public $direction = 'ltr';
    private $type = PreferenceKey::FollowupEmail;

    public function __construct(public User $user, public string $plainToken)
    {
        $this->direction = $this->user->displayLang() == 'ar' ? 'rtl' : 'ltr';
        App::setLocale($this->user->displayLang());
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.tutor_invitation.subject'),
            to: [$this->user->email],
        );
    }

    public function content(): Content
    {
        $link = route('email.setup_tutor', ['token' => $this->plainToken]);
        return new Content(
            markdown: 'emails.accounts.tutor-invitation',
            with: ['setup_link' => $link]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
