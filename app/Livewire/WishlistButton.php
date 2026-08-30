<?php

namespace App\Livewire;

use App\Models\Wishlist;
use Livewire\Component;

class WishlistButton extends Component
{
    public array $course;

    public bool $isWishlisted = false;

    public function mount(array $course, ?bool $isWishlisted = null): void
    {
        $this->course = $course;

        if ($isWishlisted !== null) {
            $this->isWishlisted = $isWishlisted;
        } elseif (auth()->check()) {
            $this->isWishlisted = Wishlist::query()
                ->where('user_id', auth()->id())
                ->where('course_id', $this->courseId())
                ->exists();
        }
    }

    public function toggle(): void
    {
        if (! auth()->check()) {
            session()->put('url.intended', url()->current());
            $this->redirect(route('auth.login'));

            return;
        }

        $wishlist = Wishlist::query()
            ->where('user_id', auth()->id())
            ->where('course_id', $this->courseId())
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $this->isWishlisted = false;
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'course_id' => $this->courseId(),
            ]);
            $this->isWishlisted = true;
        }

        $this->dispatch('wishlist-updated', courseId: $this->courseId(), isWishlisted: $this->isWishlisted);
    }

    public function render()
    {
        return view('livewire.wishlist-button');
    }

    private function courseId(): int
    {
        if (isset($this->course['data'])) {
            return (int) $this->course['data']->id;
        }

        return (int) $this->course['id'];
    }
}
