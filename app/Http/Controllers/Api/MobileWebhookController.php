<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\SignatureVerificationException;
use Stripe\Webhook;

class MobileWebhookController extends Controller
{
    public function stripeWebhook(Request $request)
    {
        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $endpoint_secret = env('STRIPE_WEBHOOK_SECRET');

        $event = null;

        try {
            $event = Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (SignatureVerificationException $e) {
            // Invalid signature
            return response()->json(['error' => 'Invalid signature'], 400);
        }

        // Handle the event
        switch ($event->type) {
            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;

                $course_id = $paymentIntent->metadata->course_id ?? null;
                $user_id = $paymentIntent->metadata->user_id ?? null;

                if ($course_id && $user_id) {
                    $user = User::find($user_id);
                    if ($user) {
                        // Assuming the User model has a courses() many-to-many relationship
                        // Check if already enrolled to avoid duplicates
                        if (! $user->courses()->where('course_id', $course_id)->exists()) {
                            $user->courses()->attach($course_id);
                            Log::info("User {$user_id} enrolled in course {$course_id}");
                        }
                    }
                }
                break;
            default:
                Log::info('Received unknown event type '.$event->type);
        }

        return response()->json(['status' => 'success']);
    }
}
