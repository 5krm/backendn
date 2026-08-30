<x-layouts.app :user="auth()->user()">
    <div class="exam-info min-h-[76vh] bg-gradient-to-tl from-primary/15 via-white to-white py-3 flex items-center justify-center">
        
            <livewire:course-rating :course_slug="$course" :display_course="true" >

            <div class="mx-auto mt-5">
                
                <a href="{{ route('app.lessons.by-course', ['course' => $course]) }}"
                    class="link mx-1 flex items-center justify-center" type="button">
                    @if ($direction == 'rtl')
                        <i class="icon-[mdi--arrow-right] size-4 me-1 mt-1"></i>
                    @else
                        <i class="icon-[mdi--arrow-left] size-4 me-1 mt-1"></i>
                    @endif
                    {{ __('course.backToCourse') }}
                </a>
            </div>
        </livewire:course-rating>
    </div>
</x-layouts.app>
