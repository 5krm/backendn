<x-layouts.app>
    <div class="min-h-screen ">

        <div class=" mb-6   flex justify-between items-center">
            <a href="{{ route('courses') }}" class="inline-flex items-center gap-2 text-primary hover:text-primary-300">
                @php $isRtl = (isset($direction) ? $direction === 'rtl' : (app()->getLocale() === 'ar')) @endphp
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 {{ $isRtl ? 'transform rotate-180' : '' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>

                {{ __('organization.back_to_courses') }}
            </a>
        </div>

        <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10">

            <section class="lg:col-span-8 space-y-6">
                {{-- Header & Main Info --}}
                <div class="bg-white border border-gray-200 rounded-xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="flex flex-col items-start gap-6 sm:flex-row">
                        <div class="relative mx-2 shrink-0">
                            <img class="h-24 w-24 rounded-full border-4 border-white shadow-md object-cover bg-gray-200"
                                src="{{ $tutor->profile_image }}" alt="{{ $tutor->localized_name }}">
                        </div>

                        <div class="flex-1">
                            <h1 class="mb-2 text-4xl font-bold text-gray-900">{{ $tutor->localized_name }}</h1>

                            @if ($tutor->user?->localized_bio)
                                <p class="mb-6 text-gray-700">{{ $tutor->user->localized_bio }}</p>
                            @endif

                            <div class="mb-6 flex flex-wrap gap-6">
                                <div class="flex items-center gap-2">
                                    <span class="icon-[mdi--book-open-variant] h-5 w-5 text-emerald-600"
                                        aria-hidden="true"></span>
                                    <span class="text-lg font-semibold text-gray-900">{{ $stats['courses_count'] }}</span>
                                    <span class="text-sm text-gray-600">({{ __('tutor.courses') }})</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="icon-[mdi--account-group] h-5 w-5 text-blue-600"
                                        aria-hidden="true"></span>
                                    <span class="text-lg font-semibold text-gray-900">
                                        {{ number_format($stats['students_count']) }}
                                    </span>
                                    <span class="text-sm text-gray-600">({{ __('tutor.students._') }})</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="icon-[mdi--play-circle] h-5 w-5 text-indigo-600"
                                        aria-hidden="true"></span>
                                    <span class="text-lg font-semibold text-gray-900">{{ $stats['lessons_count'] }}</span>
                                    <span class="text-sm text-gray-600">({{ __('tutor.lessons') }})</span>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="icon-[mdi--check-decagram] h-5 w-5 text-amber-500"
                                        aria-hidden="true"></span>
                                    <span class="text-lg font-semibold text-gray-900">
                                        {{ round($stats['completion_rate']) }}%</span>
                                    <span class="text-sm text-gray-600">({{ __('tutor.completion') }})</span>
                                </div>
                            </div>

                            @php
                                $socialLinks = $tutor->user?->socialLinks ?? collect();
                                $phone = $tutor->user?->phone;
                            @endphp
                            @if ($socialLinks->isNotEmpty()  )
                                <div class="flex gap-3 flex-wrap items-center pt-6 border-t border-gray-200">
                                    @foreach ($socialLinks as $link)
                                        <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                                            title="{{ $link->platformLabel() }}"
                                            class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-primary hover:text-white transition-colors">
                                            <x-social-platform-icon
                                                :platform="$link->platform ?? ($link->getAttributes()['platform'] ?? null)"
                                                class="w-5 h-5" />
                                        </a>
                                    @endforeach
                                    <!-- @if ($phone)
                                        <a href="tel:{{ preg_replace('/\s+/', '', $phone) }}"
                                            class="ms-auto px-4 py-2 bg-white text-gray-700 rounded-lg shadow-sm hover:bg-gray-50 border border-gray-200 font-medium transition flex items-center gap-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z">
                                                </path>
                                            </svg>
                                            {{ $phone }}
                                        </a>
                                    @endif -->
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Courses --}}
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                    <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('tutor.featured_courses') }}</h2>
                    </div>

                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        @foreach ($courses as $course)
                            <x-compact-course-card
                                :course="$course"
                                :wishlist-key="'tutor-wishlist-' . $course['data']->id"
                            />
                        @endforeach
                    </div>
                </div>
            </section>

            {{-- Sidebar: General Info + Professional --}}
            <aside class="lg:col-span-4 space-y-4">
                <div class="bg-white border border-gray-200 rounded-xl px-4">
                    <div class="p-6 space-y-4 border-b border-gray-200">
                        <div class="text-sm font-bold text-base-content/70">{{ __('tutor.general_info') }}</div>

                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-4">
                                <div class="text-xs font-extrabold uppercase tracking-wider text-base-content/40">
                                    {{ __('auth.phone') }}
                                </div>
                                <div class="font-semibold text-base-content text-right" dir="ltr">
                                    @if ($tutor->user?->phone)
                                        <a href="tel:{{ preg_replace('/\s+/', '', $tutor->user->phone) }}"
                                            class="hover:text-primary transition-colors">
                                            {{ $tutor->user->phone }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-start justify-between gap-4">
                                <div class="text-xs font-extrabold uppercase tracking-wider text-base-content/40">
                                    {{ __('profile.fields.job_title') }}
                                </div>
                                <div class="font-semibold text-base-content text-right">
                                    {{ $tutor->user?->localized_job_title ?: '-' }}
                                </div>
                            </div>

                            @if ($tutor->user?->organization)
                                <div class="flex items-start justify-between gap-4">
                                    <div class="text-xs font-extrabold uppercase tracking-wider text-base-content/40">
                                        {{ __('tutor.organization') }}
                                    </div>
                                    <div class="font-semibold text-base-content text-right">
                                        @if ($tutor->user->organization->slug)
                                            <a href="{{ route('organization.index', $tutor->user->organization) }}"
                                                class="hover:text-primary transition-colors">
                                                {{ $tutor->user->organization->name }}
                                            </a>
                                        @else
                                            {{ $tutor->user->organization->name }}
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="p-6 space-y-4">
                        <div class="text-sm font-bold text-base-content/70">{{ __('tutor.professional') }}</div>

                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-4">
                                <div class="text-xs font-extrabold uppercase tracking-wider text-base-content/40">
                                    {{ __('tutor.experience') }}
                                </div>
                                <div class="font-semibold text-base-content text-right">
                                    @if ($tutor->experience_years)
                                        {{ $tutor->experience_years }} {{ __('tutor.years') }}
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-start justify-between gap-4">
                                <div class="text-xs font-extrabold uppercase tracking-wider text-base-content/40">
                                    {{ __('tutor.hourly_rate') }}
                                </div>
                                <div class="font-semibold text-base-content text-right">
                                    @if ($tutor->hourly_rate)
                                        ${{ $tutor->hourly_rate }}
                                        <span class="text-xs text-base-content/50 font-normal">{{ __('tutor.per_hour') }}</span>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-start justify-between gap-4">
                                <div class="text-xs font-extrabold uppercase tracking-wider text-base-content/40">
                                    {{ __('tutor.specialization') }}
                                </div>
                                <div class="font-semibold text-base-content text-right">
                                    {{ $tutor->localized_specialization ?: '-' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
        </div>
    </div>
</x-layouts.app>
