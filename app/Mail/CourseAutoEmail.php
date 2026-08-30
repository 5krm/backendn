<?php

namespace App\Mail;

use App\Enums\PreferenceKey;
use App\Models\Courses\CourseMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CourseAutoEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $direction = 'ltr';
    private $type = PreferenceKey::FollowupEmail;
    /**
     * Create a new message instance.
     */
    public function __construct(public User $user, public CourseMail $courseMail)
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
            subject: $this->courseMail->subject,
            to: $this->user->email
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        if (!$this->courseMail->relationLoaded('course')) {
            $this->courseMail->load('course');
        }
        $content = $this->mapTags($this->courseMail, $this->user);
        $unsubscribe_link = route('email.unsubscribe', ['token' => encrypt($this->user->email), 'type' => $this->type]);
        Log::info('content:' . $content);

        return new Content(
            markdown: 'emails.courses.dynamic',
            with: [
                'user' => $this->user,
                'mail' => $this->courseMail,
                'content' => $content,
                'unsubscribe_link' => $unsubscribe_link
            ],
        );
    }

    public function getTags(): array
    {
        return [
            'student_name' => '{student_name}',
            'course_name' => '{course_name}',
            'tutor_name' => '{tutor_name}',
            'tutor_email' => '{tutor_email}',
            'course_url' => '{course_url}',
        ];
    }


    public function mapTags($mail, $user): string
    {
        return Str::swap([
            '{course_name}' => $mail->course->title,
            '{tutor_name}' => $user->name,
            '{tutor_email}' => $user->email,
            '{course_url}' => route('app.courses.details', $mail->course),
            '{student_name}' => $user->name
        ], $mail->body);
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
