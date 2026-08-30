<x-layouts.app :title="__('base.cookie_policy')" :metaDescription="__('base.cookie_policy_description')">

    @push('meta')
    <meta name="keywords" content="cookie policy, cookies, tracking, {{ config('app.name') }}">
    @endpush

    @php
        $isRtl = app()->getLocale() === 'ar';
    @endphp

    <div dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="min-h-screen bg-gray-50 py-12 lg:py-24">
        <div class="container mx-auto px-4 max-w-4xl">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm font-medium mb-6">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-[#000033] transition-colors">
                    {{ __('base.home') }}
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-[#00cc99]">{{ __('base.cookie_policy') }}</span>
            </nav>

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#000033] tracking-tight mb-6">
                    {{ __('base.cookie_policy') }}
                </h1>
                <div class="inline-flex items-center rounded-full bg-[#00cc99]/10 px-4 py-1.5 text-sm font-semibold text-[#00cc99]">
                    {{ __('base.last_updated') }}: January 15, 2026
                </div>
            </div>

            @php
                function sectionLayout($number, $title, $content, $isOpen = false) {
                    $openAttr = $isOpen ? 'open' : '';
                    return '
                    <details class="group bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden" ' . $openAttr . '>
                        <summary class="flex items-center justify-between p-4 md:p-5 cursor-pointer select-none outline-none hover:bg-gray-50/50 transition-colors list-none [&::-webkit-details-marker]:hidden">
                            <div class="flex items-center gap-3">
                                <span class="text-2xl font-black text-gray-200">' . $number . '</span>
                                <h2 class="text-sm md:text-[15px] font-bold text-[#000033]">' . $title . '</h2>
                            </div>
                            <div class="w-7 h-7 rounded-full bg-gray-50 flex items-center justify-center flex-shrink-0 group-open:rotate-180 transition-transform duration-300">
                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </summary>
                        <div class="px-4 md:px-5 pb-5 pt-2 border-t border-gray-50">
                            ' . $content . '
                        </div>
                    </details>';
                }
            @endphp

            <div class="space-y-4 lg:space-y-6">

                {{-- Section 1 --}}
                {!! sectionLayout('01', __('base.what_are_cookies'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.what_are_cookies_text') . '</p>
                ', true) !!}

                {{-- Section 2 --}}
                {!! sectionLayout('02', __('base.how_we_use_cookies'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.how_we_use_cookies_text') . '</p>
                ') !!}

                {{-- Section 3 --}}
                {!! sectionLayout('03', __('base.types_of_cookies'), '
                    <div class="space-y-6">
                        <div class="bg-gray-50 p-4 md:p-5 rounded-xl border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-3 h-3 rounded-full bg-[#00cc99]"></div>
                                <h3 class="text-xl font-bold text-[#000033]">' . __('base.essential_cookies') . '</h3>
                            </div>
                            <p class="text-gray-600 leading-relaxed">' . __('base.essential_cookies_text') . '</p>
                        </div>

                        <div class="bg-gray-50 p-4 md:p-5 rounded-xl border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-3 h-3 rounded-full bg-[#00cc99]"></div>
                                <h3 class="text-xl font-bold text-[#000033]">' . __('base.performance_cookies') . '</h3>
                            </div>
                            <p class="text-gray-600 leading-relaxed">' . __('base.performance_cookies_text') . '</p>
                        </div>

                        <div class="bg-gray-50 p-4 md:p-5 rounded-xl border border-gray-100 shadow-sm">
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-3 h-3 rounded-full bg-[#00cc99]"></div>
                                <h3 class="text-xl font-bold text-[#000033]">' . __('base.functionality_cookies') . '</h3>
                            </div>
                            <p class="text-gray-600 leading-relaxed">' . __('base.functionality_cookies_text') . '</p>
                        </div>
                    </div>
                ') !!}

                {{-- Section 4 --}}
                {!! sectionLayout('04', __('base.managing_cookies'), '
                    <div class="bg-[#000033] p-4 md:p-5 rounded-xl text-white">
                        <p class="text-sm md:text-[15px] leading-relaxed text-gray-300">' . __('base.managing_cookies_text') . '</p>
                    </div>
                ') !!}

                {{-- Section 5 --}}
                {!! sectionLayout('05', __('base.contact_us'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.contact_us_cookies_text') . '</p>
                ') !!}

            </div>

            {{-- Legal Navigation --}}
            <div class="mt-12 pt-8 border-t border-gray-200">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6">
                    {{ __('base.other_legal_pages') }}
                </h3>
                <div class="bg-white p-4 md:p-5 rounded-xl border border-gray-100 shadow-sm">
                    <x-legal-nav />
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
