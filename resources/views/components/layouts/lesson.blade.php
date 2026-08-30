<x-layouts.main>
    <div class="shadow-md h-auto relative z-10">
        <div class="  mx-auto flex items-center justify-between p-3">
            <div class="flex items-center">
                <a href="/app/courses/{{ $course['data']->slug }}" class="flex items-center font-bold">
                    @if ($direction == 'rtl')
                        <i class="icon-[mdi--arrow-right] size-5 me-2"></i>
                    @else
                        <i class="icon-[mdi--arrow-left] size-5 me-2"></i>
                    @endif
                    {{ $course['data']->title }}
                </a>
                <p class="md:ms-2 ms-6 md:block hidden">
                    {{ Str::limit($lesson['title'], 30, $end = '...') }}
                </p>
            </div>
            <div class="md:w-1/3 w-1/2 ps-5">
                @unless ($enrollment?->score)
                    <span class="font-bold text-xs">{{ __('course.yourProgress') }}</span>
                    <div class="flex items-center">
                        <progress class="progress progress-primary " value="{{ round($enrollment?->progress) }}"
                            max="100" min="1"></progress>
                        <p class="ms-2 text-end text-xs">
                            {{ round(($enrollment?->progress / 100) * $course['data']->published_lessons_count) }}/{{ $course['data']->published_lessons_count }}
                        </p>
                    </div>
                @else
                    <div class="flex items-center justify-end">
                        <i class="icon-[mdi--check-circle] size-4 text-primary me-1"></i> <span
                            class="font-bold text-sm text-primary">{{ __('course.passed') }}</span>
                    </div>
                @endunless
            </div>
        </div>
    </div>
    <main class="flex relative bg-white">
        <x-drawer :title="trans_choice('course.theChapters', 2)" :id="'chapters'">
            @foreach ($sections as $section)                
                <x-lessons.lessons-section :current="$lesson" :section="$section" :index="$loop->index + 1" />
            @endforeach
            <div class="pt-3">
                @if ($enrollment?->progress == 100)
                    <h3 class="text-lg font-bold ">{{ __('exam.ready') }}</h3>
                    <a href="{{ route('app.courses.exam-info', ['course' => $course['data']->slug]) }}"
                        class="p-3 flex items-center group hover:bg-[#f1f5f9] hover:text-primary ">
                        <div
                            class="w-8 h-8 max-h-10 max-w-10  group-hover:text-primary group-hover:border-primary flex justify-center items-center rounded-full border-[3px]  font-bold border-[grey] text-[grey]">
                            <i class="icon-[mdi--text-box-edit-outline] size-5"></i>
                        </div>
                        <div class="ms-5 text-sm ">
                            <p class="font-medium group-hover:text-primary">{{ __('exam.takeit') }}</p>
                            <p class="text-[grey] my-2 text-xs">
                                {{ __('exam.guide') }}
                            </p>
                        </div>
                    </a>
                @endif
            </div>
        </x-drawer>
        <div class="flex-1 min-w-0 relative mx-auto lg:px-8 px-2 lg:max-w-[70%] overflow-x-hidden pt-5">
                {{ $slot }}
        </div>

        <x-drawer :title="__('notes.notes')" :id="'notes'" :position="'end-0'">
            <livewire:lesson-notes />
        </x-drawer>
    </main>
</x-layouts.main>
