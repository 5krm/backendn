<?php

namespace App\Mail;

use App\Enums\PreferenceKey;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\Courses\Course;
use App\Models\Lessons\Lesson;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\App;

class CourseFollowUp extends Mailable
{
    use Queueable, SerializesModels;

    public $direction = 'ltr';
    private $type = PreferenceKey::FollowupEmail;

    public function __construct(
        public User $user,
        public Course $course,
        public Lesson $lesson
    ) {
        $this->direction = $this->user->displayLang() == 'en' ? 'ltr' : 'rtl';
        $this->locale($user->displayLang());
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.course_followup.subject'),
            to: [$this->user->email],
        );
    }

    public function content(): Content
    {
        $unsubscribe_link = route('email.unsubscribe', ['token' => encrypt($this->user->email), 'type' => $this->type]);
        return new Content(
            markdown: 'emails.courses.followup',
            with: ['unsubscribe_link' => $unsubscribe_link, 'direction' => $this->direction]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
