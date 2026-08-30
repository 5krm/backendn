@php
    $courseData = $course['data'];
    $metaTitle = $courseData->title;
    $metaDesc = Str::limit(strip_tags($courseData->description ?? ''), 160);
    $metaImage = $courseData->cover_url ?: $course['file'] ?? asset('assets/images/default-course.png');
    $metaUrl = route('courses.details', $courseData->slug);
    $preview = $course['preview_lesson'];
    $hasPreview = $preview ? $preview->video || $preview->getVideoId() : false;
@endphp

<x-layouts.main :title="$metaTitle" :metaDescription="$metaDesc" :ogImage="$metaImage" :canonical="$metaUrl" ogType="article">
    @push('meta')
        <meta name="keywords"
            content="{{ $courseData->category?->localized_name }}, {{ config('app.name') }}, online course, تعلم, دورة">
        @if ($courseData->lang)
            <meta property="og:locale" content="{{ $courseData->lang == 'ar' ? 'ar_AR' : 'en_US' }}">
        @endif
    @endpush

    <x-navbar :langRedirect="route('courses')"></x-navbar>

    {{-- Hero Section - Enterprise Level --}}
    <div class="bg-secondary ">
        <div class="container mx-auto px-4 lg:px-8 py-8 lg:py-12">
            {{-- Breadcrumb --}}
            @if ($course['data']->category)
                <nav class="flex items-center gap-2 text-sm text-white/80 mb-6">
                    <a href="{{ route('courses') }}"
                        class="hover:text-white transition-colors">{{ trans_choice('course.courses', 5) }}</a>
                    @if (App::isLocale('en'))
                        <span class="icon-[mdi--chevron-right] w-4 h-4"></span>
                    @else
                        <span class="icon-[mdi--chevron-left] w-4 h-4"></span>
                    @endif
                    <span class="text-white">{{ $course['data']->category->localized_name }}</span>
                </nav>
            @endif

            <div class="grid lg:grid-cols-3 gap-8 items-start ">
                {{-- Left: Course Info --}}
                <div class="lg:col-span-2 text-white">
                    {{-- Category Badge --}}
                    @if ($course['data']->category)
                        <div
                            class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary rounded-full text-sm font-semibold mb-4">
                            {{ $course['data']->category->localized_name }}
                        </div>
                    @endif

                    <h1 class="text-3xl lg:text-5xl font-bold mb-4 leading-tight">
                        {{ $course['data']->title }}
                    </h1>

                    <div class="text-lg lg:text-xl text-white/90 mb-6 pb-5 lg:leading-relaxed leading-relaxed">
                        {!! $course['data']->description !!}
                    </div>



                    {{-- Course Meta Info --}}
                    <div class="flex flex-wrap items-center gap-6 mb-6 pb-6 border-b border-white/20 mt-3">
                        @if (isset($course['data']->students_count) && $course['data']->students_count > 0)
                            <div class="flex items-center gap-2">
                                <span class="icon-[mdi--account-group] w-5 h-5"></span>

                                <span class="text-white/80 text-sm">
                                    {{ number_format($course['data']->students_count) }}
                                    {{ __('course.enrolled_students') }}</span>
                            </div>
                        @endif

                        @if (isset($course['data']->lessons_count))
                            <div class="flex items-center gap-2">
                                <span class="icon-[mdi--book-open-page-variant] w-5 h-5"></span>
                                <span
                                    class="text-white/80 text-sm ">{{ trans_choice('lessons.lessons', $course['data']->lessons_count) }}</span>
                            </div>
                        @endif

                        <div class="flex items-center gap-2">
                            <span class="icon-[mdi--translate] w-5 h-5"></span>
                            <span class="">{{ $course['data']->lang == 'ar' ? 'العربية' : 'English' }}</span>
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="icon-[mdi--certificate-outline] w-5 h-5"></span>
                            <span class="text-white/80 text-sm">{{ __('course.certificate_of_completion') }}</span>
                        </div>
                    </div>

                    {{-- Instructor Info --}}
                    @if ($course['data']->tutor?->tutorProfile)
                        <div class="flex items-center gap-3">
                            <img class="w-12 h-12 rounded-full object-cover ring-2 ring-white/30"
                                src="{{ $course['data']->tutor->tutorProfile->profile_image }}"
                                alt="{{ $course['data']->tutor->tutorProfile->localized_name }}" />
                            <div>
                                <p class="text-sm text-white/70">{{ __('course.course_by') }}</p>
                                <p class="font-semibold">{{ $course['data']->tutor->tutorProfile->localized_name }}</p>
                            </div>

                            @if (isset($course['organization']))
                                <div class="relative  max-w-72 overflow-hidden rounded-lg   p-3">
                                    <a href="{{ route('organization.index', $course['organization']['slug']) }}">
                                        <img src="{{ $course['organization']['logo'] }}"
                                            alt="{{ $course['organization']['name'] }}"
                                            class="h-full w-full object-contain">

                                    </a>
                                </div>
                            @endif

                        </div>
                    @endif
                </div>

                {{-- Right: Enrollment Card --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
                        {{-- Course Image --}}
                        <div class="relative">
                            <img class="w-full aspect-video object-cover" src="{{ $course['file'] }}"
                                alt="{{ $course['data']->title }}" />
                            @if ($course['data']->video_url)
                                <div class="absolute inset-0 flex items-center justify-center bg-black/20">
                                    <div
                                        class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg hover:scale-110 transition-transform cursor-pointer">
                                        <span class="icon-[mdi--play] w-8 h-8 text-primary"></span>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <div class="p-6 gap-4">
                            {{-- Pricing --}}
                            @if (!$course['enrolled'])
                                <div class="mb-6">
                                    @if ($course['data']->is_free)
                                        <div class="flex items-baseline gap-3 mb-2">
                                            <span
                                                class="text-4xl font-bold text-primary">{{ __('course.free') }}</span>
                                        </div>
                                    @else
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <span class="text-5xl font-bold text-primary">
                                                    <x-price :price="$course['data']->price" :promo="$course['promo']" />
                                                </span>
                                                @if ($course['promo'])
                                                    <span class="text-gray-500 text-lg line-through">
                                                        {{ Number::currency($course['promo']['old_price']) }}
                                                    </span>
                                                @endif
                                            </div>
                                            @if ($course['promo'])
                                                <div
                                                    class=" rounded-2xl bg-secondary/95 px-4 py-3 text-white shadow-lg backdrop-blur-sm">
                                                    <div class="flex items-baseline gap-1 leading-none">
                                                        <span
                                                            class="text-3xl font-black">{{ $course['promo']['discount'] }}%</span>
                                                        <span
                                                            class="text-xs font-extrabold uppercase tracking-wide">{{ __('course.off') }}</span>
                                                    </div>

                                                    <div
                                                        class="mt-2 flex items-center gap-1.5 rounded-lg bg-white/15 px-2 py-1 text-[11px] font-bold">
                                                        <i class="icon-[mdi--clock-outline] text-sm"></i>
                                                        <span>{{ trans_choice('course.sale_ends_in', max(1, (int) $course['promo']['days'])) }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endif
                            @if (session('success'))
                                <div class="alert alert-success mb-4">
                                    {{ session('success') }}
                                </div>
                            @endif

                            @if ($course['is_preview'])
                                <div class="my-3">
                                    <livewire:notify-me-button :course="$course" :key="'notify-' . $course['data']->id" />
                                </div>
                            @elseif($course['enrolled'])
                                <a href="{{ route('app.lessons.by-course', ['course' => $course['data']->slug]) }}"
                                    class="btn btn-primary w-full mb-4">
                                    <span class="icon-[mdi--play-circle] w-5 h-5"></span>
                                    {{ __('course.continue_learning') }}
                                </a>
                                @if (isset($course['enrollment']) && $course['enrollment'])
                                    <div class="bg-primary/5 rounded-lg p-4 mb-3">
                                        <div class="flex items-center justify-between mb-2">
                                            <span
                                                class="text-sm font-medium text-gray-700">{{ __('course.your_progress') }}</span>
                                            <span
                                                class="text-sm font-bold text-primary">{{ round($course['enrollment']->progress ?? 0) }}%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-2 overflow-hidden">
                                            <div class="bg-primary h-2 rounded-full transition-all"
                                                style="width:  {{ $course['enrollment']->progress ?? 0 }}%"></div>
                                        </div>
                                    </div>
                                @endif
                            @else
                                <a href="{{ route('app.courses.enroll', $course['data']->slug) }}"
                                    class="btn btn-primary w-full btn-lg mb-3">
                                    {{ $course['data']->is_free ? __('course.enrollment.for_free') : __('course.enrollment.enrollNow') }}
                                </a>

                            @endif


                            {{-- Course Includes --}}

                            <div class="pt-6 border-t border-gray-200">
                                <p class="font-bold text-secondary mb-4 text-lg">
                                    {{ __('course.this_course_includes') }}</p>
                                <div class="space-y-3">
                                    @if (isset($course['data']->lessons_count))
                                        <div class="flex items-start gap-3">
                                            <span
                                                class="icon-[mdi--play-circle-outline] w-5 h-5 text-gray-600 mt-0.5"></span>
                                            <span
                                                class="text-sm text-gray-700">{{ trans_choice('lessons.lessons', $course['data']->lessons_count) }}</span>
                                        </div>
                                    @endif

                                    {{-- <div class="flex items-start gap-3">
                                        <span class="icon-[mdi--clock-outline] w-5 h-5 text-gray-600 mt-0.5"></span>
                                        <span class="text-sm text-gray-700">{{ $course['data']->textDuration }}
                                            {{ __('course.on_demand_video') }}</span>
                                    </div> --}}

                                    @if (isset($course['resources_count']) && $course['resources_count'] > 0)
                                        <div class="flex items-start gap-3">
                                            <span
                                                class="icon-[mdi--file-document-outline] w-5 h-5 text-gray-600 mt-0.5"></span>
                                            <span class="text-sm text-gray-700">{{ $course['resources_count'] }}
                                                {{ __('course.downloadable_resources') }}</span>
                                        </div>
                                    @endif

                                    <div class="flex items-start gap-3">
                                        <span class="icon-[mdi--infinity] w-5 h-5 text-gray-600 mt-0.5"></span>
                                        <span
                                            class="text-sm text-gray-700">{{ __('course.full_lifetime_access') }}</span>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <span class="icon-[mdi--cellphone] w-5 h-5 text-gray-600 mt-0.5"></span>
                                        <span
                                            class="text-sm text-gray-700">{{ __('course.access_mobile_desktop') }}</span>
                                    </div>

                                    <div class="flex items-start gap-3">
                                        <span
                                            class="icon-[mdi--certificate-outline] w-5 h-5 text-gray-600 mt-0.5"></span>
                                        <span
                                            class="text-sm text-gray-700">{{ __('course.certificate_of_completion') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Tab Navigation --}}
    <div class="bg-white border-b border-gray-200 sticky top-16 z-40 shadow-sm -mt-px">
        <div class="container">
            <nav class="flex gap-6 lg:gap-8 overflow-x-auto scrollbar-hide">
                <a href="#overview"
                    class="tab-link py-4 px-2 border-b-2 border-primary text-primary font-semibold whitespace-nowrap transition-colors">
                    {{ __('course.overview') }}
                </a>
                <a href="#curriculum"
                    class="tab-link py-4 px-2 border-b-2 border-transparent text-gray-600 hover:text-secondary hover:border-gray-300 font-semibold whitespace-nowrap transition-colors">
                    {{ __('course.curriculum') }}
                </a>
                <a href="#instructor"
                    class="tab-link py-4 px-2 border-b-2 border-transparent text-gray-600 hover:text-secondary hover:border-gray-300 font-semibold whitespace-nowrap transition-colors">
                    {{ __('course.instructor') }}
                </a>
                @if (count($course['data']->testimonials) > 0)
                    <a href="#reviews"
                        class="tab-link py-4 px-2 border-b-2 border-transparent text-gray-600 hover:text-secondary hover:border-gray-300 font-semibold whitespace-nowrap transition-colors">
                        {{ __('course.reviews') }}
                    </a>
                @endif
            </nav>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="container py-12">
        <div class="grid lg:grid-cols-3 gap-12">
            {{-- Left Column: Main Content --}}
            <div class="lg:col-span-2 space-y-12" id="overview">
                @if ($hasPreview)
                    @if (!empty($preview->video))
                        {!! $preview->video !!}
                    @elseif (!empty($preview->getVideoId()))
                        <iframe class="w-full my-1 rounded-lg"
                            src="https://www.youtube.com/embed/{{ $preview->getVideoId() }}"
                            title="YouTube video player" frameborder="0"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                            allowfullscreen></iframe>
                    @endif
                @endif
                {{-- What You'll Learn Section --}}
                <section class="scroll-mt-32">
                    <h2 class="text-3xl font-bold text-secondary mb-6">
                        {{ __('course.what_you_will_learn') }}
                    </h2>
                    <div class=" bg-white border border-gray-200 rounded-xl p-8">
                        <div class="prose max-w-none course-objectives">
                            {!! $course['data']->objectives !!}
                        </div>
                    </div>

                </section>

                {{-- Course Curriculum Section --}}
                <section id="curriculum" class="scroll-mt-32">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-3xl font-bold text-secondary">
                            {{ __('course.course_content') }}
                        </h2>
                        <button class="text-primary font-semibold text-sm hover:underline" onclick="expandAll()">
                            {{ __('course.expand_all') }}
                        </button>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-4 mb-6">
                        <div class="flex flex-wrap items-center gap-4 text-sm text-gray-700">
                            <span>
                                <strong>{{ count($sections) }}</strong>
                                {{ trans_choice('course.chapters', count($sections)) }}
                            </span>
                            <span>•</span>
                            <span> {{ trans_choice('lessons.lessons', $course['data']->lessons_count) }}</span>
                        </div>
                    </div>

                    <div class="space-y-2">
                        @foreach ($sections as $section)
                            <div class="border border-gray-200 rounded-lg overflow-hidden bg-white">
                                <details class="group curriculum-section">
                                    <summary
                                        class="flex items-center justify-between p-5 cursor-pointer list-none hover:bg-gray-50 transition-colors">
                                        <div class="flex-1 pe-4">
                                            <div class="flex items-center gap-3 mb-1">
                                                <h3 class="font-bold text-secondary">
                                                    {{ $section->title }}
                                                </h3>
                                                <span
                                                    class="icon-[mdi--chevron-left] w-5 h-5 text-gray-400 group-open:rotate-90 transition-transform"></span>
                                            </div>
                                            @if ($section->description)
                                                <p class="text-sm text-gray-600 ml-8">{{ $section->description }}</p>
                                            @endif
                                        </div>
                                        <div class="flex items-center gap-4 text-sm text-gray-600">
                                            <span>
                                                {{ trans_choice('lessons.lessons', $section->publishedLessons->count()) }}</span>
                                        </div>
                                    </summary>

                                    <div class="border-t border-gray-200 bg-gray-50">
                                        @foreach ($section->publishedLessons as $lesson)
                                            <div
                                                class="flex items-center justify-between px-5 py-4 border-b border-gray-200 last:border-b-0 hover:bg-white transition-colors">
                                                <div class="flex items-center gap-3 flex-1">
                                                    @if ($lesson->video_url)
                                                        <span
                                                            class="icon-[mdi--play-circle-outline] w-5 h-5 text-gray-400"></span>
                                                    @else
                                                        <span
                                                            class="icon-[mdi--file-document-outline] w-5 h-5 text-gray-400"></span>
                                                    @endif
                                                    <span
                                                        class="text-sm font-medium text-secondary">{{ $lesson->title }}</span>
                                                </div>
                                                <div class="flex items-center gap-3">
                                                    <span
                                                        class="text-sm text-gray-600">{{ $lesson->textDuration }}</span>
                                                    @if (!$course['enrolled'])
                                                        <span
                                                            class="icon-[mdi--lock-outline] w-4 h-4 text-gray-400"></span>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </details>
                            </div>
                        @endforeach
                    </div>
                </section>

                {{-- Requirements Section --}}
                @if ($course['data']->requirements)
                    <section class="scroll-mt-32">
                        <h2 class="text-3xl font-bold text-secondary mb-6">
                            {{ __('course.requirements') }}
                        </h2>
                        <div class="prose prose-lg max-w-none">
                            {!! $course['data']->requirements !!}
                        </div>
                    </section>
                @endif

                {{-- Description Section --}}
                @if ($course['data']->long_description)
                    <section class="scroll-mt-32">
                        <h2 class="text-3xl font-bold text-secondary mb-6">
                            {{ __('course.description') }}
                        </h2>
                        <div class="prose prose-lg max-w-none">
                            {!! $course['data']->long_description !!}
                        </div>
                    </section>
                @endif
                {{-- Instructor Section --}}
                @if ($course['data']->tutor)
                    @php
                        $id = $course['data']->tutor->tutorProfile->id;
                        $instructor = $course['data']->tutor->tutorProfile;
                        $instructorUser = $course['data']->tutor;
                        $name = $instructor->localized_name;
                        $image = $instructor->profile_image;
                        $specialization = $instructor->localized_specialization;
                        $bio = $instructorUser->localized_bio;
                        $socialLinks = $instructorUser->relationLoaded('socialLinks')
                            ? $instructorUser->socialLinks
                            : $instructorUser->socialLinks()->get();
                    @endphp
                    <section id="instructor" class="scroll-mt-32 flex flex-col gap-3">
                        <div>
                            <h2 class="text-3xl font-bold text-secondary mb-6">
                                {{ __('course.about_instructor') }}
                            </h2>

                        </div>

                        <div class="bg-white border border-gray-200 rounded-xl p-8">
                            <div class="flex flex-col md:flex-row gap-8 items-start">
                                {{-- Instructor Avatar --}}
                                <div class="flex-shrink-0">
                                    <img class="w-32 h-32 rounded-full object-cover border-4 border-gray-100"
                                        src="{{ $image }}" alt="{{ $name }}" />
                                </div>
                                {{-- Instructor Info --}}
                                <div class="flex-1">

                                    <a href="{{ route('tutor.index', $id) }}">
                                        <h3 class="text-2xl font-bold text-secondary mb-2">{{ $name }}</h3>
                                    </a>
                                    <p class="text-lg text-gray-600 mb-4">{{ $specialization }}</p>
                                    <div class="flex flex-wrap gap-4 mb-6">
                                        @if (isset($course['data']->tutor->tutor_courses_count))
                                            <div
                                                class="inline-flex items-center gap-2 px-3 py-1.5 bg-primary/10 text-primary rounded-full text-sm font-medium">
                                                <span class="icon-[mdi--book-open-variant] w-4 h-4"></span>
                                                <span>{{ $course['data']->tutor->tutor_courses_count }}
                                                    {{ trans_choice('course.courses', $course['data']->tutor->tutor_courses_count) }}</span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Bio --}}
                                    @if ($bio)
                                        <p class="text-gray-700 leading-relaxed mb-4">{{ $bio }}</p>
                                    @endif

                                    {{-- Social Links --}}
                                    @if ($socialLinks->isNotEmpty())
                                        <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
                                            @foreach ($socialLinks as $link)
                                                <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                                                    title="{{ $link->platformLabel() }}"
                                                    class="w-10 h-10 flex items-center justify-center rounded-full bg-gray-100 hover:bg-primary hover:text-white transition-colors">
                                                    <x-social-platform-icon :platform="$link->platform ?? ($link->getAttributes()['platform'] ?? null)" class="w-5 h-5" />
                                                </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>
                @endif


                {{-- Organization Section --}}
                @if (isset($course['organization']))
                    <section id="instructor" class="scroll-mt-32">
                        <h2 class="text-3xl font-bold text-secondary mb-6">
                            {{ __('organization.info') }}
                        </h2>

                        <div class="bg-white border border-gray-200 rounded-xl p-8">
                            <div class="flex flex-col md:flex-row gap-8 items-start">
                                <div
                                    class="relative  h-32 w-60 overflow-hidden rounded-lg bg-slate-100  pt-2 px-3 object-contain">
                                    <img class="w-full h-full object-contain"
                                        src="{{ $course['organization']['logo'] }}"
                                        alt="{{ $course['organization']['name'] }}" />
                                </div>

                                {{-- Instructor Info --}}
                                <div class="flex-1">
                                    <h3 class="text-2xl font-bold text-secondary mb-2">
                                        {{ $course['organization']['name'] }}</h3>

                                    {{-- description --}}
                                    <p class="text-gray-700 leading-relaxed mb-4">
                                        {{ $course['organization']['description'] }}</p>

                                    <div class="flex gap-3 mt-6 pt-6 border-t border-gray-200">
                                        <a href="{{ route('organization.index', $course['organization']['slug']) }}"
                                            class="btn btn-primary btn-outline">
                                            {{ __('organization.view_organization') }}
                                            <span
                                                class="{{ $direction == 'rtl' ? 'icon-[mdi--arrow-left]' : 'icon-[mdi--arrow-right]' }}"></span>
                                        </a>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                @endif

                {{-- Student Reviews Section --}}
                @if (count($course['data']->testimonials) > 0)
                    <section id="reviews" class="scroll-mt-32">
                        <h2 class="text-3xl font-bold text-secondary mb-6">
                            {{ __('course.student_reviews') }}
                        </h2>

                        <div class="grid md:grid-cols-2 gap-6">
                            @foreach ($course['data']->testimonials as $testimonial)
                                <x-courses.testimonial :$testimonial :$loop />
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>

            {{-- Right Column: Sticky Sidebar (Desktop Only) --}}
            <div class="hidden lg:block lg:col-span-1">
                <div class="sidebar-sticky top-28">
                    {{-- Quick Info Card --}}
                    <div class="bg-white border border-gray-200 rounded-xl p-6 mb-6">
                        <h3 class="font-bold text-secondary mb-4">{{ __('course.quick_info') }}</h3>
                        <div class="divide-y">
                            <div class="flex items-center justify-between text-sm py-2">
                                <span class="text-gray-600">{{ __('course.lessons') }}</span>
                                <span
                                    class="font-semibold text-secondary">{{ $course['data']->lessons_count ?? 0 }}</span>
                            </div>
                            @if (isset($course['data']->students_count) && $course['data']->students_count > 0)
                                <div class="flex items-center justify-between text-sm py-2">
                                    <span class="text-gray-600">{{ __('course.enrolled') }}</span>
                                    <span
                                        class="font-semibold text-secondary">{{ number_format($course['data']->students_count) }}</span>
                                </div>
                            @endif
                            <div class="flex items-center justify-between text-sm py-2">
                                <span class="text-gray-600">{{ __('course.language') }}</span>
                                <span
                                    class="font-semibold text-secondary">{{ $course['data']->lang == 'ar' ? 'العربية' : 'English' }}</span>
                            </div>
                            <div class="flex items-center justify-between text-sm py-2">
                                <span class="text-gray-600">{{ __('course.level') }}</span>
                                <span
                                    class="font-semibold text-secondary">{{ $course['data']->level->getLabel() }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Share Card --}}
                    <div class="bg-white border border-gray-200 rounded-xl p-6">
                        <h3 class="font-bold text-secondary mb-4">{{ __('course.share_course') }}</h3>
                        <div class="flex gap-3">
                            <button type="button" onclick="shareOnFacebook()"
                                class="flex-1 btn btn-sm btn-outline hover:bg-blue-50 hover:border-blue-500 transition-colors"
                                title="Share on Facebook">
                                <span class="icon-[mdi--facebook] w-5 h-5 text-blue-600"></span>
                            </button>
                            <button type="button" onclick="shareOnTwitter()"
                                class="flex-1 btn btn-sm btn-outline hover:bg-sky-50 hover:border-sky-500 transition-colors"
                                title="Share on Twitter">
                                <span class="icon-[mdi--twitter] w-5 h-5 text-sky-500"></span>
                            </button>
                            <button type="button" onclick="shareOnLinkedIn()"
                                class="flex-1 btn btn-sm btn-outline hover:bg-blue-50 hover:border-blue-700 transition-colors"
                                title="Share on LinkedIn">
                                <span class="icon-[mdi--linkedin] w-5 h-5 text-blue-700"></span>
                            </button>
                            <button type="button" onclick="copyLink()"
                                class="flex-1 btn btn-sm btn-outline hover:bg-gray-50 hover:border-gray-500 transition-colors"
                                title="Copy link">
                                <span class="icon-[mdi--link-variant] w-5 h-5 text-gray-600"></span>
                            </button>
                        </div>
                        <div id="copy-notification" class="hidden mt-3 text-sm text-green-600 text-center">
                            {{ __('course.link_copied') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Final CTA Section --}}
    @if (!$course['enrolled'] && !$course['is_preview'])
        <div class="bg-secondary py-16">
            <div class="max-w-4xl mx-auto px-4 lg:px-8 text-center">
                <h2 class="text-3xl lg:text-4xl font-bold text-white mb-4">
                    {{ __('course.start_learning_today') }}
                </h2>
                <p class="text-xl text-white/90 mb-8">
                    @if (isset($course['data']->students_count) && $course['data']->students_count > 0)
                        {{ __('course.join_students', ['count' => number_format($course['data']->students_count)]) }}
                    @else
                        {{ __('course.enroll_now_message') }}
                    @endif
                </p>

                <a href="{{ route('app.courses.enroll', $course['data']->slug) }}" class="btn btn-primary">
                    {{ $course['data']->is_free ? __('course.enrollment.for_free') : __('course.enrollment.enrollNow') }}
                </a>

            </div>
        </div>
    @endif

    <x-footer></x-footer>

    {{-- Survey Complete Toast --}}
    @if (session('survey_complete'))
        <div id="showMe" class="fixed bottom-6 right-6 z-50 max-w-md">
            <div role="alert" class="alert bg-white border border-primary shadow-xl rounded-lg">
                <span class="icon-[mdi--checkbox-marked-circle-outline] text-primary w-6 h-6"></span>
                <span class="text-secondary font-medium">{{ session('survey_complete') }}</span>
            </div>
        </div>
    @endif

    <style>
        /* Survey toast animation */
        #showMe {
            animation: slideIn 0.3s ease-out, fadeOut 0.3s ease-in 3s forwards;
        }

        @keyframes slideIn {
            from {
                transform: translateX(100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateY(-10px);
            }
        }

        /* Details/Summary styling */
        details summary::-webkit-details-marker {
            display: none;
        }

        /* Tab navigation active state */
        .tab-link {
            transition: all 0.2s ease;
        }

        /* Smooth scroll */
        html {
            scroll-behavior: smooth;
        }

        .scroll-mt-32 {
            scroll-margin-top: 10rem;
        }
    </style>

    <script>
        // Tab navigation
        document.addEventListener('DOMContentLoaded', function() {
            const tabLinks = document.querySelectorAll('.tab-link');

            // Update active tab on scroll
            const sections = document.querySelectorAll('section[id]');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        tabLinks.forEach(link => {
                            link.classList.remove('border-primary', 'text-primary');
                            link.classList.add('border-transparent', 'text-gray-600');

                            if (link.getAttribute('href') === `#${entry.target.id}`) {
                                link.classList.add('border-primary', 'text-primary');
                                link.classList.remove('border-transparent',
                                    'text-gray-600');
                            }
                        });
                    }
                });
            }, {
                threshold: 0.3,
                rootMargin: '-100px 0px -50% 0px'
            });

            sections.forEach(section => observer.observe(section));

            // Smooth scroll on click
            tabLinks.forEach(link => {
                link.addEventListener('click', (e) => {
                    e.preventDefault();
                    const targetId = link.getAttribute('href').substring(1);
                    const targetSection = document.getElementById(targetId);
                    if (targetSection) {
                        const offset = 140;
                        const elementPosition = targetSection.getBoundingClientRect().top;
                        const offsetPosition = elementPosition + window.pageYOffset - offset;

                        window.scrollTo({
                            top: offsetPosition,
                            behavior: 'smooth'
                        });
                    }
                });
            });
        });

        // Expand all sections
        function expandAll() {
            const details = document.querySelectorAll('.curriculum-section');
            const allOpen = Array.from(details).every(d => d.open);

            details.forEach(detail => {
                detail.open = !allOpen;
            });

            event.target.textContent = allOpen ? '{{ __('course.expand_all') }}' : '{{ __('course.collapse_all') }}';
        }

        // Share functions
        window.courseUrl = {!! json_encode(route('app.courses.details', ['course' => $course['data']->slug])) !!};
        window.shareTitle = {!! json_encode($course['data']->title) !!};
        window.shareDescription = {!! json_encode(Str::limit($course['data']->description, 100)) !!};

        window.shareOnFacebook = function() {
            console.log('Facebook share clicked');
            const url = 'https://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(window.courseUrl);
            console.log('Opening URL:', url);
            window.open(url, '_blank');
        }

        window.shareOnTwitter = function() {
            console.log('Twitter share clicked');
            const text = window.shareTitle + ' - ' + window.shareDescription;
            const url = 'https://twitter.com/intent/tweet?url=' + encodeURIComponent(window.courseUrl) + '&text=' +
                encodeURIComponent(text);
            console.log('Opening URL:', url);
            window.open(url, '_blank');
        }

        window.shareOnLinkedIn = function() {
            console.log('LinkedIn share clicked');
            const url = 'https://www.linkedin.com/sharing/share-offsite/?url=' + encodeURIComponent(window.courseUrl);
            console.log('Opening URL:', url);
            window.open(url, '_blank');
        }

        window.copyLink = function() {
            console.log('Copy link clicked');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(window.courseUrl).then(() => {
                    console.log('Link copied successfully');
                    const notification = document.getElementById('copy-notification');
                    if (notification) {
                        notification.classList.remove('hidden');
                        setTimeout(() => {
                            notification.classList.add('hidden');
                        }, 3000);
                    }
                }).catch(err => {
                    console.error('Failed to copy:', err);
                    alert({!! json_encode(__('course.copy_failed')) !!});
                });
            } else {
                // Fallback for older browsers
                console.log('Using fallback copy method');
                const textArea = document.createElement('textarea');
                textArea.value = window.courseUrl;
                textArea.style.position = 'fixed';
                textArea.style.left = '-999999px';
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    console.log('Link copied successfully (fallback)');
                    const notification = document.getElementById('copy-notification');
                    if (notification) {
                        notification.classList.remove('hidden');
                        setTimeout(() => {
                            notification.classList.add('hidden');
                        }, 3000);
                    }
                } catch (err) {
                    console.error('Failed to copy:', err);
                    alert({!! json_encode(__('course.copy_failed')) !!});
                }
                document.body.removeChild(textArea);
            }
        }

        console.log('Share functions initialized', {
            courseUrl: window.courseUrl,
            shareTitle: window.shareTitle,
            shareDescription: window.shareDescription
        });
    </script>
</x-layouts.main>
