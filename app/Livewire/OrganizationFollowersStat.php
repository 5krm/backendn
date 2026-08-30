<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class OrganizationFollowersStat extends Component
{
    public int $followersCount;

    public function mount(int $followersCount): void
    {
        $this->followersCount = $followersCount;
    }

    #[On('organization-followers-updated')]
    public function updateCount(int $count): void
    {
        $this->followersCount = $count;
    }

    public function render()
    {
        return view('livewire.organization-followers-stat');
    }
}
