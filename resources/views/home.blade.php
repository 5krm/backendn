<x-layouts.main :title="__('seo.home_title')" :metaDescription="__('seo.home_description')">
    @push('meta')
        <meta name="keywords" content="{{ config('app.name') }}, online learning, NGO, courses, تعلم, دورات, أكاديمية">
    @endpush

    <x-navbar name="Login" :image="'images/logo.png'" />
    @if (auth()->check() && !auth()->user()->hasVerifiedEmail())
        <div class="mx-auto lg:px-48 md:px-16 px-4 my-4">
            <div role="alert" class="alert bg-[#fde68a]  ">
                <i class="icon-[mdi--information-outline] size-5"></i>
                <span class="text-sm font-medium text-base-700 ">{!! __('auth.account-verification.reminder', ['link' => route('verification.notice')]) !!}</span>
            </div>
        </div>
    @endif

    <x-footer></x-footer>
</x-layouts.main>
