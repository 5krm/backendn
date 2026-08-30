<?php

namespace App\Jobs\Stripe;

use App\Models\Courses\Course;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Stripe\StripeClient;

class UpdateStripeProduct implements ShouldQueue
{
    use Queueable;

    public function __construct(private Course $course) {}

    public function handle(StripeClient $stripe): void
    {
        if ($this->course->stripe_product_id == null) {
            return;
        }

        $stripe->products->update($this->course->stripe_product_id, [
            'name' => $this->course->title,
            'description' => mb_substr((string) $this->course->description, 0, 500),
            'images' => [$this->course->coverImage],
        ]);
    }
}
