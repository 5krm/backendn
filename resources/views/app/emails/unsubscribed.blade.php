<x-layouts.main>
    <main class="container mx-auto {{ $fullwidth ?? false ? '' : 'lg:px-48 px-4' }} py-3">
        <div class="flex justify-between items-center">
            <div class="flex-1 flex items-center">
                <a href="{{ route('courses') }}" class="text-xl block ">
                    <img width="120" class="w-28 block mx-auto"
                        src="{{ asset('assets/svg/logo/ngo-academy-logo-en.svg') }}" alt="">
                </a>
            </div>
            <ul class="menu menu-horizontal items-center p-0 lg:flex hidden text-primary">
                <li class="mx-1 "> <a class=""
                        href="https://www.portal365.org/{{ App::isLocale('ar') ? 'ar' : 'en' }}/ContactUs">{{ __('base.contactus') }}</a>
                </li>
                <i class="text-gray-300 text-xs">●</i>
                <li class="mx-1"> <a class="">{{ __('home.term_of_use') }}</a> </li>
                <i class="text-gray-300 text-xs">●</i>
                @if (App::isLocale('en'))
                    <li class="mx-1 "><a
                            href="{{ route('languages.switch', ['lang' => 'ar', 'redirect' => url()->current()]) }}">عربي</a>
                    </li>
                @else
                    <li class="mx-1 ">
                        <a
                            href="{{ route('languages.switch', ['lang' => 'en', 'redirect' => url()->current()]) }}">English</a>
                    </li>
                @endif
            </ul>
        </div>

        <div class="min-h-screen  grid grid-cols-1 md:grid-cols-2 items-center py-12">
            <div class="w-full flex justify-center ">
                <img src="/assets/images/inbox-cleanup.png" alt="guide-img" width="450">
            </div>
            <div class="w-full">
                <h1 class="mb-5 text-2xl font-bold">{{ __('subscription.unsubscribe_title', ['type' => __($type)]) }}
                </h1>
                <p class="my-2">
                    {{ __('subscription.stop_sending', ['type' => __($type)]) }}.
                </p>
                <p class="my-2">
                <form method="POST" action="{{ route('email.subscribe', ['type' => $type, 'token' => $token]) }}">
                    @csrf
                    {{ __('subscription.mistake') }} <button type="submit"
                        class="btn btn-sm btn-ghost font-bold text-primary ">
                        {{ __('subscription.resubscribe') }}
                    </button>

                </form>

                </p>
                <p class="my-2">
                    {!! __('subscription.manage_settings', ['app' => config('app.name'), 'link' => route('app.settings')]) !!}
                </p>
            </div>

            <div class="col-span-2  text-center text-sm ">
                <div class="divider"></div>
                <p>
                    {{ __('errors.need_help') }} <a
                        href="https://www.portal365.org/{{ App::isLocale('ar') ? 'ar' : 'en' }}/ContactUs"
                        class="link">{{ __('base.contactus') }}</a>
                </p>
                <a href="{{ route('dashboard') }}" class="link text-md mx-3 items-center flex  justify-center  mt-5">
                    @if (($direction ?? 'ltr') == 'rtl')
                        <i class="icon-[mdi--arrow-right] size-4 me-1"></i>
                    @else
                        <i class="icon-[mdi--arrow-left] size-4 me-1"></i>
                    @endif
                    {{ __('errors.back') }}
                </a>
            </div>
        </div>
    </main>
</x-layouts.main>