<?php

namespace App\Mail;

use App\Enums\CourseEmailType;
use App\Enums\PreferenceKey;
use App\Models\Courses\Course;
use App\Models\Courses\CourseMailLog;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class NewCourseAdded extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $direction = 'ltr';

    private $type = PreferenceKey::UpdateEmail;

    public function __construct(public User $user, public Course $course)
    {
        $this->direction = $user->displayLang() == 'en' ? 'ltr' : 'rtl';
        $this->locale($user->displayLang());
        // $this->onQueue('low');
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.new_course.subject'),
        );
    }

    public function content(): Content
    {

        CourseMailLog::create([
            'course_id' => $this->course->id,
            'user_id' => $this->user->id,
            'type' => CourseEmailType::NewCourse,
        ]);
        $unsubscribe_link = route('email.unsubscribe', ['token' => encrypt($this->user->email), 'type' => $this->type]);
        $this->course->load('organization');

        return new Content(
            markdown: 'emails.courses.new-course',
            with: ['unsubscribe_link' => $unsubscribe_link, 'direction' => $this->direction]
        );
    }

    public function attachments(): array
    {
        return [];
    }

    public function failed(Throwable $exception): void
    {
        // Execute logic when the email fails to send after all retries
        Log::info('Mailable failed to send: '.$exception->getMessage());

    }
}
