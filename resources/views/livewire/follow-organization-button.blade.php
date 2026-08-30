<div>
    @auth
    
             <button
                type="button"
                wire:click="toggleFollow"
                class="rounded-md px-6 py-2 font-semibold text-white transition {{ $isFollowing ? 'bg-gray-600 hover:bg-gray-700' : 'bg-primary hover:bg-primary-700' }}">
                {{ $isFollowing ? __('organization.unfollow') : __('organization.follow') }}
            </button>
     @else
        <a href="{{ route('auth.login') }}"
           class="inline-block rounded-md bg-primary hover:bg-primary-700 px-6 py-2 font-semibold text-white transition ">
            {{ __('organization.follow') }}
        </a>
    @endauth
</div>
