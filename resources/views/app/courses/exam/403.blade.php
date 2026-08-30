<x-layouts.error>
    <div class="min-h-3/4  grid grid-cols-1 md:grid-cols-2 gap-5 items-center place-items">
        <div class=" w-full flex justify-center">
            <img src="/assets/images/403.png" alt="guide-img"  width="450">
        </div>

        <div class="text-center ">
            <p class="font-bold text-xl mb-7"> {{ __('exam.403.title') }}</p>

            <p>
                {!! __('exam.403.content', [
                    'link' => route('app.courses.details', ['course' => $course]),
                    'course' => $course->title,
                ]) !!}
            </p>
            <a href="{{ route('app.lessons.by-course', ['course' => $course->slug]) }}"
                class="link text-lg mx-3 items-center flex font-medium justify-center mt-5 md:mt-10">
                <i class="icon-[mdi--play-circle] size-6 me-1"></i>
                {{ __('course.continueLearning') }}
            </a>
        </div>

    </div>
</x-layouts.error>
