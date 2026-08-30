<x-layouts.app :title="__('base.terms_of_service')" :metaDescription="__('base.terms_of_service_description')">

    @push('meta')
    <meta name="keywords" content="terms of service, user agreement, platform rules, {{ config('app.name') }}">
    @endpush

    @php $isRtl = ($direction ?? 'ltr') === 'rtl'; @endphp

    <div dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="min-h-screen bg-gray-50 py-12 lg:py-24">
        <div class="container mx-auto px-4 max-w-4xl">

            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-2 text-sm font-medium mb-6">
                <a href="{{ route('home') }}" class="text-gray-500 hover:text-[#000033] transition-colors">
                    {{ __('base.home') }}
                </a>
                <span class="text-gray-300">/</span>
                <span class="text-[#00cc99]">{{ __('base.terms_of_service') }}</span>
            </nav>

            {{-- Header --}}
            <div class="mb-8">
                <h1 class="text-2xl md:text-3xl font-extrabold text-[#000033] tracking-tight mb-6">
                    {{ __('base.terms_of_service') }}
                </h1>
                <div class="inline-flex items-center rounded-full bg-[#00cc99]/10 px-4 py-1.5 text-sm font-semibold text-[#00cc99]">
                    {{ __('base.last_updated') }}: {{ __('base.terms_last_updated') }}
                </div>
            </div>

            @php
                $bullet = function(array $items) {
                    $out = '<ul class="space-y-4 mt-6">';
                    foreach ($items as $item) {
                        $out .= '<li class="flex items-start gap-4">';
                        $out .= '<div class="w-2 h-2 rounded-full bg-[#00cc99] mt-2.5 flex-shrink-0"></div>';
                        $out .= '<span class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . e($item) . '</span>';
                        $out .= '</li>';
                    }
                    $out .= '</ul>';
                    return $out;
                };

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

                {{-- S1 --}}
                {!! sectionLayout('01', __('base.tos_s1_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mb-4">' . __('base.tos_s1_p1') . '</p>
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s1_p2') . '</p>
                ', true) !!}

                {{-- S2 --}}
                {!! sectionLayout('02', __('base.tos_s2_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s2_intro') . '</p>
                    ' . $bullet([__('base.tos_s2_item1'),__('base.tos_s2_item2'),__('base.tos_s2_item3'),__('base.tos_s2_item4'),__('base.tos_s2_item5'),__('base.tos_s2_item6'),__('base.tos_s2_item7')]) . '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mt-6">' . __('base.tos_s2_note') . '</p>
                ') !!}

                {{-- S3 --}}
                {!! sectionLayout('03', __('base.tos_s3_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s3_intro') . '</p>
                    ' . $bullet([__('base.tos_s3_item1'),__('base.tos_s3_item2'),__('base.tos_s3_item3'),__('base.tos_s3_item4'),__('base.tos_s3_item5')]) . '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mt-6">' . __('base.tos_s3_note1') . '</p>
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mt-4">' . __('base.tos_s3_note2') . '</p>
                ') !!}

                {{-- S4 --}}
                {!! sectionLayout('04', __('base.tos_s4_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s4_intro') . '</p>
                    ' . $bullet([__('base.tos_s4_item1'),__('base.tos_s4_item2'),__('base.tos_s4_item3'),__('base.tos_s4_item4'),__('base.tos_s4_item5')]) . '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mt-6">' . __('base.tos_s4_note') . '</p>
                ') !!}

                {{-- S5 --}}
                {!! sectionLayout('05', __('base.tos_s5_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s5_intro') . '</p>
                    ' . $bullet([__('base.tos_s5_item1'),__('base.tos_s5_item2'),__('base.tos_s5_item3'),__('base.tos_s5_item4')]) . '
                    <div class="bg-gray-50 p-4 md:p-5 rounded-xl mt-6">
                        <h3 class="text-sm font-bold text-[#00cc99] uppercase tracking-widest mb-4">' . __('base.tos_s5_refund_title') . '</h3>
                        ' . $bullet([__('base.tos_s5_refund1'),__('base.tos_s5_refund2'),__('base.tos_s5_refund3'),__('base.tos_s5_refund4')]) . '
                    </div>
                ') !!}

                {{-- S6 --}}
                {!! sectionLayout('06', __('base.tos_s6_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s6_intro') . '</p>
                    ' . $bullet([__('base.tos_s6_item1'),__('base.tos_s6_item2'),__('base.tos_s6_item3'),__('base.tos_s6_item4'),__('base.tos_s6_item5')]) . '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mt-6">' . __('base.tos_s6_note') . '</p>
                ') !!}

                {{-- S7 --}}
                {!! sectionLayout('07', __('base.tos_s7_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mb-4">' . __('base.tos_s7_p1') . '</p>
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s7_p2') . '</p>
                    ' . $bullet([__('base.tos_s7_item1'),__('base.tos_s7_item2'),__('base.tos_s7_item3'),__('base.tos_s7_item4'),__('base.tos_s7_item5')]) . '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mt-6">' . __('base.tos_s7_note') . '</p>
                ') !!}

                {{-- S8 --}}
                {!! sectionLayout('08', __('base.tos_s8_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s8_intro') . '</p>
                    ' . $bullet([__('base.tos_s8_item1'),__('base.tos_s8_item2'),__('base.tos_s8_item3'),__('base.tos_s8_item4'),__('base.tos_s8_item5'),__('base.tos_s8_item6'),__('base.tos_s8_item7'),__('base.tos_s8_item8')]) . '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mt-6">' . __('base.tos_s8_note') . '</p>
                ') !!}

                {{-- S9 --}}
                {!! sectionLayout('09', __('base.tos_s9_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s9_intro') . '</p>
                    ' . $bullet([__('base.tos_s9_item1'),__('base.tos_s9_item2'),__('base.tos_s9_item3')]) . '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mt-6">' . __('base.tos_s9_note') . '</p>
                ') !!}

                {{-- S10 --}}
                {!! sectionLayout('10', __('base.tos_s10_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">
                        ' . __('base.tos_s10_text') . '
                        <a href="' . route('legal.privacy-policy') . '" class="font-bold text-[#00cc99] hover:underline">' . __('base.tos_s10_link') . '</a>
                    </p>
                ') !!}

                {{-- S11 --}}
                {!! sectionLayout('11', __('base.tos_s11_title'), '
                    <div class="bg-[#000033] p-4 md:p-5 rounded-xl text-white">
                        <p class="text-sm md:text-[15px] font-bold mb-4">' . __('base.tos_s11_heading') . '</p>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-4">
                                <div class="w-2 h-2 rounded-full bg-[#00cc99] mt-2.5 flex-shrink-0"></div>
                                <span class="text-sm md:text-[15px] text-gray-300 leading-relaxed">' . __('base.tos_s11_item1') . '</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-2 h-2 rounded-full bg-[#00cc99] mt-2.5 flex-shrink-0"></div>
                                <span class="text-sm md:text-[15px] text-gray-300 leading-relaxed">' . __('base.tos_s11_item2') . '</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-2 h-2 rounded-full bg-[#00cc99] mt-2.5 flex-shrink-0"></div>
                                <span class="text-sm md:text-[15px] text-gray-300 leading-relaxed">' . __('base.tos_s11_item3') . '</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-2 h-2 rounded-full bg-[#00cc99] mt-2.5 flex-shrink-0"></div>
                                <span class="text-sm md:text-[15px] text-gray-300 leading-relaxed">' . __('base.tos_s11_item4') . '</span>
                            </li>
                        </ul>
                        <p class="text-sm md:text-[15px] text-gray-400 leading-relaxed mt-6">' . __('base.tos_s11_note') . '</p>
                    </div>
                ') !!}

                {{-- S12 --}}
                {!! sectionLayout('12', __('base.tos_s12_title'), '
                    <div class="bg-[#000033] p-4 md:p-5 rounded-xl text-white">
                        <p class="text-sm md:text-[15px] font-bold mb-4">' . __('base.tos_s12_heading') . '</p>
                        <ul class="space-y-4">
                            <li class="flex items-start gap-4">
                                <div class="w-2 h-2 rounded-full bg-[#00cc99] mt-2.5 flex-shrink-0"></div>
                                <span class="text-sm md:text-[15px] text-gray-300 leading-relaxed">' . __('base.tos_s12_item1') . '</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-2 h-2 rounded-full bg-[#00cc99] mt-2.5 flex-shrink-0"></div>
                                <span class="text-sm md:text-[15px] text-gray-300 leading-relaxed">' . __('base.tos_s12_item2') . '</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-2 h-2 rounded-full bg-[#00cc99] mt-2.5 flex-shrink-0"></div>
                                <span class="text-sm md:text-[15px] text-gray-300 leading-relaxed">' . __('base.tos_s12_item3') . '</span>
                            </li>
                            <li class="flex items-start gap-4">
                                <div class="w-2 h-2 rounded-full bg-[#00cc99] mt-2.5 flex-shrink-0"></div>
                                <span class="text-sm md:text-[15px] text-gray-300 leading-relaxed">' . __('base.tos_s12_item4') . '</span>
                            </li>
                        </ul>
                        <p class="text-sm md:text-[15px] text-gray-400 leading-relaxed mt-6">' . __('base.tos_s12_note') . '</p>
                    </div>
                ') !!}

                {{-- S13 --}}
                {!! sectionLayout('13', __('base.tos_s13_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s13_intro') . '</p>
                    ' . $bullet([__('base.tos_s13_item1'),__('base.tos_s13_item2'),__('base.tos_s13_item3'),__('base.tos_s13_item4')]) . '
                ') !!}

                {{-- S14 --}}
                {!! sectionLayout('14', __('base.tos_s14_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s14_intro') . '</p>
                    ' . $bullet([__('base.tos_s14_item1'),__('base.tos_s14_item2'),__('base.tos_s14_item3')]) . '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mt-6">' . __('base.tos_s14_note') . '</p>
                ') !!}

                {{-- S15 --}}
                {!! sectionLayout('15', __('base.tos_s15_title'), '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mb-4">' . __('base.tos_s15_p1') . '</p>
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed">' . __('base.tos_s15_intro') . '</p>
                    ' . $bullet([__('base.tos_s15_item1'),__('base.tos_s15_item2'),__('base.tos_s15_item3')]) . '
                    <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mt-6">' . __('base.tos_s15_note') . '</p>
                ') !!}

                {{-- S16 --}}
                <details class="group bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                    <summary class="flex items-center justify-between p-4 md:p-5 cursor-pointer select-none outline-none hover:bg-gray-50/50 transition-colors list-none [&::-webkit-details-marker]:hidden">
                        <div class="flex items-center gap-3">
                            <span class="text-2xl font-black text-gray-200">16</span>
                            <h2 class="text-sm md:text-[15px] font-bold text-[#000033]">{{ __('base.tos_s16_title') }}</h2>
                        </div>
                        <div class="w-7 h-7 rounded-full bg-gray-50 flex items-center justify-center flex-shrink-0 group-open:rotate-180 transition-transform duration-300">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </summary>
                    <div class="px-4 md:px-5 pb-5 pt-2 border-t border-gray-50">
                        <p class="text-sm md:text-[15px] text-gray-600 leading-relaxed mb-6">{{ __('base.tos_s16_intro') }}</p>
                        <div class="bg-gray-50 p-4 md:p-5 rounded-2xl">
                            <p class="font-bold text-xl text-[#000033] mb-4">{{ config('app.name') }}</p>
                            <div class="space-y-4">
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <span class="text-gray-500 font-medium">{{ __('base.tos_s16_general') }}:</span>
                                    <a href="mailto:info@portal365.org" class="font-bold text-[#00cc99] hover:text-[#000033] transition-colors">info@portal365.org</a>
                                </div>
                                <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                                    <span class="text-gray-500 font-medium">{{ __('base.tos_s16_support') }}:</span>
                                    <a href="mailto:support@portal365.org" class="font-bold text-[#00cc99] hover:text-[#000033] transition-colors">support@portal365.org</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

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
