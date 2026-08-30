<?php

namespace App\Jobs\Stripe;

use App\Models\Courses\Course;
use App\Models\Courses\CoursePrice;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\StripeClient;

class CreateStripeProduct implements ShouldQueue
{
    use Queueable;

    public function __construct(private Course $course) {}

    public function handle(StripeClient $stripe): void
    {
        $coursePrice = CoursePrice::create([
            'course_id' => $this->course->id,
            'price' => $this->course->price
        ]);

        $product = $stripe->products->create([
            'name' => $this->course->title,
            'description' => mb_substr((string) $this->course->description, 0, 500),
            'default_price_data' => [
                'unit_amount' => $this->course->price * 100,
                'currency' => 'usd',
                'metadata' => [
                    'course_id' => $this->course->id,
                    'course_price_id' => $coursePrice->id
                ]
            ],
            'metadata' => [
                'course_id' => $this->course->id
            ]
        ]);

        $this->course->stripe_product_id = $product->id;
        $coursePrice->stripe_price_id = $product->default_price;
        $this->course->save();
        $coursePrice->save();
    }
}
