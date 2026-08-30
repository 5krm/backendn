<?php

namespace App\Mail;

use App\Enums\PreferenceKey;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PromotionAnnouncement extends Mailable
{
    use Queueable, SerializesModels;

    public string $direction = 'ltr';

    private PreferenceKey $type = PreferenceKey::FollowupEmail;

    public function __construct(public User $user, public Promotion $promotion)
    {
        $this->direction = $this->user->displayLang() == 'ar' ? 'rtl' : 'ltr';
        $this->locale($this->user->displayLang());
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('emails.promotion_announcement.subject', [
                'percent' => $this->promotion->discount_percent,
            ]),
            to: [$this->user->email]
        );
    }

    public function content(): Content
    {
        $unsubscribe_link = route('email.unsubscribe', [
            'token' => encrypt($this->user->email),
            'type' => $this->type,
        ]);

        return new Content(
            markdown: 'emails.promotions.announcement',
            with: [
                'unsubscribe_link' => $unsubscribe_link,
                'direction' => $this->direction,
                'promotion' => $this->promotion,
            ]
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
