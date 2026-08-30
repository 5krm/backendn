<?php
$label = $score >= 95 ? __('student.score.excelent') : ($score >= 87 ? __('student.score.great') : __('student.score.good'));
$color = $score >= 95 ? 'primary' : 'info';
?>
<div class="flex items-center gap-3">
    <div class="relative flex h-14 w-14 items-center justify-center">
        <div class="relative flex h-14 w-14 items-center justify-center">
            <svg class="h-full w-full -rotate-90 transform" viewBox="0 0 36 36">
                <path class="text-gray-100" stroke-width="3.5" stroke="currentColor" fill="none"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />

                <path class="text-{{ $color }} transition-all duration-1000 ease-out"
                    stroke-dasharray="{{ $score }}, 100" stroke-width="3.5" stroke-linecap="round"
                    stroke="currentColor" fill="none"
                    d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            </svg>
        </div>
        <span class="absolute text-xs font-bold text-slate-500">{{ $score }}%</span>
    </div>
    <div>
        <p class="font-semibold text-gray-900 text-sm">{{ $label }}</p>
        <p class="text-xs text-gray-400">{{ __('student.score.your_score') }}</p>
    </div>
</div>
