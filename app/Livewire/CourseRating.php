<?php

namespace App\Livewire;

use App\Models\Courses\Course;
use App\Models\Courses\CourseRating as CourseRatingModel;
use Livewire\Component;

class CourseRating extends Component
{
    public string $course_slug;
    public Course $course;
    public bool $hasSubmited = false;
    public bool $display_course = false;
    public int $rating = 0;
    public string $review = "";
    public CourseRatingModel $previous_rating;
    public function mount()
    {
        $this->course = Course::where('slug', $this->course_slug)->first();
        $user = auth()->user();
        $prev = CourseRatingModel::where('user_id', $user->id)->where('course_id', $this->course->id)->first();
        if (isset($prev)) {
            $this->previous_rating = $prev;
            $this->rating = $this->previous_rating->rating;
            $this->review = $this->previous_rating->review;
        }
    }
    public function submitRating()
    {
        $this->validate([
            'rating' => 'required|integer|min:1|max:5',
        ]);

        CourseRatingModel::updateOrCreate([
            'user_id' => auth()->id(),
            'course_id' => $this->course->id
        ], [
            'rating' => $this->rating,
            'review' => $this->review
        ]);
        $this->dispatch('rating-submitted');
    }

    public function render()
    {
        
        
        return view('livewire.course-rating');
    }
}
