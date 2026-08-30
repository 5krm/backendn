<?php

namespace App\Jobs;

use App\Enums\PreferenceKey;
use App\Mail\PromotionAnnouncement;
use App\Models\Promotion;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;

class SendPromotionEmailJob implements ShouldQueue
{
    use Queueable;

    public $timeout = 0;

    public $tries = 10;

    public function __construct(public User $user, public Promotion $promotion)
    {
        $this->onQueue('low');
    }

    public function handle(): void
    {
        $allowed = $this->user->preferences()
            ->where('key', PreferenceKey::FollowupEmail)
            ->where('value', true)
            ->exists();

        if (! $allowed || $this->user->isTutor() || blank($this->user->email)) {
            return;
        }

        Mail::to($this->user->email)->send(new PromotionAnnouncement($this->user, $this->promotion));
    }
}
