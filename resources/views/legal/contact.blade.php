<x-layouts.app
    :title="__('base.contactus')"
    :metaDescription="__('seo.contact_description')"
>
    @push('meta')
    <meta name="keywords" content="contact, support, {{ config('app.name') }}, تواصل, دعم">
    @endpush

    @php
        $isRtl = ($direction ?? 'ltr') === 'rtl';
    @endphp

    <div dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="min-h-screen bg-gray-50 py-12 lg:py-24">
        <div class="container mx-auto px-4 max-w-4xl">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm font-medium mb-12">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-[#000033] transition-colors">
                    {{ __('base.home') }}
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-[#00cc99]">{{ __('base.contactus') }}</span>
            </nav>

            {{-- Page Header --}}
            <div class="mb-10 text-start">
                <h1 class="text-3xl md:text-4xl font-extrabold text-[#000033] tracking-tight mb-4">
                    {{ __('base.contactus') }}
                </h1>
                <p class="text-base text-gray-500 leading-relaxed max-w-2xl">
                    {{ __('base.contact_subtitle') }}
                </p>
            </div>

            {{-- Contact Channels --}}
            <div class="grid sm:grid-cols-2 gap-4 lg:gap-6 mb-8">

                {{-- General Email --}}
                <a href="mailto:info@portal365.org"
                   class="flex items-start gap-4 bg-white rounded-xl p-6 border border-gray-100 shadow-sm text-start">
                    <div class="w-12 h-12 rounded-full bg-[#00cc99]/10 flex items-center justify-center flex-shrink-0 text-[#00cc99]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1 pt-1 text-start">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">{{ __('base.general_inquiries') }}</p>
                        <p class="text-lg font-bold text-[#000033]">info@portal365.org</p>
                    </div>
                </a>

                {{-- Technical Support --}}
                <a href="mailto:support@portal365.org"
                   class="flex items-start gap-4 bg-white rounded-xl p-6 border border-gray-100 shadow-sm text-start">
                    <div class="w-12 h-12 rounded-full bg-[#00cc99]/10 flex items-center justify-center flex-shrink-0 text-[#00cc99]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 pt-1 text-start">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">{{ __('base.technical_support') }}</p>
                        <p class="text-lg font-bold text-[#000033]">support@portal365.org</p>
                    </div>
                </a>

                {{-- Response Time (Not clickable) --}}
                <div class="flex items-start gap-4 bg-white rounded-xl p-6 border border-gray-100 shadow-sm text-start">
                    <div class="w-12 h-12 rounded-full bg-[#00cc99]/10 flex items-center justify-center flex-shrink-0 text-[#00cc99]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 pt-1 text-start">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">{{ __('base.response_time') }}</p>
                        <p class="text-lg font-bold text-[#000033]">{{ __('base.within_24_hours') }}</p>
                    </div>
                </div>

                {{-- FAQ --}}
                <a href="{{ route('legal.faq') }}"
                   class="flex items-start gap-4 bg-white rounded-xl p-6 border border-gray-100 shadow-sm text-start">
                    <div class="w-12 h-12 rounded-full bg-[#00cc99]/10 flex items-center justify-center flex-shrink-0 text-[#00cc99]">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="flex-1 pt-1 text-start">
                        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">{{ __('base.quick_help') }}</p>
                        <p class="text-lg font-bold text-[#000033]">{{ __('base.faq') }}</p>
                    </div>
                </a>
            </div>

            {{-- Tip Banner --}}
            <div class="bg-[#000033] rounded-xl p-6 md:p-8 flex items-start gap-4 text-start">
                <svg class="w-6 h-6 flex-shrink-0 text-[#00cc99] mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <div class="flex-1 text-start">
                    <p class="font-bold text-lg text-white mb-2">{{ __('base.technical_support_tip_title') }}</p>
                    <p class="text-gray-300 leading-relaxed text-[15px]">{{ __('base.technical_support_tip') }}</p>
                </div>
            </div>

            {{-- Legal Navigation --}}
            <div class="mt-12 pt-8 border-t border-gray-200">
                <h3 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-6 text-start">
                    {{ __('base.other_legal_pages') }}
                </h3>
                <div class="bg-white p-6 md:p-8 rounded-xl border border-gray-100 shadow-sm text-start">
                    <x-legal-nav />
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
