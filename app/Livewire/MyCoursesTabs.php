<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class MyCoursesTabs extends Component
{
    public string $activeTab = 'in-progress';

    public $enrollments;

    public $certificates;

    public $wishlistCourses;

    public $ratings;

    // Method to switch tabs
    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.my-courses-tabs');
    }

    #[On('wishlist-updated')]
    public function onWishlistUpdated(int $courseId, bool $isWishlisted): void
    {
        if (! $isWishlisted) {
            $this->wishlistCourses = $this->wishlistCourses->where('course_id', '!=', $courseId);
        }
    }
}
