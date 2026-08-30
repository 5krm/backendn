@push('meta')
    <meta name="description" content="{{ __('seo.courses_description') }}">
    <meta property="og:title" content="{{ __('seo.courses_title') }} | {{ config('app.name') }}">
    <meta property="og:description" content="{{ __('seo.courses_description') }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta name="keywords" content="{{ config('app.name') }}, online courses, دورات, تعلم, NGO">
@endpush

<x-layouts.app>
    <div class="space-y-4 mb-6">
        @if ($promotionBanner)
            <x-promotions.display :banner="$promotionBanner" />
        @endif
    </div>

    {{-- Courses Section --}}
    <div class="space-y-6 mb-8" x-data="{ categoryOpen: false }">
        <div
            class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 sticky top-16 md:top-20 bg-base-100/80 backdrop-blur z-[1] p-6 rounded-box border border-base-200">
            <div class="space-y-1">
                <div class="flex items-end gap-4">
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold text-gray-900">
                            {{ __('course.available_courses') }}
                        </h2>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-auto flex flex-col md:flex-row items-stretch md:items-center gap-2 md:gap-3">

                {{-- Category Dropdown --}}
                <div class="dropdown dropdown-bottom md:dropdown-end z-20" x-data="{ categoryOpen: false }">
                    @php
                        $activeCategory = $categories->firstWhere('slug', request('category'));
                        $activeName = $activeCategory ? $activeCategory->localized_name : __('course.categories');
                        $searchPh = __('course.search_placeholder');
                    @endphp

                    <button @click="categoryOpen = !categoryOpen" @click.outside="categoryOpen = false" type="button"
                        :aria-expanded="categoryOpen" aria-controls="category-menu"
                        class="btn btn-outline w-full md:w-auto min-w-[9rem] px-5 py-2.5 items-center justify-between gap-3 bg-base-100 border-base-300 hover:border-primary hover:text-primary shadow-sm group">
                        <span class="flex items-center gap-2">
                            <span
                                class="{{ $activeCategory ? 'icon-[mdi--tag-outline]' : 'icon-[mdi--apps]' }} w-5 h-5 text-base-content/60 group-hover:text-primary"></span>
                            <span
                                class="font-semibold text-base-content/80 group-hover:text-primary leading-none">{{ $activeName }}</span>
                        </span>
                        <span
                            class="icon-[mdi--chevron-down] w-5 h-5 text-base-content/50 transition-transform duration-200"
                            :class="categoryOpen ? 'rotate-180' : ''"></span>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="categoryOpen" x-cloak id="category-menu"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="dropdown-content menu menu-md p-2 mt-2 w-full md:w-72 bg-base-100 rounded-2xl shadow-xl border border-base-200">
                        <ul class="max-h-64 overflow-y-auto">
                            <li>
                                <a href="{{ route('courses', array_filter(['search' => request('search'), 'sort' => request('sort'), 'view' => request('view'), 'free_only' => request('free_only')])) }}"
                                    class="flex items-center gap-3  py-2 px-3 {{ request('category') == null ? 'active text-primary' : '' }}">
                                    <span class="icon-[mdi--apps] w-5 h-5"></span>
                                    <x-t key="course.all_courses" default="All Courses" />
                                </a>
                            </li>
                            <li class="menu-title"><span><x-t key="course.categories" default="Categories" /></span>
                            </li>
                            @foreach ($categories as $category)
                                <li>
                                    <a href="{{ route('courses', array_merge(['category' => $category->slug], request()->only('search', 'sort', 'view', 'free_only'))) }}"
                                        class="flex items-center gap-3  py-2 px-3 {{ request('category') == $category->slug ? 'active text-primary' : '' }}">
                                        <span class="icon-[mdi--tag-outline] w-4 h-4"></span>
                                        {{ $category->localized_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                {{-- Search Form --}}
                <form method="GET" action="{{ route('courses') }}" class="w-full md:min-w-[320px] flex-1">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if (request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if (request('view'))
                        <input type="hidden" name="view" value="{{ request('view') }}">
                    @endif
                    @if (request('free_only'))
                        <input type="hidden" name="free_only" value="{{ request('free_only') }}">
                    @endif
                    <label class="input input-bordered  flex items-center gap-3 bg-base-100">
                        <span class="icon-[mdi--magnify] w-5 h-5 text-base-content/60"></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="grow"
                            placeholder="{{ $searchPh }}" />
                    </label>
                </form>

                {{-- Sort Dropdown --}}
                <div class="dropdown dropdown-bottom md:dropdown-end z-10" x-data="{ sortOpen: false }">
                    @php
                        $sortOptions = [
                            'newest' => __('course.sort.newest'),
                            'title' => __('course.sort.title'),
                            'students' => __('course.sort.students'),
                            'lessons' => __('course.sort.lessons'),
                            'free' => __('course.sort.free'),
                        ];
                        $currentSort = request('sort') ?? 'newest';
                        $currentSortLabel = $sortOptions[$currentSort] ?? __('course.sort.newest');
                    @endphp

                    <button @click="sortOpen = !sortOpen" @click.outside="sortOpen = false" type="button"
                        :aria-expanded="sortOpen" aria-controls="sort-menu"
                        class="btn btn-outline w-full md:w-auto min-w-[9rem] px-5 py-2.5 items-center justify-between gap-3 bg-base-100 border-base-300 hover:border-primary hover:text-primary shadow-sm group">
                        <span class="flex items-center gap-2">
                            <span class="icon-[mdi--sort] w-5 h-5 text-base-content/60 group-hover:text-primary"></span>
                            <span
                                class="font-semibold text-base-content/80 group-hover:text-primary leading-none">{{ $currentSortLabel }}</span>
                        </span>
                        <span
                            class="icon-[mdi--chevron-down] w-5 h-5 text-base-content/50 transition-transform duration-200"
                            :class="sortOpen ? 'rotate-180' : ''"></span>
                    </button>

                    {{-- Dropdown Menu --}}
                    <div x-show="sortOpen" x-cloak id="sort-menu" x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0"
                        x-transition:leave-end="opacity-0 translate-y-2"
                        class="dropdown-content menu menu-md p-2 mt-2 w-full md:w-56 bg-base-100 rounded-2xl shadow-xl border border-base-200">
                        <ul>
                            <li>
                                <a href="{{ route('courses', array_merge(['sort' => 'newest'], request()->only('category', 'search', 'view', 'free_only'))) }}"
                                    class="flex items-center gap-3 py-2 px-3 {{ $currentSort === 'newest' ? 'active text-primary' : '' }}">
                                    <span class="icon-[mdi--clock-outline] w-5 h-5"></span>
                                    <x-t key="course.sort.newest" default="Newest" />
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('courses', array_merge(['sort' => 'title'], request()->only('category', 'search', 'view', 'free_only'))) }}"
                                    class="flex items-center gap-3 py-2 px-3 {{ $currentSort === 'title' ? 'active text-primary' : '' }}">
                                    <span class="icon-[mdi--sort-alphabetical-ascending] w-5 h-5"></span>
                                    <x-t key="course.sort.title" default="Title" />
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('courses', array_merge(['sort' => 'lessons'], request()->only('category', 'search', 'view', 'free_only'))) }}"
                                    class="flex items-center gap-3 py-2 px-3 {{ $currentSort === 'lessons' ? 'active text-primary' : '' }}">
                                    <span class="icon-[mdi--book-open-page-variant-outline] w-5 h-5"></span>
                                    <x-t key="course.sort.lessons" default="Lessons" />
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('courses', array_merge(['sort' => 'students'], request()->only('category', 'search', 'view', 'free_only'))) }}"
                                    class="flex items-center gap-3 py-2 px-3 {{ $currentSort === 'students' ? 'active text-primary' : '' }}">
                                    <span class="icon-[mdi--accounts] w-5 h-5"></span>
                                    <x-t key="course.sort.students" default="Students" />
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('courses', array_merge(['sort' => 'free'], request()->only('category', 'search', 'view', 'free_only'))) }}"
                                    class="flex items-center gap-3 py-2 px-3 {{ $currentSort === 'free' ? 'active text-primary' : '' }}">
                                    <span class="icon-[mdi--currency-usd-off] w-5 h-5"></span>
                                    <x-t key="course.sort.free" default="Free First" />
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>

                <form method="GET" action="{{ route('courses') }}" class="flex items-center w-full md:w-auto">
                    @if (request('category'))
                        <input type="hidden" name="category" value="{{ request('category') }}">
                    @endif
                    @if (request('search'))
                        <input type="hidden" name="search" value="{{ request('search') }}">
                    @endif
                    @if (request('sort'))
                        <input type="hidden" name="sort" value="{{ request('sort') }}">
                    @endif
                    @if (request('view'))
                        <input type="hidden" name="view" value="{{ request('view') }}">
                    @endif
                    <input type="hidden" name="free_only" value="0">
                    <label
                        class="btn btn-outline w-full md:w-auto px-4 py-2.5 items-center justify-between gap-2 bg-base-100 border-base-300 hover:border-primary hover:text-primary cursor-pointer">
                        <span class="font-semibold text-sm">{{ __('course.free_only') }}</span>
                        <input type="checkbox" name="free_only" value="1" class="toggle toggle-sm"
                            {{ request('free_only') ? 'checked' : '' }} onchange="this.form.submit()" />
                    </label>
                </form>
            </div>
        </div>

        {{-- Active Filters Chips and Clear --}}
        @php $hasFilters = request('category') || request('search'); @endphp
        @if ($hasFilters)
            <div class="flex flex-wrap items-center gap-2">
                @if (request('category'))
                    <a href="{{ route('courses', array_filter(['search' => request('search'), 'sort' => request('sort')])) }}"
                        class="badge badge-outline badge-primary gap-2">
                        <span class="icon-[mdi--tag-outline] w-4 h-4"></span>
                        {{ $activeName }}
                        <span class="icon-[mdi--close] w-3 h-3"></span>
                    </a>
                @endif
                @if (request('search'))
                    <a href="{{ route('courses', array_filter(['category' => request('category'), 'sort' => request('sort')])) }}"
                        class="badge badge-outline gap-2">
                        <span class="icon-[mdi--magnify] w-4 h-4"></span>
                        “{{ request('search') }}”
                        <span class="icon-[mdi--close] w-3 h-3"></span>
                    </a>
                @endif
                <a href="{{ route('courses', array_filter(['sort' => request('sort'), 'view' => request('view'), 'free_only' => request('free_only')])) }}"
                    class="btn btn-ghost btn-sm">{{ __('course.clear_filters', ['default' => 'Clear filters']) }}</a>
            </div>
        @endif
    </div>

    @if (count($courses) === 0)
        <div class="hero bg-base-100 rounded-box border border-base-200 md:h-[500px]">
            <div class="hero-content text-center">
                <div class="max-w-md">
                    <div class="mb-4">
                        <span class="icon-[mdi--emoticon-sad-outline] w-10 h-10 text-base-content/40"></span>
                    </div>
                    <h3 class="text-2xl font-bold"><x-t key="course.no_results_title" default="No courses found" />
                    </h3>
                    <p class="py-2 text-base-content/60"><x-t key="course.no_results_subtitle"
                            default="Try adjusting your filters or search" /></p>
                    <a href="{{ route('courses') }}" class="btn btn-primary"><x-t key="course.clear_filters"
                            default="Clear filters" /></a>
                </div>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8">
            @foreach ($courses as $course)
                <livewire:course-preview-card :$course :key="$course['data']->id" :is-wishlisted="$course['in_wishlist']" mode="grid" />
            @endforeach
        </div>
    @endif

    <div class="mt-8">
        {{ method_exists($courses, 'links') ? $courses->withQueryString()->links() : '' }}
    </div>

</x-layouts.app>
