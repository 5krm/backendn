<x-layouts.app :user="$user">
    <div class="exam-info min-h-[76vh] bg-gradient-to-tl from-primary/15 via-white to-white py-3">
        <div class="flex justify-center text-[#7f7f7f] mb-10 items-center md:mx-0 mx-3">
            <div class="text-center">
                <div
                    class="md:w-[97vh]  border border-primary/20 bg-white/70 overflow-hidden backdrop-blur-sm shadow-[0_8px_30px_rgba(0,0,0,0.05)]  rounded-[1.75rem] text-start mx-auto mt-3 mb-3">
                    <div class="mb-5 bg-[#e6faf5] md:flex justify-between items-center px-10 py-7">
                        <div>
                            <h1 class="text-5xl font-light text-black mb-3">{{ $course->title }}</h1>
                            <p class="text-xl">{{ __('exam.info.prepare') }}</p>
                        </div>
                        <div class="max-sm:flex justify-end ">
                            <img  src="/assets/svg/exam_drawing.svg" alt="guide-img"  class="md:w-44 w-36" />
                        </div>
                    </div>
                    <div class="px-10 py-7">
    
                        <p>{{ __('exam.conditions.title') }}</p>
    
                        <div class="md:flex gap-16 my-6">
                            <div class="flex gap-3">
                                <div class="bg-[#e6faf5] p-2 flex justify-center item-center rounded-full">
                                    <i class="icon-[mdi--check-circle-outline] size-8  text-primary"></i>
                                </div>
    
                                <div>
                                    <p class="font-light">{{ __('exam.info.questions') }}</p>
                                    <p class="text-xl">{{$questionsNo}}</p>
                                </div>
                            </div>
                            <div class="flex gap-3">
                                <div class="bg-[#e6faf5] p-2 flex justify-center item-center rounded-full">
                                    <i class="icon-[mdi--school-outline] size-8  text-primary"></i>
                                </div>
    
                                <div>
                                    <p class="font-light">{{ __('exam.info.successPercent') }}</p>
                                    <p class="text-xl">80%</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex gap-3 border border-primary rounded-xl my-3 p-5 ">
                            <i class="icon-[mdi--lightbulb-outline] size-6  text-primary"></i>
                            <div class="mt-1 font-semibold">
                                <h2 class="text-black">{{ __('exam.info.beforeStart') }}:</h2>
                                <ul class="mt-2 space-y-2 text-gray-500 list-disc list-inside">
                                    <li>{{ __('exam.conditions.passing_percent', ['percent' => 80]) }}</li>
                                    
                                    <li>{{ __('exam.conditions.repeat') }}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:grid max-sm:space-y-3 grid-cols-2 justify-center gap-4 mt-5 md:w-[97vh] mx-auto px-3">
                    <a href="{{ route('app.courses.exam', ['course' => $course->slug]) }}"
                        class="btn rounded-full btn-primary btn-xl text-lg shadow-lg flex  btn-md capitalize">
                        <i class="icon-[mdi--text-box-edit-outline] size-6"></i>
                        {{ __('exam.takeit') }}
                    </a>

                    <a href="{{ route('app.lessons.by-course', ['course' => $course->slug]) }}"
                        class="btn btn-outline text-lg  items-center flex  border-2 primary rounded-full font-normal mt-0 capitalize">
                        <i class="icon-[mdi--book-open-blank-variant-outline] size-6 me-1"></i>
                        {{ __('exam.review') }}
                    </a>
                </div>
            </div>
        </div>
        <div class="text-center md:mb-3 mb-10 px-3">
            <p class="my-5">{!! __('exam.support_note') !!}</p>
            <a class="text-primary items-center flex justify-center"
                href="{{ route('app.lessons.by-course', ['course' => $course->slug]) }}">

                @if ($direction == 'rtl')
                    <i class="icon-[mdi--arrow-right-thick] size-5 mt-1 me-1"></i>
                @else
                    <i class="icon-[mdi--arrow-left-thick] size-5 mt-1 me-1"></i>
                @endif
                {{ __('course.backToCourse') }}
            </a>
        </div>
    </div>
</x-layouts.app>
