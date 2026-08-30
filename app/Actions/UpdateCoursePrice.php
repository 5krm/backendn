<?php

namespace App\Actions;

use App\Jobs\Stripe\CreateStripePrice;
use App\Models\Courses\Course;
use App\Models\Courses\CoursePrice;

class UpdateCoursePrice
{
    public function execute(Course $course)
    {
        if ($course->is_free) {
            return;
        }

        $coursePrice = CoursePrice::query()
            ->where('price', $course->price)
            ->where('course_id', $course->id)
            ->first();


        if ($coursePrice != null) {
            $coursePrice->is_active = true;
            $coursePrice->save();
            return;
        }

        $coursePrice =  CoursePrice::create([
            "course_id" => $course->id,
            "price" => $course->price,
            "is_active" => true
        ]);

        CreateStripePrice::dispatch($coursePrice);
    }
}
