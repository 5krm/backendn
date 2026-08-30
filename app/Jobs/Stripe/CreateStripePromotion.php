<?php

namespace App\Jobs\Stripe;

use App\Models\Promotion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Stripe\StripeClient;

class CreateStripePromotion implements ShouldQueue
{
    use Queueable;

    public function __construct(private Promotion $promotion) {}

    public function handle(StripeClient $stripe): void
    {
        $coupon = $stripe->coupons->create([
            'name'=> $this->promotion->title, 
            'duration' => 'once',
            'percent_off' => $this->promotion->discount_percent,
        ]);


        $this->promotion->stripe_promotion_id = $coupon->id;
        $this->promotion->save();
    }
}
