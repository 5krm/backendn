<?php

namespace App\Http\Resources\Courses;

use App\Enums\CourseStatus;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\Lessons\CourseSectionResource;
use App\Http\Resources\TutorResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'slug' => $this->slug,
            'link' => route('courses.details', ['course' => $this->slug]),
            'title' => $this->title,
            'description' => $this->description,
            'objectives' => $this->objectives,
            'lang' => $this->lang,
            'cover_image' => $this->coverImage,
            'duration' => $this->duration ?? 0,
            'formatted_duration' => $this->textDuration,
            'order' => $this->order,
            'price' => $this->price ?? 0,
            'is_free' => $this->is_free ?? false,
            'old_price' => $this->old_price ?? 0,
            'stripe_price_id' => $this->stripe_price_id,
            'status' => [
                'key' => $this->status->value,
                'value' => CourseStatus::titles()[$this->status->value]
            ],
            'created_at' => Carbon::parse($this->created_at)->format('Y-m-d'),
            'lesson_count' => $this->lessons_count ?? 0,
            'student_count' => $this->students_count ?? 0,
            'author' => TutorResource::make($this->whenLoaded('tutor')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'sections' => CourseSectionResource::collection($this->whenLoaded('sections')),
            'is_enrolled' => $this->when(auth('sanctum')->check(), function () {
                return \App\Models\Courses\Enrollment::where('course_id', $this->id)
                    ->where('user_id', auth('sanctum')->id())
                    ->exists();
            }),
            'completed_lessons_count' => $this->when(auth('sanctum')->check(), function () {
                return \App\Models\Lessons\LessonTracking::where('user_id', auth('sanctum')->id())
                    ->whereNotNull('completed_at')
                    ->whereIn('lesson_id', $this->lessons()->pluck('id'))
                    ->count();
            }),
        ];
    }
}
