<x-layouts.profile>
    <h4 class="font-bold text-lg">{{ __('billing.title') }}</h4>

    <div class="mt-4 space-y-8">
        @if ($courses->isEmpty())
            <div class="border border-gray-400 border-dashed text-center mt-4 p-8 rounded-xl">
                <p class="mb-0 text-gray-500">{{ __('course.no_courses') }}</p>
                <a class="btn btn-primary btn-outline btn-sm w-40 mx-auto mt-5 flex items-center"
                    href="{{ route('courses') }}">
                    {{ __('home.explore_courses') }} <span
                        class="{{ $direction == 'rtl' ? 'icon-[mdi--arrow-left]' : 'icon-[mdi--arrow-right]' }}"></span></a>
            </div>
        @endif
        @foreach ($courses as $course)
            <div class="card md:card-side bg-base-100 shadow-xl">
                <div class="grid  grid-cols-1 md:grid-cols-3 gap-2">
                    <div>
                        <figure
                            class="relative  h-full transition-all duration-300 cursor-pointer filter  hover:grayscale">
                            <a href="{{ route('app.lessons.by-course', $course) }}">
                                <img class=" h-full object-cover    md:rounded-s-lg md:rounded-t-none  rounded-t-lg"
                                    src="{{ $course->coverImage }}" alt="Movie" />
                            </a>
                        </figure>
                    </div>
                    <div class="md:col-span-2  ">
                        <div class="card-body gap-0">
                            <a href="{{ route('app.lessons.by-course', $course) }}">
                                <h2 class="card-title hover:text-primary">{{ $course->title }}</h2>
                            </a>
                            <span class="text-xs mt-1 text-gray-500 flex items-center gap-1">
                                <span class="icon-[mdi--calendar-month-outline]"></span>
                                {{ __('billing.enrolled_on') }} {{ $course->pivot->created_at->format('d M, Y') }}
                            </span>
                            <p class="mt-4">{{ Str::words($course->description, 20) }}</p>
                            <div class="card-actions items-center justify-between mt-4">
                                <div class="flex items-center justify-between">
                                    <progress class="progress progress-primary w-56"
                                        value="{{ $course->pivot->progress }}" max="100"></progress>
                                    <span class="ms-2 text-sm">{{ $course->pivot->progress }}%</span>
                                </div>
                                @if (!$course->is_free)
                                    <a href="{{ route('app.billing.courseInvoice', $course->slug) }}"
                                        class="btn action-btn btn-sm btn-primary btn-outline">
                                        <span class="icon-[mdi--download]"></span>
                                        {{ __('billing.invoice') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        @endforeach
    </div>
</x-layouts.profile>
