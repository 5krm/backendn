<x-layouts.app>
    <div class="lg:grid lg:grid-cols-4 gap-5">
        <div class="">
            <ul class="menu mb-4 space-y-2 lg:mb-0 lg:menu-lg rounded-box">
                <li>
                    <a class=" !text-base {{ request()->routeIs('app.profile') ? 'active' : '' }}" href="{{ route('app.profile') }}">
                        <i class="icon-[mdi--home-outline] text-2xl"></i>
                        {{ __('profile.links.profile') }}
                    </a>
                </li>
                <li>
                    <a class=" !text-base {{ request()->routeIs('app.settings') ? 'active' : '' }}" href="{{ route('app.settings') }}">
                        <i class="icon-[mdi--settings-outline] text-2xl"></i>
                        {{ __('profile.links.settings') }}
                    </a>
                </li>
                <li>
                    <a class=" !text-base {{ request()->routeIs('app.billing') ? 'active' : '' }}" href="{{ route('app.billing') }}">
                        <i class="icon-[mdi--payment] text-2xl"></i>
                        {{ __('profile.links.billing') }}
                    </a>
                </li>
            </ul>
        </div>

        <div class="lg:col-span-3 card bg-white lg:p-8 p-2">

            {{ $slot }}

        </div>
    </div>
</x-layouts.app>
