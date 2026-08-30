<?php

namespace App\Mail;

use App\Enums\CourseEmailType;
use App\Enums\PreferenceKey;
use App\Models\Courses\CourseMailLog;
use App\Models\Lessons\Lesson;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class PublishedLesson extends Mailable
{
    use Queueable, SerializesModels;

    public $direction = 'ltr';
    private $type = PreferenceKey::UpdateEmail;

    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public Lesson $lesson)
    {
        $this->direction = $this->user->displayLang() == 'ar' ? 'rtl' : 'ltr';
        $this->locale($user->displayLang());
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.published_lesson.subject'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $lesson = Lesson::whereHas('course')->find($this->lesson->id);       

        $log = CourseMailLog::create([
            'course_id' => $lesson->course_id,
            'user_id' => $this->user->id,
            'type' => CourseEmailType::PublishedLesson,
            'record_id' => $lesson->id
        ]);

        if (!$log || !$log->id) {
            return throw (new \Exception('failed to create log record!'));
        }

        $unsubscribe_link = route('email.unsubscribe', ['token' => encrypt($this->user->email), 'type' => $this->type]);
        return new Content(
            markdown: 'emails.courses.published-lesson',
            with: ['unsubscribe_link' => $unsubscribe_link, 'direction' => $this->direction]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }


    public function failed(Throwable $exception): void
    {
        // Execute logic when the email fails to send after all retries
        Log::info('Mailable failed to send: ' . $exception->getMessage());
    }
}
