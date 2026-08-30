<?php
$is_current = (request()->route()->parameters['lesson'] ?? null) == $lesson['public_key'];
$completed = $lesson['completed_at'];
$prev_completed = $lesson['prev_completed']??false;

$is_open = $is_current || $completed || $prev_completed;
?>

<a href="{{ $is_open ? '/app/courses/lessons/' . $lesson['public_key'] : '#' }}" @class([
    'p-2 flex items-center',
    'hover:bg-[#f1f5f9] hover:text-primary rounded-lg' => $is_open,
    'hover:cursor-not-allowed' => !$is_open,
])>
    <div @class([
        'flex justify-center items-center',
        'w-6 h-6 border-primary text-primary' => $is_current,
    ])>
        @if ($is_current)
            <i class="icon-[mdi--play-circle-outline] size-6 "></i>
        @elseif ($is_open)
            <span class="p-1 px-1.5 text-sm">{{ sprintf('%02d', $index ?? $lesson['order']) }}</span>
        @else
            <i class="icon-[mdi--lock] size-4 m-1"></i>
        @endif
    </div>
    <div class="ms-4 text-xs">
        <p>{{ $lesson['title'] }}</p>
        <span class="text-gray-500">{{ $lesson['duration'] }}</span>
    </div>
</a>
