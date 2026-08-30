{{-- @php
    $isPreview = $course['status'] === 'preview';
@endphp --}}

<div
    class="card bg-base-100 shadow-md border  border-gray-100 rounded-[2rem] group hover:shadow-xl transition-shadow duration-300 flex flex-col ">

    <figure class="relative overflow-hidden flex-shrink-0">
        <a href="{{ $course['link'] }}" class="w-full">
            <img class="w-full aspect-[4/3] object-cover transition-transform duration-500 group-hover:scale-105"
                src="{{ $course['data']->cover_url ?: $course['file'] }}" alt="{{ $course['data']->title }}" />
        </a>

        <div class="absolute top-4 left-4 flex flex-col gap-2">
            @if ($course['data']->is_free)
                <div
                    class="bg-primary/90 backdrop-blur-sm text-white rounded-full px-4 py-1 text-sm font-bold shadow-sm">
                    {{ __('course.free') }}
                </div>
            @elseif($course['data']->price > 0)
                <div
                    class="bg-white/90 backdrop-blur-sm text-secondary rounded-full px-4 py-1 text-sm font-bold shadow-sm">
                    {{ Number::currency($course['promo']['price'] ?? $course['data']->price) }}
                </div>
            @endif
        </div>

        @if ($course['discount'] > 0 && !$course['data']->is_free)
            <div class="absolute bottom-4 left-4 rounded-2xl bg-secondary/95 px-4 py-3 text-white shadow-lg backdrop-blur-sm">
                <div class="flex items-baseline gap-1 leading-none">
                    <span class="text-3xl font-black">{{ $course['discount'] }}%</span>
                    <span class="text-xs font-extrabold uppercase tracking-wide">{{ __('course.off') }}</span>
                </div>

                @if ($course['promo'])
                    <div class="mt-2 flex items-center gap-1.5 rounded-lg bg-white/15 px-2 py-1 text-[11px] font-bold">
                        <i class="icon-[mdi--clock-outline] text-sm"></i>
                        <span>{{ trans_choice('course.sale_ends_in', max(1, (int) $course['promo']['days'])) }}</span>
                    </div>
                @endif
            </div>
        @endif
    </figure>

    <div class="p-6 flex flex-col gap-3 flex-1">
        @if ($course['data']->category)
            <div class="text-[10px] font-extrabold tracking-[0.15em] text-primary uppercase">
                {{ $course['data']->category->localized_name }}
            </div>
        @endif

        <a href="{{ $course['link'] }}" class="block">
            <h2
                class="text-xl font-bold text-secondary line-clamp-2 leading-tight hover:text-primary transition-colors">
                {{ $course['data']->title }}
            </h2>
        </a>
      <div class="mt-auto">
                @if ($course['is_preview'])
                    <div class="bg-yellow-50 border border-yellow-200 text-center rounded-2xl p-6 mb-6   ">
                        <span class="  font-bold text-yellow-700">
                            🚧 {{ __('course.coming_soon') }}
                        </span>
                    </div>
                @else
                <div class="flex items-center justify-between bg-gray-50/80 rounded-2xl p-4 mt-4 border border-gray-100/50">
                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="flex items-center gap-2">
                            <span class="text-base font-black text-secondary">{{ $course['data']->lessons_count ?? 0 }}</span>
                            <i class="icon-[mdi--book-open-blank-variant] text-gray-400 text-lg opacity-70"></i>
                        </div>
                        <span
                            class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider">{{ __('course.lessons') }}</span>
                    </div>

                    <div class="w-px h-8 bg-gray-200/60"></div>

                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="flex items-center gap-2">
                            <span class="text-base font-black text-secondary">{{ $course['data']->students_count ?? 0 }}</span>
                            <i class="icon-[mdi--accounts] text-gray-400 text-lg opacity-70"></i>
                        </div>
                        <span
                            class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider">{{ __('course.enrolled') }}</span>
                    </div>

                    <div class="w-px h-8 bg-gray-200/60"></div>

                    <div class="flex-1 flex flex-col items-center gap-1">
                        <div class="flex items-center gap-2">
                            <span class="text-base font-black text-secondary">{{ $course['data']->textDuration ?? 0 }}</span>
                        </div>
                        <span
                            class="text-[10px] text-gray-400 font-extrabold uppercase tracking-wider">{{ __('course.duration') }}</span>
                    </div>
                </div>
                @endif

                <div class="flex items-center justify-between mt-5">
                    <div class="flex flex-col">
                        @if ($course['data']->price > 0 && !$course['data']->is_free)
                            <div class="flex items-center gap-2">
                                <span
                                    class="text-xl font-black text-secondary">{{ Number::currency($course['promo']['price'] ?? $course['data']->price) }}</span>
                                @if ($course['promo'] || $course['data']->old_price > $course['data']->price)
                                    <span
                                        class="text-xs line-through text-gray-400">{{ Number::currency($course['promo']['old_price'] ?? $course['data']->old_price) }}</span>
                                @endif
                            </div>
                        @else
                            <span class="text-xl font-black text-primary uppercase">{{ __('course.free') }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2">
                    @if ($course['is_preview'])
                            @if($course['in_wishlist'] )
                                <span
                                    disabled
                                    class="text-xs  py-4 text-gray-400 " >
                                     {{ __('course.added_to_wishlist') }}
                                </span>
                            @else
                                <button href="{{ $course['link'] }}/enroll" class="btn btn-grey ">
                                    {{   __('course.notify_me') }}
                                </button>
                            @endif
                        @elseif($course['enrolled'])
                            <a href="{{ route('app.lessons.by-course', $course['data']) }}"
                                class="btn btn-primary btn-outline">
                                {{ __('base.continue') }}
                                <span class="icon-[mdi--arrow-left]"></span>
                            </a>
                        @else
                            <a href="{{ $course['link'] }}/enroll" class="btn btn-primary ">
                                {{ $course['data']->is_free ? __('course.enrollment.for_free') : __('course.enrollment.enrollNow') }}
                            </a>
                        @endif
                    </div>
                </div>
        </div>
    </div>
</div>
