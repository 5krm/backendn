<?php

namespace App\Events;

use App\Models\Courses\Course;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;

class CoursePublished
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Course $course)
    {
    }
}
