<x-layouts.main>
    <x-navbar></x-navbar>
    <div class="relative">
        <div class="absolute top-0 left-0 w-full z-0 bg-secondary text-center text-white lg:py-40 py-24  !bg-cover  !bg-no-repeat "
            style=" 
background: linear-gradient(180deg, rgba(255, 255, 255, 0.2) -76.92%, rgba(255, 255, 255, 0) 77.38%),url('{{ asset('/assets/svg/lines.svg') }}'),#003;">
        </div>

        <div class="lg:w-5/12 w-full px-4 mx-auto  mb-10  pt-16 relative z-10">
            <a href="{{ route('app.courses.details', $course['data']->slug) }}" class="text-primary flex items-center">
                <span
                    class="me-2 {{ $direction != 'rtl' ? 'icon-[mdi--arrow-left]' : 'icon-[mdi--arrow-right]' }}"></span>
                {{__('course.back_to_course')}}
                </a>
            <img class="w-full rounded-xl mt-3" src="{{ $course['file'] }}" alt="{{ $course['data']->title }}" />
            <div class="px-2">
                <h1 class="text-2xl font-bold text-secondary mt-5">
                    {{ $course['data']->title }}
                </h1>
                <p class="mt-5 text-lg ">
                    @if($course['data']->is_free)
                        {!! __('course.free_enroll_success', ['link' => route('dashboard')]) !!}
                    @else
                    {!! __('course.enroll_success', ['link' => route('app.billing')]) !!}
                    @endif
                </p>
                <div>
                    <a href="/app/courses/{{ $course['data']->slug }}/lessons"
                        class="mt-10 btn btn-primary items-center flex">
                        {{ __('course.continue_to_lessons') }} <span
                            class="{{ $direction == 'rtl' ? 'icon-[mdi--arrow-left]' : 'icon-[mdi--arrow-right]' }}"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <x-footer></x-footer>
</x-layouts.main>
