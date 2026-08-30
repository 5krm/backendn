@props([
    'course',
    'wishlistKey' => null,
])

@php
    $wishlistKey = $wishlistKey ?? 'wishlist-' . $course['data']->id;
@endphp

<div
    {{ $attributes->class('group rounded-xl border border-gray-200 hover:shadow-lg transition duration-300 overflow-hidden bg-white') }}>
    <div class="h-40 bg-gray-200 relative overflow-hidden">
        <img src="{{ $course['file'] }}" alt="{{ $course['data']->title }}"
            class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        <div class="absolute top-3 right-3 flex gap-2">
            <div class="badge badge-ghost py-3 px-2 font-semibold">
                {{ $course['data']->level?->getLabel() }}
            </div>
        </div>
    </div>
    <div class="p-5">
        <a href="{{ $course['link'] ?? url('/app/courses/' . $course['data']->slug) }}" class="block">
            <h3 class="font-bold text-gray-900 group-hover:text-primary transition">
                {{ $course['data']->title }}
            </h3>
        </a>

        @if ($course['data']->category)
            <p class="text-sm text-gray-500 mt-2 line-clamp-2">
                {{ $course['data']->category->localized_name }}
            </p>
        @endif

        <div class="mt-4 flex justify-between items-center">
            @if ($course['data']->is_free)
                <span class="text-lg font-bold text-gray-900">{{ __('course.free') }}</span>
            @elseif ($course['data']->price > 0)
                <span class="text-lg font-bold text-gray-900">
                    {{ Number::currency($course['data']->price) }}
                </span>
            @endif

            <div class="flex items-center text-sm">
                @if ($course['is_preview'])
                    <livewire:wishlist-button
                        :course="$course"
                        :is-wishlisted="$course['in_wishlist'] ?? false"
                        :key="$wishlistKey"
                    />
                @elseif ($course['enrolled'])
                    <a href="{{ route('app.lessons.by-course', $course['data']) }}"
                        class="btn btn-primary btn-outline">
                        {{ __('base.continue') }}
                        <span
                            class="{{ ($direction ?? 'ltr') == 'rtl' ? 'icon-[mdi--arrow-left]' : 'icon-[mdi--arrow-right]' }}"></span>
                    </a>
                @else
                    <a href="{{ route('app.courses.enroll.form', $course['data']) }}" class="btn btn-primary">
                        {{ $course['data']->is_free ? __('course.enrollment.for_free') : __('course.enrollment.enrollNow') }}
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
