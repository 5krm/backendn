<x-layouts.app>
    <div class="min-h-screen ">

        {{-- Header --}}
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

        {{-- Organization Header --}}
        <div class="mt-8 grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-10">

            <section class="lg:col-span-8 space-y-6">
                <div class="bg-white border border-gray-200 rounded-xl px-4 py-8 sm:px-6 lg:px-8">
                    <div class="mx-auto max-w-6xl">

                        <div class="flex flex-col items-start gap-6 sm:flex-row">

                            {{-- Organization Image --}}
                            <div class="relative  h-32 w-60 overflow-hidden rounded-lg bg-slate-100  pt-2 px-3 ">
                                <img src="{{ $organization->logo_url }}" alt="{{ $organization->name }}"
                                    class="h-full w-full object-contain">
                            </div>

                            {{-- Organization Info --}}
                            <div class="flex-1">

                                <h1 class="mb-2 text-4xl font-bold text-gray-900">
                                    {{ $organization->name }}
                                </h1>



                                @if ($organization->description)
                                    <p class="mb-6 text-gray-700">
                                        {{ $organization->description }}
                                    </p>
                                @endif

                                {{-- Stats 1 --}}
                                {{--  <div class="mb-6 flex flex-wrap gap-6">


                            <div class="flex items-center gap-2">
                                 <span class="text-sm text-gray-600">
                                    {{ __('organization.instructors') }} :
                                </span>
                                <span class="text-lg font-semibold text-gray-900">
                                    @if ($stats['instructors_count'] >= 1000)
                                        {{ number_format($stats['instructors_count'] / 1000, 0) }}K
                                    @else
                                        {{ number_format($stats['instructors_count']) }}
                                    @endif
                                </span>
                               
                  
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- <span aria-hidden="true">📚</span> -->
                                <span class="text-sm text-gray-600">
                                     {{ __('organization.courses') }} :
                                </span>
                                <span class="text-lg font-semibold text-gray-900">
                                    {{ $stats['courses_count'] }}
                                </span>
                               
                            </div>

                            <div class="flex items-center gap-2">
                                <!-- <span aria-hidden="true">🔔</span> -->
                                <span class="text-sm text-gray-600">
                                    {{ __('organization.students') }} :
                                </span>
                                <span class="text-lg font-semibold text-gray-900">
                                    {{ number_format($stats['students_count']) }}
                                </span>
                              
                            </div>
                            <div class="flex items-center gap-2">
                                 <span class="text-sm text-gray-600">
                                    {{ __('organization.followers') }} :
                                </span>
                                <span class="text-lg font-semibold text-gray-900">
                                    <livewire:organization-followers-stat :followers-count="$stats['followers_count']" />
                                </span>
                              
                            </div>

                        </div>  --}}
                                {{-- Stats 2 --}}
                                <div class="mb-6 flex flex-wrap gap-6">
                                    <div class="flex items-center gap-2">
                                        <span class="icon-[mdi--account-heart] h-5 w-5 text-amber-500"
                                            aria-hidden="true"></span>
                                        <span class="text-lg font-semibold text-gray-900">
                                            <livewire:organization-followers-stat :followers-count="$stats['followers_count']" />
                                        </span>
                                        <span class="text-sm text-gray-600">({{ __('organization.followers') }})</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="icon-[mdi--teach] h-5 w-5 text-indigo-600"
                                            aria-hidden="true"></span>
                                        <span class="text-lg font-semibold text-gray-900">
                                            @if ($stats['instructors_count'] >= 1000)
                                                {{ number_format($stats['instructors_count'] / 1000, 0) }}K
                                            @else
                                                {{ number_format($stats['instructors_count']) }}
                                            @endif
                                        </span>
                                        <span
                                            class="text-sm text-gray-600">({{ __('organization.instructors') }})</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="icon-[mdi--account-group] h-5 w-5 text-blue-600"
                                            aria-hidden="true"></span>
                                        <span class="text-lg font-semibold text-gray-900">
                                            {{ number_format($stats['students_count']) }}
                                        </span>
                                        <span class="text-sm text-gray-600">({{ __('organization.students') }})</span>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <span class="icon-[mdi--book-open-variant] h-5 w-5 text-emerald-600"
                                            aria-hidden="true"></span>
                                        <span class="text-lg font-semibold text-gray-900">
                                            {{ $stats['courses_count'] }}
                                        </span>
                                        <span class="text-sm text-gray-600">({{ __('organization.courses') }})</span>
                                    </div>
                                </div>

                                <div class="flex gap-3 flex-wrap items-center pt-6 border-t border-gray-200">
                                    <livewire:follow-organization-button :organization="$organization" />

                                </div>


                            </div>
                        </div>


                    </div>
                </div>
                <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">

                    <div class="px-8 py-6 border-b border-gray-100 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-900">{{ __('organization.courses_available') }}</h2>

                    </div>





                    <div class="p-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
                        @foreach ($courses as $course)
                            <x-compact-course-card
                                :course="$course"
                                :wishlist-key="'org-wishlist-' . $course['data']->id"
                            />
                        @endforeach
                    </div>



                </div>
            </section>
            <aside class="lg:col-span-4 space-y-4  ">
                <div class="  bg-white border border-gray-200 rounded-xl px-4 ">
                    <div class="  p-6 space-y-4  border-b border-gray-200">
                        <div class="text-sm font-bold text-base-content/70">{{ __('organization.info') }}</div>

                        <div class="space-y-3">
                            <div class="flex items-start justify-between gap-4">
                                <div class="text-xs font-extrabold uppercase tracking-wider text-base-content/40">
                                    {{ __('organization.founded') }}
                                </div>
                                <div class="font-semibold text-base-content">
                                    {{ $organization->founded ?? '-' }}
                                </div>
                            </div>

                            <div class="flex items-start justify-between gap-4">
                                <div class="text-xs font-extrabold uppercase tracking-wider text-base-content/40">
                                    {{ __('organization.category') }}
                                </div>
                                <div class="font-semibold text-base-content">
                                    {{ $organization->category ?? '-' }}
                                </div>
                            </div>

                            <div class="flex items-start justify-between gap-4">
                                <div class="text-xs font-extrabold uppercase tracking-wider text-base-content/40">
                                    {{ __('organization.position') }}
                                </div>
                                <div class="font-semibold text-base-content text-right">
                                    {{ $organization->position ?? '-' }}
                                </div>
                            </div>


                            <div class="flex items-start justify-between gap-4">
                                <div class="text-xs font-extrabold uppercase tracking-wider text-base-content/40">
                                    {{ __('organization.website') }}
                                </div>
                                <div class="font-semibold">
                                    @if ($organization->website)
                                        <a href="{{ $organization->website }}" target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-xs text-gray-600 transition-colors duration-300 group-hover:text-blue-600">
                                            {{ __('organization.clickToVisit') }}
                                        </a>
                                    @else
                                        <span class="text-base-content">-</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="  p-6 space-y-4    border-gray-200">
                        <div class="text-sm font-bold text-base-content/70">{{ __('organization.instructors') }}</div>

                        <div class="space-y-6">

                            @if (($instructors ?? collect())->count())
                                @foreach ($instructors as $instructor)
                                    <a href="{{ route('tutor.index', $instructor) }}"
                                        class="flex items-center gap-3 group">
                                        <img src="{{ $instructor->profile_image }}"
                                            alt="{{ $instructor->localized_name }}"
                                            class="w-10 h-10 rounded-lg object-cover" />
                                        <div class="min-w-0">
                                            <div class="font-semibold text-base-content truncate group-hover:text-primary transition-colors">
                                                {{ $instructor->localized_name }}</div>
                                            @if ($instructor->localized_specialization)
                                                <div class="text-xs text-base-content/60 truncate">
                                                    {{ $instructor->localized_specialization }}</div>
                                            @endif
                                        </div>
                                    </a>
                                @endforeach
                            @else
                                <div class="text-sm text-base-content/60">{{ __('base.noData') }}</div>
                            @endif


                        </div>
                    </div>
                </div>



            </aside>
        </div>






    </div>
</x-layouts.app>
