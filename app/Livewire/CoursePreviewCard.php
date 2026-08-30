<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class CoursePreviewCard extends Component
{
    public array $course;

    public bool $isWishlisted = false;

    #[On('wishlist-updated')]
    public function onWishlistUpdated(int $courseId, bool $isWishlisted): void
    {
        if ($courseId === (int) $this->course['data']->id) {
            $this->isWishlisted = $isWishlisted;
        }
    }

    public function render()
    {
        return view('livewire.course-preview-card');
    }
}
