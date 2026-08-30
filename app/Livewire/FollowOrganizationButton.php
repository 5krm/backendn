<?php

namespace App\Livewire;

use App\Models\Organization;
use App\Models\OrganizationFollower;
use Livewire\Component;

class FollowOrganizationButton extends Component
{
    public Organization $organization;

    public bool $isFollowing = false;

    public function mount(Organization $organization): void
    {
        $this->organization = $organization;

        if (auth()->check()) {
            $this->isFollowing = OrganizationFollower::query()
                ->where('user_id', auth()->id())
                ->where('organization_id', $this->organization->id)
                ->exists();
        }
    }

    public function toggleFollow(): void
    {
        if (! auth()->check()) {
            session()->put('url.intended', url()->current());
            $this->redirect(route('auth.login'));

            return;
        }

        $follow = OrganizationFollower::query()
            ->where('user_id', auth()->id())
            ->where('organization_id', $this->organization->id)
            ->first();

        if ($follow) {
            $follow->delete();
            $this->isFollowing = false;
        } else {
            OrganizationFollower::create([
                'user_id' => auth()->id(),
                'organization_id' => $this->organization->id,
            ]);
            $this->isFollowing = true;
        }

        $followersCount = OrganizationFollower::query()
            ->where('organization_id', $this->organization->id)
            ->count();

        $this->dispatch('organization-followers-updated', count: $followersCount);
    }

    public function render()
    {
        return view('livewire.follow-organization-button');
    }
}
