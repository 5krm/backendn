<div style="pointer-events: auto;">
    <div x-data="coursesDropdown()" x-cloak class="relative inline-block w-full">
        <button type="button"
            @click="open = !open; $event.stopImmediatePropagation();"
            class="w-full px-3 py-2 text-sm font-medium text-gray-900 dark:text-gray-100 bg-gray-100 dark:bg-gray-800 rounded hover:bg-gray-200 dark:hover:bg-gray-700 transition text-left flex items-center justify-between">
            <span>{{ __('tutor.students.courses_no', ['count' => $getRecord()->courses_count]) }}</span>
            <span class="text-xs">▼</span>
        </button>

        <div x-show="open"
            x-transition
            @click="$event.stopImmediatePropagation(); $event.preventDefault();"
            class="absolute left-0 top-full mt-1 w-48 bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 rounded shadow-lg z-50">
            <div class="py-1 max-h-60 overflow-y-auto">
                @forelse($getRecord()->courses as $course)
                    <div class="flex px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 cursor-default">
                        {{ $course->name }}
                    </div>
                @empty
                    <div class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                        {{ __('tutor.students.no_courses') }}
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const coursesDivs = document.querySelectorAll('[x-data="coursesDropdown()"]');
    coursesDivs.forEach(div => {
        div.addEventListener('click', function(e) {
            e.stopImmediatePropagation();
            e.stopPropagation();
            return false;
        }, true);
    });
});

function coursesDropdown() {
    return {
        open: false,
    };
}
</script>



