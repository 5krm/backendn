<div class="flex flex-col items-center justify-center px-8 py-3 bg-white font-sans">
    <?php $progressOffset = max(0, min(100, 100 - $percentage)); ?>

    <!-- Outer Wrapper for the Circular Progress -->
    <div class="relative flex items-center justify-center w-48 h-48">

        <!-- SVG Ring -->
        <svg class="w-full h-full -rotate-90" viewBox="0 0 36 36">
            <!-- Background Track Circle -->
            <circle cx="18" cy="18" r="15.915" fill="none" class="stroke-gray-200" stroke-width="2.5" />
            <!-- Progress Bar -->
            <circle cx="18" cy="18" r="15.915" fill="none"
                class="{{ $percentage >= $min ? 'stroke-primary' : 'stroke-warning' }} transition-all duration-500 ease-out"
                stroke-width="2.5" stroke-dasharray="100" stroke-dashoffset="{{ $progressOffset }}" stroke-linecap="round" />
        </svg>

        <!-- Centered Content -->
        <div class="absolute flex flex-col items-center justify-center text-center">
            <span class="text-4xl  text-gray-700 tracking-tight">{{ round($percentage,1) }}%</span>
            <span class="text-xs font-semibold text-gray-400 tracking-widest uppercase mt-0.5">
                {{__('tutor.table.score')}}
            </span>
        </div>

        <!-- Bottom Ribbon/Badge Icon -->
        <div
            class="absolute -bottom-2 flex items-center justify-center w-8 h-8 rounded-full {{ $percentage >= $min ? 'bg-primary' : 'bg-warning' }}  text-white shadow-sm">
            <i class="{{ $percentage >= $min ? 'icon-[mdi--seal]' : 'icon-[mdi--seal]' }} size-6 text-white"></i>
        </div>

    </div>

    <!-- Heading Text Below -->
    <h2 class="mt-8 text-3xl font-light text-gray-700">
        @if ($percentage >= $min)
            {{__('exam.result.greatJob')}}
        @else
            {{__('exam.result.tryAgain')}}
        @endif
    </h2>
