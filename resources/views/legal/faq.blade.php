<x-layouts.app :title="__('base.faq')" :metaDescription="__('seo.faq_description')">

    @push('meta')
    <meta name="keywords" content="FAQ, frequently asked questions, help, {{ config('app.name') }}, أسئلة شائعة">
    @endpush

    @php $isRtl = ($direction ?? 'ltr') === 'rtl'; @endphp

    <div dir="{{ $isRtl ? 'rtl' : 'ltr' }}" class="min-h-screen bg-white">
        <div class="container mx-auto px-4 py-12">
            <div class="max-w-3xl mx-auto">

                {{-- Breadcrumb --}}
                <nav class="flex items-center gap-2 text-sm mb-8 text-start">
                    <a href="{{ route('home') }}" class="transition-opacity" style="color:#000033;opacity:.55;" onmouseover="this.style.opacity=1" onmouseout="this.style.opacity=.55">
                        {{ __('base.home') }}
                    </a>
                    <span style="color:#000033;opacity:.3;">/</span>
                    <span class="font-medium" style="color:#00cc99;">{{ __('base.faq') }}</span>
                </nav>

                {{-- Header --}}
                <div class="mb-10">
                    <div class="flex items-center gap-4 mb-2">
                        <div class="w-1 h-10 rounded-full flex-shrink-0" style="background:#00cc99;"></div>
                        <h1 class="text-4xl font-bold" style="color:#000033;">{{ __('base.faq') }}</h1>
                    </div>
                    <p class="text-base mt-1 ms-5" style="color:#000033;opacity:.5;">
                        {{ __('base.faq_subtitle') }}
                    </p>
                </div>

                {{-- Category Tabs --}}
                <div class="flex flex-wrap gap-2 mb-8" id="faq-tabs">
                    @php
                        $categories = [
                            'enrollment'   => __('base.enrollment_registration'),
                            'content'      => __('base.course_content'),
                            'certificates' => __('base.assessments_certificates'),
                            'platform'     => __('base.platform_features'),
                            'support'      => __('base.support_help'),
                        ];
                    @endphp

                    @foreach($categories as $key => $label)
                        <button
                            onclick="switchTab('{{ $key }}')"
                            id="tab-{{ $key }}"
                            class="px-4 py-2 rounded-full text-sm font-medium transition-all"
                            style="{{ $loop->first ? 'background:#000033;color:#fff;' : 'background:#e9f3ff;color:#000033;' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>

                {{-- FAQ Sections --}}
                @php
                    $sections = [
                        'enrollment' => [
                            [__('base.faq_register_question'), __('base.faq_register_answer')],
                            [__('base.faq_registration_fee_question'), __('base.faq_registration_fee_answer')],
                            [__('base.faq_multiple_users_question'), __('base.faq_multiple_users_answer')],
                        ],
                        'content' => [
                            [__('base.faq_content_type_question'), __('base.faq_content_type_answer')],
                            [__('base.faq_download_materials_question'), __('base.faq_download_materials_answer')],
                            [__('base.faq_languages_question'), __('base.faq_languages_answer')],
                            [__('base.faq_course_duration_question'), __('base.faq_course_duration_answer')],
                        ],
                        'certificates' => [
                            [__('base.faq_assessments_work_question'), __('base.faq_assessments_work_answer')],
                            [__('base.faq_retake_assessment_question'), __('base.faq_retake_assessment_answer')],
                            [__('base.faq_receive_certificate_question'), __('base.faq_receive_certificate_answer')],
                            [__('base.faq_certificates_recognized_question'), __('base.faq_certificates_recognized_answer')],
                        ],
                        'platform' => [
                            [__('base.faq_track_progress_question'), __('base.faq_track_progress_answer')],
                            [__('base.faq_discussion_forum_question'), __('base.faq_discussion_forum_answer')],
                            [__('base.faq_mobile_access_question'), __('base.faq_mobile_access_answer')],
                            [__('base.faq_offline_access_question'), __('base.faq_offline_access_answer')],
                        ],
                        'support' => [
                            [__('base.faq_get_support_question'), __('base.faq_get_support_answer')],
                            [__('base.faq_technical_issue_question'), __('base.faq_technical_issue_answer')],
                            [__('base.faq_platform_tutorials_question'), __('base.faq_platform_tutorials_answer')],
                        ],
                    ];
                @endphp

                <div id="faq-content" class="mb-6">
                    @foreach($sections as $sectionKey => $items)
                        <div id="section-{{ $sectionKey }}" class="{{ $loop->first ? '' : 'hidden' }}">
                            <div class="rounded-2xl overflow-hidden" style="box-shadow:0 2px 16px rgba(0,0,51,.07);">
                                @foreach($items as $i => $item)
                                    <div class="faq-item {{ $i > 0 ? 'border-t' : '' }}" style="border-color:#e9f3ff;background:#fff;">
                                        <button
                                            onclick="toggleFaq(this)"
                                            class="w-full flex items-center justify-between gap-4 p-6 text-start"
                                            style="color:#000033;">
                                            <span class="font-semibold text-base leading-snug">{{ $item[0] }}</span>
                                            <span class="faq-icon flex-shrink-0 w-7 h-7 rounded-full flex items-center justify-center transition-all" style="background:#e9f3ff;color:#00cc99;">
                                                <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/>
                                                </svg>
                                            </span>
                                        </button>
                                        <div class="faq-answer hidden px-6 pb-6 text-start">
                                            <p class="text-sm leading-relaxed" style="color:#000033;opacity:.65;">{{ $item[1] }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- CTA --}}
                <div class="rounded-2xl p-8 mb-6 flex flex-col items-center text-center gap-4" style="background:#000033;">
                    <p class="text-lg font-bold text-white">{{ __('base.faq_didnt_find_answer') }}</p>
                    <p class="text-sm" style="color:rgba(255,255,255,.6);">{{ __('base.faq_contact_us_text') }}</p>
                    <a href="{{ route('legal.contact') }}"
                       class="px-6 py-2.5 rounded-full text-sm font-semibold transition-opacity"
                       style="background:#00cc99;color:#000033;"
                       onmouseover="this.style.opacity='.85'" onmouseout="this.style.opacity='1'">
                        {{ __('base.contactus') }}
                    </a>
                </div>

                {{-- Legal Nav --}}
                <div class="rounded-2xl p-6" style="background:#fff;box-shadow:0 2px 16px rgba(0,0,51,.07);">
                    <p class="text-xs font-semibold uppercase tracking-widest mb-4 text-start" style="color:#000033;opacity:.4;">{{ __('base.other_legal_pages') }}</p>
                    <x-legal-nav />
                </div>

            </div>
        </div>
    </div>

    <script>
        function switchTab(key) {
            document.querySelectorAll('#faq-content > div').forEach(s => s.classList.add('hidden'));
            document.querySelectorAll('#faq-tabs button').forEach(b => {
                b.style.background = '#e9f3ff';
                b.style.color = '#000033';
            });
            document.getElementById('section-' + key).classList.remove('hidden');
            var tab = document.getElementById('tab-' + key);
            tab.style.background = '#000033';
            tab.style.color = '#fff';
        }

        function toggleFaq(btn) {
            var answer = btn.nextElementSibling;
            var isOpen = !answer.classList.contains('hidden');

            btn.closest('[id^="section-"]').querySelectorAll('.faq-answer').forEach(function(a) {
                a.classList.add('hidden');
            });
            btn.closest('[id^="section-"]').querySelectorAll('.faq-icon svg').forEach(function(i) {
                i.style.transform = 'rotate(0deg)';
                i.closest('.faq-icon').style.background = '#e9f3ff';
                i.closest('.faq-icon').style.color = '#00cc99';
            });

            if (!isOpen) {
                answer.classList.remove('hidden');
                btn.querySelector('.faq-icon svg').style.transform = 'rotate(180deg)';
                btn.querySelector('.faq-icon').style.background = '#00cc99';
                btn.querySelector('.faq-icon').style.color = '#fff';
            }
        }
    </script>

</x-layouts.app>
