<?php

namespace App\Mail;

use App\Enums\PreferenceKey;
use App\Models\Courses\Course;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CourseRatingMail extends Mailable
{
    use Queueable, SerializesModels;

    
    public $direction = 'ltr';
    private $type = PreferenceKey::FollowupEmail;
    public function __construct(public User $user, public Course $course)
    {
        $this->direction = $this->user->displayLang() == 'en' ? 'ltr' : 'rtl';
        $this->locale($user->displayLang());
    }


    
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.course_rating_reminder.subject'),
            to: [$this->user->email],
        );
    }

    public function content(): Content
    {
        $unsubscribe_link = route('email.unsubscribe', ['token' => encrypt($this->user->email), 'type' => $this->type]);
        return new Content(
            markdown: 'emails.courses.course-rating-mail',
            with: ['unsubscribe_link' => $unsubscribe_link, 'direction' => $this->direction]
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
