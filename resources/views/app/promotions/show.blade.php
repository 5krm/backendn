@push('meta')
    <meta name="description" content="{{ $promotion->description ?: __('promotions.default_subheadline', ['percent' => $promotion->discount_percent]) }}">
    <meta property="og:title" content="{{ $promotion->title }} | {{ config('app.name') }}">
    <meta property="og:description" content="{{ $promotion->description ?: __('promotions.default_subheadline', ['percent' => $promotion->discount_percent]) }}">
    <meta property="og:url" content="{{ url()->current() }}">
@endpush

<x-layouts.app>
    <div class="mb-8 space-y-6">
        <div class="rounded-2xl border border-base-200 bg-base-100 p-6 md:p-8">
            <div class="flex flex-col gap-4 md:flex-row md:items-start md:justify-between">
                <div class="min-w-0 space-y-3">
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 justify-center rounded-full text-white  bg-primary font-semibold">{{ __('promotions.limited_offer') }}</span>
                        <span class="  inline-flex items-center px-2.5 py-0.5 justify-center text-primary font-semibold">
                            {{ $promotion->discount_percent }}% {{ __('course.off') }}
                        </span>
                        <span class="text-sm text-base-content/60">
                            {{ trans_choice('course.sale_ends_in', $banner->daysRemaining()) }}
                        </span>
                    </div>

                    <h1 class="text-3xl font-bold text-gray-900 md:text-4xl">
                        {{ __('promotions.promotion_courses', ['title' => $promotion->title]) }}
                    </h1>

                    @if ($promotion->description)
                        <p class="max-w-3xl text-base text-base-content/70">
                            {{ $promotion->description }}
                        </p>
                    @endif

                    <p class="text-sm text-base-content/60">
                        {{ trans_choice('promotions.courses_included', $courses->total()) }}
                        · {{ __('promotions.ends_on', ['date' => $banner->endsAtLabel()]) }}
                    </p>
                </div>

                <a href="{{ route(auth()->check() ? 'app.courses' : 'courses') }}"
                    class="inline-flex items-center justify-center font-medium gap-2 rounded-lg transition px-4 py-3 text-sm bg-white text-gray-700 ring-1 ring-inset ring-gray-300 hover:bg-gray-50 ">
                    {{ __('promotions.back_to_all_courses') }}
                </a>
            </div>
        </div>

        @if ($courses->isEmpty())
            <div class="hero rounded-box border border-base-200 bg-base-100 md:h-[420px]">
                <div class="hero-content text-center">
                    <div class="max-w-md">
                        <div class="mb-4">
                            <span class="icon-[mdi--emoticon-sad-outline] h-10 w-10 text-base-content/40"></span>
                        </div>
                        <h3 class="text-2xl font-bold">
                            <x-t key="course.no_results_title" default="No courses found" />
                        </h3>
                        <p class="py-2 text-base-content/60">
                            {{ __('promotions.no_promotion_courses') }}
                        </p>
                        <a href="{{ route(auth()->check() ? 'app.courses' : 'courses') }}" class="btn btn-primary">
                            {{ __('promotions.back_to_all_courses') }}
                        </a>
                    </div>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 lg:gap-8">
                @foreach ($courses as $course)
                    <livewire:course-preview-card :$course :key="$course['data']->id"
                        :is-wishlisted="$course['in_wishlist']" mode="grid" />
                @endforeach
            </div>

            <div class="mt-8">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</x-layouts.app>
