<div class="py-5">
    <div class="flex items-center justify-between border-b border-gray-200 pb-2">
        <span class="text-xs text-[grey]">{{ trans_choice('course.theChapters', 1) }} {{ $index ?? 0 }}</span>

    </div>
    <h3 class="mt-2 font-semibold">
        {{ $section['title'] }}
    </h3>

    @foreach ($section['lessons'] as $lesson)
        <x-lessons.lesson-card :current="$current" :lesson="$lesson" :index="$loop->index + 1" />
    @endforeach
</div>
