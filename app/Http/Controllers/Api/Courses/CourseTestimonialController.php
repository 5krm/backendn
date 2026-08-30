<?php

namespace App\Http\Controllers\Api\Courses;

use Illuminate\Http\Request;
use App\Models\Courses\Course;
use App\Http\Controllers\Controller;
use App\Models\Courses\CourseTestimonial;
use App\Data\Courses\CourseTestimonialData;
use App\Http\Resources\CourseTestimonialResource;

class CourseTestimonialController extends Controller
{
    public function __construct()
    {
        CourseTestimonialResource::withoutWrapping();
    }
    public function index(Course $course)
    {
        $testimonials = $course->testimonials()->get();
        return CourseTestimonialResource::collection($testimonials);
    }

    public function store(Course $course, CourseTestimonialData $inputs)
    {
        $testimonial = $course
            ->testimonials()
            ->create($inputs->toArray());

        return CourseTestimonialResource::make($testimonial);
    }

    public function update(Course $course, CourseTestimonial $testimonial, CourseTestimonialData $data)
    {
        $testimonial->update($data->toArray());
        return CourseTestimonialResource::make($testimonial);
    }

    public function upload(Course $course, CourseTestimonial $testimonial, Request $request)
    {
        $file = $request->file('author_image');
        $testimonial->clearMediaCollection('authors');
        $testimonial->addMedia($file)->toMediaCollection('authors');

        return CourseTestimonialResource::make($testimonial);
    }

    public function delete(Course $course, CourseTestimonial $testimonial)
    {
        $testimonial->delete();
        return response()->noContent();
    }
}
