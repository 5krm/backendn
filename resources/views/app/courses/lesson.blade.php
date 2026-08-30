<x-layouts.lesson :$enrollment :$course :$sections :$lesson>
    <div class="lesson h-full relative  bg-base-100  flex-col">
        <div class="mx-4 p-2">

            @if (!empty($lesson['video']))
                {!! $lesson['video'] !!}
            @elseif (!empty($lesson['video_id']))
                <iframe class="w-full my-1 rounded-lg" height="315"
                    src="https://www.youtube.com/embed/{{ $lesson['video_id'] }}" title="YouTube video player"
                    frameborder="0"
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                    allowfullscreen></iframe>
            @else
                <div class="w-full h-[315px] bg-slate-100 rounded-lg flex items-center justify-center">
                    <div class="text-center text-slate-500">
                        <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <p>{{ __('lessons.no_video') }}</p>
                    </div>
                </div>
            @endif

        </div>
        <div class="px-6">
            <div class="pt-5 rounded-md">
                <div class="lg:hidden flex justify-between mb-5">
                    <button data-target="#chapters" class="btn btn-xs btn-accent"><span
                            class="icon-[mdi--book-settings-outline]"></span>
                        {{ trans_choice('course.theChapters', 2) }}</button>
                    <button data-target="#notes" class="btn btn-xs btn-accent"><span
                            class="icon-[mdi--notes-outline]"></span>{{ __('notes.notes') }}</button>
                </div>

                <h1 class="text-xl font-semibold ">
                    {{ $lesson['title'] }}
                </h1>
                <div class="flex items-center mt-2">
                    <span class="icon-[mdi--clock-outline] size-3"></span>
                    <span class="ms-1 text-xs text-gray-700">{{ __('course.duration') }}</span>
                    <span class="ms-2 text-xs">{{ $lesson['duration'] }}</span>
                </div>

                <div class="row flex items-center justify-between mt-6">
                    <div>
                        @if ($lesson['previous'])
                            <a href="/app/courses/lessons/{{ $lesson['previous']?->public_key }}"
                                onclick="this.classList.add('pointer-events-none','opacity-50')"
                                class="btn btn-sm btn-accent text-secondary">
                                <span
                                    class="{{ $direction == 'rtl' ? 'icon-[mdi--chevron-right]' : 'icon-[mdi--chevron-left]' }} size-5"></span>
                                <span>{{ __('base.back') }}</span>
                            </a>
                        @endif
                    </div>
                    <div class="self-stretch">
                        @if ($lesson['next'])
                        
                            @if (!$lesson['completed_at'] && $lesson['has_quiz'])
                                <x-dialog :title="__('quizes.quiz', ['lesson' => $lesson['title']])">
                                    <x-slot:btn class="btn btn-sm h-full btn-primary btn-outline pe-2">
                                        <span>{{ __('base.next') }}</span>
                                        <span
                                            class="{{ $direction == 'rtl' ? 'icon-[mdi--chevron-left]' : 'icon-[mdi--chevron-right]' }} h-6 w-6"></span>
                                    </x-slot:btn>
                                    @livewire('Quiz', ['lesson' => $lesson], key(Str::random()))
                                </x-dialog>
                            @else
                                <a href="/app/courses/lessons/{{ $lesson['next']?->public_key }}"
                                    onclick="this.classList.add('pointer-events-none','opacity-50')"
                                    class="btn pe-2 btn-sm h-full btn-primary btn-outline md:mt-0 mt-8">
                                    <span>{{ __('base.next') }}</span>
                                    <span
                                        class="{{ $direction == 'rtl' ? 'icon-[mdi--chevron-left]' : 'icon-[mdi--chevron-right]' }} h-6 w-6"></span>
                                </a>
                            @endif
                        @else
                            @if (!$lesson['completed_at'] && $lesson['has_quiz'])
                                <x-dialog :title="__('quizes.quiz', ['lesson' => $lesson['title']])">
                                    <x-slot:btn class="btn btn-sm h-full btn-primary btn-outline pe-2">
                                        <span>{{ __('quizes.takeQuiz') }}</span>
                                        <span class="icon-[mdi--file-question-outline] h-6 w-6"></span>
                                    </x-slot:btn>
                                    @livewire('Quiz', ['lesson' => $lesson], key(Str::random()))
                                </x-dialog>
                            @endif
                            
                            @if (!$lesson['completed_at'] && !$lesson['has_quiz'] )
                                <form action="{{ route('app.lessons.complete_lesson', $lesson['public_key']) }}"
                                    method="POST" class="inline-block">
                                    @csrf
                                    <button type="submit"
                                        class="btn pe-2 btn-sm h-full btn-primary btn-outline md:mt-0 mt-8">
                                        <span class="icon-[mdi--check-circle] h-4 w-4"></span>
                                        <span>{{ __('lessons.mark_complete') }}</span>
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </div>
            </div>

            <div class="mt-5">
                <div class="prose max-w-full lesson-content overflow-x-auto">
                    {!! $lesson['content'] !!}
                </div>
                <div role="tablist" class="mt-16 tabs tabs-primary tabs-bordered ">
                    <input type="radio" name="my_tabs_1" role="tab" class="tab font-bold text-lg "
                        aria-label="{{ trans_choice('lessons.discussions', 1) }}" checked />
                    <div role="tabpanel" class="tab-content p-5" id="quiz_container">
                        <livewire:comments :lesson_id="$lesson['lesson_id']" />
                    </div>

                    <input type="radio" name="my_tabs_1" role="tab" class="tab font-bold text-lg  "
                        aria-label="{{ __('resources.resources') }}" />
                    <div role="tabpanel" class="tab-content p-5">
                        <livewire:lesson-resources />
                    </div>
                </div>
            </div>
        </div>
    </div>
    <x-lessons.congrats-popup :$lesson :$course />


</x-layouts.lesson>

<div class="bg-[#fde68a]"></div>
<div class="bg-[#d9f99d]"></div>
<div class="bg-[#fed7aa]"></div>
<div class="bg-[#e7e5e4]"></div>
<div class="bg-[#ddd6fe]"></div>
