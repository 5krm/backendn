@props(['langRedirect' => url()->current()])

<nav class="sticky top-0 left-0 right-0 z-50 bg-base-100 shadow-md">
    <div class="navbar container">
        <div class="flex-1 flex items-center">
            <a href="{{ route('courses') }}" class="text-xl block ">
                <img width="120" class="w-28 block mx-auto" src="{{ asset('assets/svg/logo/ngo-academy-logo-en.svg') }}"
                    alt="">
            </a>
        </div>

        <div class="flex-none">
            <ul class="menu menu-horizontal items-center p-0 lg:flex hidden ">
                <li class="  {{ Route::currentRouteName() === 'courses' ? 'text-primary' : '' }}">
                    <a href="{{ route('courses') }}">{{ __('base.explore') }}</a>
                </li>
                <li class="  {{ Route::currentRouteName() === 'dashboard' ? 'text-primary' : '' }}">
                    <a href="{{ route('dashboard') }}">{{ __('base.myCourses') }}</a>
                </li>
                @if (App::isLocale('en'))
                    <li><a
                            href="{{ route('languages.switch', ['lang' => 'ar', 'redirect' => $langRedirect]) }}">عربي</a>
                    </li>
                @else
                    <li><a
                            href="{{ route('languages.switch', ['lang' => 'en', 'redirect' => $langRedirect]) }}">English</a>
                    </li>
                @endif
                @if (auth()->check())
                    <div class="dropdown dropdown-end ms-2">
                        <div class="flex items-center gap-2" tabindex="0" role="button">
                            <div class="text-sm">👋 {{ __('base.hi') }} <span
                                    class="font-semibold">{{ auth()->user()->firstName() }}</span></div>

                            <div class="btn btn-ghost btn-circle avatar placeholder">
                                <div class="w-10  text-neutral-content rounded-full">
                                    <img class="object-cover" src="{{ auth()->user()->profile }}" />
                                </div>
                            </div>
                        </div>
                        <ul tabindex="0"
                            class="menu  dropdown-content pt-2 z-10 p-2 shadow bg-base-100 rounded-box w-52">
                            <li><a href="{{ route('app.profile') }}">{{ __('profile.links.profile') }}</a>
                            </li>
                            <li><a href="{{ route('app.settings') }}">{{ __('profile.links.settings') }}</a>
                            </li>
                            <li><a href="{{ route('auth.logout') }}">{{ __('auth.logout') }}</a></li>
                        </ul>
                    </div>
                @else
                    <li><a href="{{ route('auth.login') }}">
                            {{ __('auth.signin') }}
                        </a>
                    </li>
                    <li class="bg-primary text-white rounded-lg ms-2">
                        <a href="{{ route('auth.register') }}">
                            {{ __('auth.signup') }}
                        </a>
                    </li>
                @endif
            </ul>

            <ul class="menu menu-horizontal items-center p-0 lg:hidden">
                @if (auth()->check())
                    <div class="dropdown dropdown-end ms-2">
                        <div class="flex items-center gap-2" tabindex="0" role="button">
                            <div>مرحبا <span class="">{{ auth()->user()->firstName() }}</span> 👋</div>
                            <div class="btn btn-ghost btn-circle avatar placeholder">
                                <div class="w-10  text-neutral-content rounded-full">
                                    <img class="object-cover" src="{{ auth()->user()->profile }}" />
                                </div>
                            </div>
                        </div>
                        <ul tabindex="0"
                            class="menu  dropdown-content pt-2 z-10 p-2 shadow bg-base-100 rounded-box w-52">
                            <li><a href="{{ route('app.profile') }}">{{ __('profile.links.profile') }}</a>
                            </li>
                            <li><a href="{{ route('app.settings') }}">{{ __('profile.links.settings') }}</a>
                            </li>
                            <li><a href="{{ route('auth.logout') }}">{{ __('auth.logout') }}</a></li>
                        </ul>
                    </div>
                @else
                    <li><a href="{{ route('auth.login') }}">
                            {{ __('auth.signin') }}
                        </a>
                    </li>
                    <li class="bg-primary text-white rounded-lg ms-2">
                        <a href="{{ route('auth.register') }}">
                            {{ __('auth.signup') }}
                        </a>
                    </li>
                @endif
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button"
                        class="inline-flex lg:hidden ms-1 md:ms-3 h-10 w-10 items-center justify-center rounded-lg p-2.5 text-sm text-gray-500 hover:bg-gray-100 focus:outline-none ">
                        <span class="icon-[mdi--menu] size-5"></span>
                    </div>
                    <ul tabindex="0"
                        class="menu  dropdown-content mt-3 z-[1] p-2 shadow bg-base-100 rounded-box w-52">
                        <li class="  {{ Route::currentRouteName() === 'courses' ? 'text-primary' : '' }}">
                            <a href="{{ route('courses') }}">{{ __('base.explore') }}</a>
                        </li>
                        <li class="  {{ Route::currentRouteName() === 'dashboard' ? 'text-primary' : '' }}">
                            <a href="{{ route('dashboard') }}">{{ __('base.myCourses') }}</a>
                        </li>
                        @if (App::isLocale('en'))
                            <li class="mx-2 text-secondary"><a
                                    href="{{ route('languages.switch', ['lang' => 'ar', 'redirect' => $langRedirect]) }}">عربي</a>
                            </li>
                        @else
                            <li class="mx-2 text-secondary">
                                <a
                                    href="{{ route('languages.switch', ['lang' => 'en', 'redirect' => $langRedirect]) }}">English</a>
                            </li>
                        @endif

                    </ul>
                </div>
            </ul>
        </div>
    </div>
</nav>
