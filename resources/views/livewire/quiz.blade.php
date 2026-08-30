<div>

    <div id="alert-border-1"
        class="flex items-center p-4 mb-4 text-blue-800 border-l-4 border-blue-300 bg-blue-50 dark:text-blue-400 "
        role="alert">
        <i class="icon-[mdi--info-outline]"></i>
        <div class="ms-3 text-sm font-medium">
            {!! __('quizes.note') !!}
        </div>
    </div>

    @error('answers')
        <div id="alert-border-1" class="flex items-center p-4 mb-4 text-red-500 border-l-4 border-red-300 bg-red-50 "
            role="alert">
            <i class="icon-[mdi--info-outline]"></i>
            <div class="ms-3 text-sm font-medium">
                {{ $message }}
            </div>
        </div>
    @enderror



    @foreach ($this->questions as $question)
        <div @class([
            'mb-5 pt-3',
            'bg-red-50 p-5' => Session::has('score') && !$question->is_correct,
            'bg-teal-50 p-5' => Session::has('score') && $question->is_correct,
        ])>
            <div class="flex items-center">
                <div class="flex">
                    <h3 class="font-bold text-md"> {{ $question->question }}</h3>
                    @if (Session::has('score'))
                        @if (!$question->is_correct)
                            <span class="flex items-center text-xs ms-3 font-bold text-red-500 "><i
                                    class="size-4 me-1 icon-[mdi--close-thick]"></i>
                                {{ __('quizes.notCorrect') }}</span>
                        @else
                            <span class="flex items-center text-xs ms-3 font-bold text-primary "><i
                                    class="size-4 me-1 icon-[mdi--check-bold]"></i>
                                {{ __('quizes.correct') }}</span>
                        @endif
                    @endif
                </div>
            </div>
            @foreach ($question->quizOptions as $key => $option)
                <div class="flex items-center my-4 ">
                    <input id="option-{{ $option->id }}" type="radio" value="{{ $option->id }}"
                        wire:model="answers.{{ $question->id }}" name="answers[{{ $question->id }}]"
                        @if (old('answers.{{ $question->id }}')) checked @endif class="me-2 radio radio-sm radio-info">
                    <label for="option-{{ $option->id }}"
                        class="ms-2 text-sm font-medium hover:cursor-pointer">{{ $option->value }}</label>
                </div>
            @endforeach

        </div>
    @endforeach


    <div class="card-footer row  pt-10">
        @if (Session::has('score'))
            <p class=" p-3 mb-5 bg-teal-50 text-primary rounded">{!! __('quizes.scoreNote', ['score' => session('score'), 'total' => $this->questions->count()]) !!}</p>
        @endif
        <div class="flex justify-end">
            <form wire:submit="save" class=" mb-5">
                @csrf
                <button class="btn btn-sm mx-1 btn-primary" type="submit">{{ __('base.check') }}</button>
                @if ($this->is_completed)
                    @if ($lesson['next'])
                        <a href="/app/courses/lessons/{{ $lesson['next']?->public_key }}"
                            class="btn btn-sm mx-1 btn-primary">{{ __('lessons.continue') }}</a>
                    @endif
                @endif
            </form>

        </div>
    </div>

</div>
