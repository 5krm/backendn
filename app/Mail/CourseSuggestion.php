<?php

namespace App\Mail;

use App\Enums\PreferenceKey;
use App\Models\Courses\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseSuggestion extends Mailable
{
    use Queueable, SerializesModels;

    public $direction = 'ltr';

    private $type = PreferenceKey::FollowupEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public Course $course)
    {
        $this->direction = $this->user->displayLang() == 'ar' ? 'rtl' : 'ltr';
        $this->locale($this->user->displayLang());
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.course_suggestion.subject'),
            to: [$this->user->email]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $unsubscribe_link = route('email.unsubscribe', ['token' => encrypt($this->user->email), 'type' => $this->type]);

        return new Content(
            markdown: 'emails.courses.suggestion',
            with: ['unsubscribe_link' => $unsubscribe_link, 'course' => $this->course]
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
