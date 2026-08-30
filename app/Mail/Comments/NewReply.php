<?php

namespace App\Mail\Comments;

use App\Enums\PreferenceKey;
use App\Models\Lessons\LessonComment\Comment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class NewReply extends Mailable
{
    use Queueable, SerializesModels;

    private $type = PreferenceKey::NotificationEmail;

    public $direction = 'ltr';

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public Comment $reply)
    {
        $this->direction = $this->user->displayLang() == 'ar' ? 'rtl' : 'ltr';
        App::setLocale($this->user->displayLang());
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: trans('emails.new_reply.title'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $unsubscribe_link = route('email.unsubscribe', ['token' => encrypt($this->user->email), 'type' => $this->type]);

        return new Content(
            markdown: 'emails.comments.new-reply',
            with: ['unsubscribe_link' => $unsubscribe_link]
        );
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
