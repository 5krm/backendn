<?php

namespace App\Jobs\Stripe;

use App\Models\Promotion;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Stripe\StripeClient;

class UpdateStripePromotion implements ShouldQueue
{
    use Queueable;

    public function __construct(private Promotion $promotion) {}

    public function handle(StripeClient $stripe): void
    {
        if ($this->promotion->stripe_promotion_id == null) return;

        $coupon = $stripe->coupons->update($this->promotion->stripe_promotion_id, [
            'name' => $this->promotion->title
        ]);

        $this->promotion->stripe_promotion_id = $coupon->id;
        $this->promotion->save();
    }
}
