<x-layouts.app :user="$user">
    <div class="exam-info card-model mb-10 h-full bg-white shadow-lg rounded p-8">
        <div class="text-center">
            @if ($score >= config('app.passing_score'))
                <div>
                    <i class="icon-[mdi--check-decagram] text-4xl text-primary"></i>
                    <p class=" p-3 mb-5 bg-teal-50 text-primary rounded">
                        {!! __('quizes.scoreResult.success', ['score' => round($score)]) !!}
                    </p>
                </div>
            @else
                <div>
                    <i class="icon-[mdi--close-circle] text-4xl text-red-500"></i>
                    <p class=" p-3 mb-5 bg-red-50 text-red-500 rounded">
                        {!! __('quizes.scoreResult.fail', ['score' => round($score)]) !!}
                    </p>
                </div>
            @endif
            <a href="{{ route('app.lessons.by-course', ['course' => $course->slug]) }}"
                class="link mx-1 flex items-center justify-center" type="button">
                @if ($direction == 'rtl')
                    <i class="icon-[mdi--arrow-right] size-4 me-1 mt-1"></i>
                @else
                    <i class="icon-[mdi--arrow-left] size-4 me-1 mt-1"></i>
                @endif
                {{ __('course.backToCourse') }}
            </a>
        </div>

        <div class="mt-8 flex flex-wrap gap-3 justify-between items-center">
            @if ($score >= config('app.passing_score'))
                <a href="{{ route('app.courses.exam-info', ['course' => $course]) }}" class="btn btn-sm btn-primary">
                    <i class="icon-[mdi--text-box-edit-outline] size-5"></i>
                    {{ __('exam.retake') }}
                </a>

                <a target="_blank" href="{{ route('app.courses.certificate', ['course' => $course]) }}"
                    class="btn btn-sm btn-primary">
                    <i class="icon-[mdi--tray-arrow-down] size-5"></i>
                    {{ __('course.getCertificate') }}
                </a>

                <a href="{{ $certificate->shareLink() }}" target="_blank" class="btn btn-sm"
                    style="background-color: #0A66C2; color: white; border-color: #0A66C2;">
                    <i class="icon-[mdi--linkedin] size-5"></i>
                    {{ __('course.shareToLinkedIn') }}
                </a>

                <a href="{{ $certificate->addToLinkedin() }}" target="_blank" class="btn btn-sm"
                    style="background-color: #0A66C2; color: white; border-color: #0A66C2;">
                    <i class="icon-[mdi--linkedin] size-5"></i>
                    {{ __('course.addToLinkedInProfile') }}
                </a>
            @else
                <a href="{{ route('app.courses.exam-info', ['course' => $course]) }}" class="btn btn-sm btn-primary">
                    <i class="icon-[mdi--text-box-edit-outline] size-5"></i>
                    {{ __('exam.retake') }}
                </a>
            @endif

        </div>

        <div class="mt-8 border-t pt-8">
            @foreach ($questions as $question)
                <?php $failed = $answers[$question->id] != $question->correctOption; ?>
                <div @class([
                    'mb-5 pt-3',
                    'bg-red-50 p-5' => $failed,
                    'bg-teal-50 p-5' => !$failed,
                ])>
                    <div class="flex items-center justify-between">
                        <p class="font-medium text-md"> {{ $question->question }}</p>
                        @if ($failed)
                            <span class="flex items-center text-xs ms-3 font-bold text-red-500 "><i
                                    class="size-4 me-1 icon-[mdi--close-thick]"></i>
                                {{ __('quizes.notCorrect') }}</span>
                        @else
                            <span class="flex items-center text-xs ms-3 font-bold text-primary "><i
                                    class="size-4 me-1 icon-[mdi--check-bold]"></i>
                                {{ __('quizes.correct') }}</span>
                        @endif

                    </div>
                    @foreach ($question->quizOptions as $key => $option)
                        <div class="flex items-center my-4 ">
                            <input id="option-{{ $option->id }}" type="radio" value="{{ $option->id }}"
                                name="answers[{{ $question->id }}]" @if (array_key_exists($question->id, $answers) && $answers[$question->id] == $option->id) checked @endif
                                class="me-2 radio radio-sm radio-info">
                            <label for="option-{{ $option->id }}"
                                class="ms-2 text-sm font-medium hover:cursor-pointer">{{ $option->value }}</label>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</x-layouts.app>
