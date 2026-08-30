<?php

namespace App\Livewire;

use App\Models\Courses\Course;
use App\Models\Lessons\Lesson;
use App\Models\Lessons\LessonTracking;
use App\Models\User;
use App\Services\CourseCompletionService;
use Carbon\Carbon;
use Livewire\Attributes\Computed;
use Livewire\Component;

class CompleteLesson extends Component
{
    public $lesson;

    public function render()
    {
        return $this->view();
    }

    #[Computed]
    public function view()
    {
        if ($this->lesson['completed_at']) {
            return <<<'HTML'
                <span class="text-primary font-bold flex  items-center">
                <span class="icon-[mdi--check-circle] me-1 size-4 "></span>
                {{ ucfirst(__('complete')) }}
                </span>
            HTML;
        } else {
            return <<<'HTML'
                <button wire:click="complete" class="btn  btn-primary rounded-full  shadow-lg" type="button">
                    {{ __('lessons.markAsComplete') }}
                    <i class="icon-[mdi--checkbox-marked-circle-outline] text-white size-5"></i>
                </button>                
            HTML;
        }
    }

    public function complete()
    {
        $lessonId = Lesson::where('public_key', $this->lesson['public_key'])->first()->id;

        /** @var User */
        $user = auth()->user();

        $tracking = LessonTracking::where('user_id', $user->id)
            ->where('lesson_id', $lessonId)
            ->first();

        $tracking->completed_at = Carbon::now();
        $tracking->save();

        $this->lesson['completed_at'] = Carbon::now();
        $course = Course::find($this->lesson['course_id']);
        $courseHasExam = Quiz::whereHas('lesson', fn ($q) => $q->where('course_id', $course->id))->exists();
        $LastLesson = Lesson::where('course_id', $this->lesson['course_id'])
            ->orderByDesc('section_order')->orderByDesc('lesson_order')->first();

        if (! $courseHasExam && $LastLesson->id == $this->lesson['id']) {
            (new CourseCompletionService)->finish_course($course, 100);
        }
    }
}
