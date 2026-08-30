<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Course;
use App\Models\Coupon;
use Stripe\Stripe;
use Stripe\PaymentIntent;

class MobilePaymentController extends Controller
{
    public function createIntent(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
        ]);

        $course = Course::findOrFail($request->course_id);

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $paymentIntent = PaymentIntent::create([
            'amount' => $course->price * 100, // Amount in cents
            'currency' => 'usd',
            'metadata' => [
                'course_id' => $course->id,
                'user_id' => auth()->id(),
            ],
        ]);

        return response()->json([
            'client_secret' => $paymentIntent->client_secret,
        ]);
    }

    public function validateCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'course_id' => 'required|exists:courses,id',
        ]);

        $coupon = Coupon::where('code', $request->code)->first();
        if (!$coupon) {
            return response()->json(['error' => 'Invalid coupon code.'], 400);
        }
        
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json(['error' => 'Coupon has expired.'], 400);
        }

        $course = Course::findOrFail($request->course_id);
        $discountAmount = $course->price * ($coupon->discount_percent / 100);
        $newPrice = max(0, $course->price - $discountAmount);

        return response()->json([
            'original_price' => $course->price,
            'discount_percent' => $coupon->discount_percent,
            'discounted_price' => $newPrice,
        ]);
    }
}
