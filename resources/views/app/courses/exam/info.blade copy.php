<x-layouts.app :user="$user">
    <div class="min-h-screen">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-10 items-center">
            <div class="col-span-2 items-center">
                <h1 class="text-2xl font-bold mb-5">{{ __('exam.greetings', ['course' => $course->title]) }}</h1>
                <p>{{ __('exam.conditions.title') }}</p>

                <ul class="mt-2 space-y-1 text-gray-500 list-disc list-inside ">
                    <li>{{ __('exam.conditions.passing_percent', ['percent' => 80]) }}</li>
                    
                    <li>{{ __('exam.conditions.repeat') }}</li>
                </ul>
                <div class="flex gap-4 mt-10 ">
                    <a href="{{ route('app.courses.exam', ['course' => $course->slug]) }}"
                        class="btn btn-primary btn-xl text-lg shadow-lg circle-2xl btn-md w-48">
                        <i class="icon-[mdi--text-box-edit-outline]"></i>
                        {{ __('exam.takeit') }}
                    </a>

                    <a href="{{ route('app.lessons.by-course', ['course' => $course->slug]) }}"
                        class="link text-lg mx-3 items-center flex font-bold  ">
                        <i class="icon-[mdi--play-circle] size-9 me-1"></i>
                        {{ __('exam.review') }}
                    </a>
                </div>
            </div>
            <div class="mt-3">
                <img src="/assets/images/guide.png" alt="guide-img" class="w-full">
            </div>
        </div>
        <p class="my-5">{!! __('exam.support_note') !!}</p>
        <a class="text-primary items-center flex" href="{{ route('app.lessons.by-course', ['course' => $course->slug]) }}">

            @if ($direction == 'rtl')
                <i class="icon-[mdi--arrow-right-thick] size-5 mt-1 me-1"></i>
            @else
                <i class="icon-[mdi--arrow-left-thick] size-5 mt-1 me-1"></i>
            @endif
            {{ __('course.backToCourse') }}
        </a>
    </div>
</x-layouts.app>
