<?php

namespace App\Jobs\Stripe;

use App\Models\Courses\CoursePrice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Stripe\StripeClient;

class CreateStripePrice implements ShouldQueue
{
    use Queueable;

    public function __construct(private CoursePrice $coursePrice) {}

    public function handle(StripeClient $stripe): void
    {
        if ($this->coursePrice->course->stripe_product_id == null) {
            return;
        }
        $this->coursePrice->load('course');
        $price = $stripe->prices->create([
            'currency' => 'usd',
            'unit_amount' => $this->coursePrice->price * 100,
            'product' => $this->coursePrice->course->stripe_product_id,
            'metadata' => [
                'course_id' => $this->coursePrice->course_id,
                'course_price_id' => $this->coursePrice->id,
            ],
        ]);

        $this->coursePrice->stripe_price_id = $price->id;
        $this->coursePrice->save();
    }
}
