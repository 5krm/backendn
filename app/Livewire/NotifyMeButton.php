<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Wishlist;

class NotifyMeButton extends Component
{
    public array $course;

    public bool $isWishlisted = false;

    public function mount(array $course)
    {
        $this->course = $course;

        if (auth()->check()) {
            $this->isWishlisted = Wishlist::query()
                ->where('user_id', auth()->id())
                ->where('course_id', $this->course['data']->id)
                ->exists();
        }
    }

    public function addToWishlist(): void
    {     
        if (! auth()->check()) {
            session()->put('url.intended', url()->current());

            $this->redirect(route('auth.login'));
            return;
        }
        $wishlist = Wishlist::query()
            ->where('user_id', auth()->id())
            ->where('course_id', $this->course['data']->id)
            ->first();

        if ($wishlist) {
            $wishlist->delete();
            $this->isWishlisted = false;
        } else {
            Wishlist::create([
                'user_id' => auth()->id(),
                'course_id' => $this->course['data']->id,
            ]);

            $this->isWishlisted = true;
        }
    }

    public function render()
    {
        return view('livewire.notify-me-button');
    }
}
