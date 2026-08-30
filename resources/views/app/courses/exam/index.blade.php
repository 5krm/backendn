<x-layouts.app :user="$user">
    <div class="exam-info py-10 bg-gradient-to-tl from-primary/15 via-white to-white">
        <div class="w-[90vh] mx-auto min-h-[76vh] py-7 my-auto ">

            <div class="w-full">
                <div class="flex justify-between">
                    <p class="text-sm text-slate-600">
                        {{ __('course.questionWizardTitle', ['current' => 1, 'total' => $questions->count()]) ?? "Question 1 of {$questions->count()}" }}
                    </p>
                    <p class="text-slate-400 text-xs" id="progressLabel">{{ $questions->count() }}%
                        {{ __('exam.complete') }}</p>
                </div>
                <progress id="questionProgress" class="progress progress-primary w-full " value="1"
                    max="{{ $questions->count() }}"></progress>
            </div>
            <div class="card-model mb-10 h-full bg-white shadow-lg rounded p-8 border border-primary/20 ">


                <form id="examForm" action="{{ route('app.courses.send_exam', ['course' => $course->slug]) }}"
                    method="post" class=" mb-5 " autocomplete="off" data-total-questions="{{ $questions->count() }}"
                    data-question-template="{{ __('exam.questionOf', ['step' => ':step', 'total' => ':total']) }}"
                    data-complete-label="{{ __('exam.complete') }}">
                    @csrf

                    @foreach ($questions as $question)
                        <div class="question-step mb-5 pt-3 transition-all duration-500 ease-out translate-x-8 opacity-0 pointer-events-none hidden"
                            data-step="{{ $loop->iteration }}">
                            <div class="flex items-center mb-3">
                                <p class="ms-3 text-xl text-base ">{{ $question->question }}</p>
                            </div>

                            @foreach ($question->quizOptions as $option)
                                <label
                                    class="option-card flex items-center my-3 rounded-lg border border-slate-200 bg-slate-50 p-4 shadow-sm transition duration-150 cursor-pointer">
                                    <input id="option-{{ $option->id }}" type="radio" value="{{ $option->id }}"
                                        name="answers[{{ $question->id }}]" class="sr-only peer" autocomplete="off">

                                    <span
                                        class="inline-flex h-5 w-5 items-center justify-center rounded-full border border-slate-300 bg-white text-primary transition peer-checked:border-primary peer-checked:bg-primary/10">
                                        <i class="icon-[mdi--check-circle] check-icon hidden peer-checked:block"></i>
                                    </span>

                                    <span class="ms-3 text-sm font-medium">{{ $option->value }}</span>
                                </label>
                            @endforeach
                        </div>
                    @endforeach

                    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between pt-8">
                        <div class="flex gap-2">
                            <button type="button" id="prevButton" class="btn btn-sm btn-outline"
                                disabled>{{ __('base.previous') ?? 'Previous' }}</button>
                            <button type="button" id="nextButton"
                                class="btn btn-sm btn-primary">{{ __('base.next') ?? 'Next Question' }}</button>
                        </div>

                        <div class="flex flex-wrap gap-2 items-center">
                            <a href="/app/courses/{{ $course->slug }}" class="btn btn-sm">{{ __('base.cancel') }}</a>
                            <button id="submitButton" class="btn btn-sm btn-primary hidden" type="submit"
                                disabled>{{ __('base.save') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

</x-layouts.app>
