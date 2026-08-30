<div class="space-y-2">
    @forelse($courses as $course)
        <div class="course-list-item py-3 my-2">
            <span class="text-sm text-gray-700 dark:text-gray-300">- {{ $course->title }}</span>
            <div>
                {{ $course->pivot->progress ?? 0 }}%
            </div>
        </div>
    @empty
        <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('tutor.students.no_courses') }}</p>
    @endforelse
</div>
<style>
    .course-list-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-left: 0.75rem;
        padding-right: 0.75rem;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
        background-color: #f9fafb;
        border-radius: 0.25rem;
    }

</style>
