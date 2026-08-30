<div class="mb-16">
    <p class="text-slate-500 text-sm">{{__('student.course.leftoff')}}</p>
    <div class="mt-1 card flex-col md:flex-row bg-base-100 shadow-xl border border-primary/20">
        <figure class="md:w-1/4 md:rounded-e-none md:rounded-s-2xl">
            <div class="group relative h-full w-full overflow-hidden rounded-2xl md:rounded-e-none md:rounded-s-2xl">

                <img class="h-full w-full max-h-60 object-cover transition-transform duration-300 scale-105"
                    src="{{ $lesson->course->coverImage }}" alt="{{ $lesson->course->title }}" />

                <div class="absolute inset-0 bg-black/40  transition-opacity duration-300 opacity-100 z-10">
                    <div class="absolute inset-0 z-20 flex items-center justify-center  transition-opacity duration-300">
                        <div
                            class="rounded-full bg-primary text-white w-fit p-2 flex gap-3 text-primary text-sm mb-2 font-semibold">
                            <i class="icon-[mdi--play] size-8"></i>
                        </div>
                    </div>
                </div>

            </div>
        </figure>
        <div class="card-body gap-0">
            <div class="md:flex justify-between items-center">
                <div>
                    <a href="{{ route('app.lessons.by-course', $lesson->course) }}">
                        <h2 class="card-title hover:text-primary text-2xl">{{ $lesson->course->title }}</h2>
                    </a>
                </div>

                <div class="mb-1 flex gap-3 md:mt-0 mt-2">
                    <div class="badge badge-ghost py-3 px-2 font-semibold">
                        {{ $lesson->course->level?->getLabel() }}
                    </div>
                    @if ($lesson->course->is_free)
                        <div class="badge badge-primary text-white p-3 font-semibold">
                            {{ __('course.free') }}
                        </div>
                    @elseif($lesson->course->price > 0)
                        <div class="badge badge-secondary p-3 font-semibold">
                            {{ Number::currency($lesson->course->price) }}
                        </div>
                    @endif
                </div>
            </div>
            <p class="text-slate-500">{{__('student.course.with')}}
                <a href="{{route('tutor.index', $lesson->course->tutor)}}" target="_blank" class="mx-1 font-bold link">{{ $lesson->course->tutor->name }}</a>
            </p>
            <div class="mt-4">
                @isset($lesson->lesson)
                    <span class="text-sm text-gray-600">
                        {{ trans_choice('course.chapters', 1) }} {{ $lesson->lesson->section_order }} •
                        {{ trans_choice('lessons.lessons', 1) }}
                        {{ $lesson->lesson->lesson_order }}
                    </span>
                    <p class="mt-1"> {{ $lesson->lesson->title }} </p>
                @endisset
            </div>
            <div class="card-actions items-center justify-between my-0">
                <div class="mt-5">
                    <div class="flex justify-between items-center">

                        <p class="text-slate-500 text-sm">{{ $progress }}% {{__('student.course.complete')}}</p>
                        <p class="text-slate-500 text-sm text-end">{{__('student.course.keepit')}} 🌿</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <progress class="progress progress-primary w-36 md:w-[70vh]" value="{{ $progress }}"
                            max="100"></progress>
                    </div>
                </div>
                @if ($progress == 100 && $lesson->passed_at == null)
                    <a href="{{ route('app.courses.exam-info', $lesson->course) }}"
                        class="btn action-btn btn-sm btn-primary btn-outline flex items-center">
                        {{ __('exam.takeit') }}
                    </a>
                @else
                    <a href="{{ route('app.lessons.by-course', $lesson->course) }}"
                        class="btn action-btn btn-sm btn-primary btn-outline flex items-center">
                        {{ __('base.continue') }}
                        <span
                            class="{{ $direction == 'rtl' ? 'icon-[mdi--arrow-left]' : 'icon-[mdi--arrow-right]' }}"></span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
