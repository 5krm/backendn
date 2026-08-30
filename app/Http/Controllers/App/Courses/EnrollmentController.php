<?php

namespace App\Http\Controllers\App\Courses;

use App\Enums\CourseStatus;
use App\Models\Invoice;
use App\Models\User;
use Stripe\StripeClient;
use App\Models\Courses\Course;
use App\Models\Courses\Enrollment;
use App\Http\Controllers\Controller;
use App\Jobs\SendCourseEmailJob;
use App\Models\Courses\CourseMail;
use App\ViewModels\Courses\CourseView;
use Illuminate\Support\Facades\DB;

class EnrollmentController extends Controller
{
    public function index(Course $course, StripeClient $stripe)
    {
        if ($course->is_free) {
            return $this->processFree($course);
        }

        $course->load(["activePromotions", "activePrice"]);

        $discount = $course->activePromotions?->first()?->stripe_promotion_id ? ['discounts' => [
            ['coupon' => $course->activePromotions?->first()?->stripe_promotion_id]
        ]] : [];

        $course->load("activePrice");
        $YOUR_DOMAIN = config('app.url');
        $user = auth()->user();

        $price = $course->activePrice?->stripe_price_id ?
            ['price' =>  $course->activePrice?->stripe_price_id] :
            ['price_data' => [
                'currency' => 'usd',
                'unit_amount' => (int) ($course->price * 100),
                'product_data' => [
                    'name' => $course->title,
                    'description' => mb_substr((string) $course->description, 0, 500),
                    'images' => [$course->coverImage]
                ],
            ]];

        $session_data = [
            'ui_mode' => 'embedded_page',
            'line_items' => [[
                ...$price,
                'quantity' => 1,
            ]],
            'customer_creation' => 'always',
            'customer_email' => $user->email,
            'mode' => 'payment',
            'client_reference_id' => (string) $course->id,
            'metadata' => [
                'course_id' => (string) $course->id,
                'course_slug' => $course->slug
            ],
            ...$discount,
            'return_url' => "$YOUR_DOMAIN/app/courses/{$course->slug}/enroll/process?session_id={CHECKOUT_SESSION_ID}",
        ];


        $checkout_session = $stripe->checkout->sessions->create($session_data);

        return view('app.courses.enrollment.index', [
            'user' => $user,
            'clientSecret' => $checkout_session->client_secret
        ]);
    }


    public function process()
    {
        /** @var User $user */
        $user =  auth()->user();

        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $session = $stripe->checkout->sessions->retrieve(request("session_id"), ['expand' => ['line_items']]);

        // Get course by metadata instead of stripe price id
        $courseId = $session->metadata['course_id'] ?? $session->client_reference_id ?? null;
        $course = $courseId ? Course::where('id', $courseId)->where('status', CourseStatus::published)->first() : null;
        if (!$course) {
            return redirect(route('app.courses'));
        }

        if ($session->status === 'open') {
            return redirect(route('app.courses.enroll'));
        }

        return $this->finishEnrollment($course, $user, $session);
    }

    private function processFree(Course $course)
    {
        /** @var User $user */
        $user =  auth()->user();

        return $this->finishEnrollment($course, $user, null);
    }

    private function finishEnrollment(Course $course, User $user, ?\Stripe\Checkout\Session $session)
    {
        DB::transaction(function () use ($course, $user, $session) {
            Enrollment::updateOrCreate([
                'user_id' => $user->id,
                'course_id' => $course->id,
            ], [
                'progress' => 0,
                'score' => 0,
            ]);

            if (!$course->is_free) {
                Invoice::create([
                    'user_id' => $user->id,
                    'invoiceable_id' => $course->id,
                    'invoiceable_type' => Course::class,
                    'amount_total' => $session->amount_total / 100,
                    'amount_subtotal' =>  $session->amount_subtotal / 100,
                    'stripe_session_id' =>  $session->id,
                ]);
            }

            $mail = CourseMail::where('course_id', $course->id)->first();
            if (isset($mail)) {
                SendCourseEmailJob::dispatch($user, $mail);
            }
        });

        return redirect(route('app.courses.enroll.success', $course->slug));
    }

    public function success(Course $course)
    {
        /** @var User $user */
        $user =  auth()->user();
        $course->load("media");

        return view('app.courses.enrollment.success', [
            "course" => (new CourseView($course))->toArray(),
            "user" => $user
        ]);
    }
}
