<div class="bg-white border border-gray-200 rounded-lg p-8 h-full flex flex-col">
    {{-- Quote icon --}}
    <div class="mb-4">
        @if ($direction == 'rtl')
            <span class="icon-[mdi--format-quote-close] w-10 h-10 text-gray-200"></span>
        @else
            <span class="icon-[mdi--format-quote-open] w-10 h-10 text-gray-200"></span>
        @endif
    </div>

    {{-- Testimonial content --}}
    <p class="text-gray-700 leading-relaxed text-lg mb-6 flex-1">
        "{{ $testimonial->content }}"
    </p>

    {{-- Author info --}}
    <div class="flex items-center gap-4 pt-6 border-t border-gray-200">
        @if (str_contains($testimonial->author_image, 'default-user.png'))
            <div
                class="w-14 h-14 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xl">
                {{ substr($testimonial->name, 0, 1) }}
            </div>
        @else
            <img class="w-12 h-12 rounded-full object-cover ring-2 ring-white/30"
                src="{{ $testimonial->author_image }}" />
        @endif
        <div>
            <p class="font-semibold text-secondary text-lg">{{ $testimonial->name }}</p>
            <p class="text-gray-600">{{ $testimonial->job_title }}</p>
        </div>
    </div>
</div>
