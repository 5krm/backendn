<?php

namespace App\Mail\Comments;

use App\Enums\PreferenceKey;
use App\Models\Lessons\LessonComment\Comment;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\App;

class NewComment extends Mailable
{
    use Queueable, SerializesModels;

    private $type = PreferenceKey::NotificationEmail;
    public $direction = 'ltr';
    
    public function __construct(public User $user, public Comment $comment)
    {
        $this->direction = $this->user->displayLang() == 'ar' ? 'rtl' : 'ltr';
        App::setLocale($this->user->displayLang());
    }
    
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: trans('emails.new_comment.title'),
        );
    }
    
    public function content(): Content
    {
        $unsubscribe_link = route('email.unsubscribe', ['token' => encrypt($this->user->email), 'type' => $this->type]);
        return new Content(
            markdown: 'emails.comments.new-comment',
            with: ['unsubscribe_link' => $unsubscribe_link]
        );
    }
}
