<div
    class="flex h-full flex-col w-[100vh] md:w-[320px] md:h-[390px] overflow-hidden rounded-2xl border border-primary/20 bg-white shadow-sm transform-gpu transition-all duration-700 ease-out hover:-translate-y-1 hover:scale-[1.02] hover:shadow-lg will-change-transform group">

    <div class="relative h-44 w-full shrink-0">
        <img src="{{ $enrollment->course->cover_image }}" alt="{{ $enrollment->course->title }}"
            class="h-full w-full object-cover" />

        <span class="absolute top-3 left-3 rounded-xl bg-sky-50 px-3 py-1 text-xs font-semibold text-sky-700 shadow-sm">
            {{ $enrollment->course->category->name }}
        </span>
       
    </div>

    <div class="flex flex-1 flex-col p-5">
        <div>
            <div class="flex justify-between items-center  text-xs font-medium text-gray-500">
                <p class="text-slate-500">{{ __('student.course.with') }}
                    <a href="{{ route('tutor.index', $enrollment->course->tutor) }}" target="_blank"
                        class="mx-1 font-bold link">{{ $enrollment->course->tutor->name }}</a>
                </p>
                @if ($type != 'wishlist' && $enrollment->course->averageRating > 0)
                    <div class="flex gap-1.5 items-center">
                        <i class="icon-[mdi--star] text-amber-400 size-4 mb-1"></i>
                        <span class="font-bold text-gray-700">{{ $enrollment->course->averageRating }}</span>
                    </div>
                @endif
            </div>

            <h3 class="mt-2 text-lg font-bold text-gray-900 leading-snug group-hover:text-primary">
                <a href="{{ route('courses.details', $enrollment->course) }}">
                    {{ $enrollment->course->title }}
                </a>
            </h3>

            @if ($type == 'wishlist')
                <div
                    class=" text-center rounded-2xl p-4 mt-auto bg-gray-50/80 border  border-dashed border-gray-200/50  ">
                    <div class="flex-1 flex flex-col items-center   ">
                        <span class=" text-lg font-bold uppercase text-black mb-2">
                            <span class="text-xl text-white">✨</span>
                            {{ __('course.coming_soon') }}
                        </span>
                        <span class="text-xs text-gray-600 tracking-wider">{{ __('course.notify_msg') }}</span>
                    </div>
                </div>
            @endif
        </div>

        <div class="mt-auto pt-4">
            @if ($type != 'wishlist')
                <div class="mt-4 flex items-center justify-between gap-4 text-xs font-medium text-gray-500">
                    <div class="flex items-center gap-1.5">
                        <i class="icon-[mdi--book-open-blank-variant-outline] size-4 mb-1"></i>
                        <span>{{ __('student.lessons', ['count' => $enrollment->course->lessons_count]) }}</span>
                    </div>
                    <span class="text-slate-500 text-xs">{{ $enrollment->progress }}%</span>
                </div>
                <progress class="progress progress-primary w-full mt-2" value="{{ $enrollment->progress }}"
                    max="100"></progress>
            @endif
            {{ $slot }}
        </div>

    </div>
</div>
