<div >
    <?php $counter = 1; ?>
    <p class="text-start text-slate-500 my-3">{{__('exam.feedback.message')}}</p>
    @foreach ($questions as $question)
        <?php $failed = $answers[$question->id] != $question->correctOption; ?>
        <div @class([
            'mb-5 py-7 rounded-lg border text-start',
            'border-error p-5' => $failed,
            'border-primary p-5' => !$failed,
        ])>
            <p class="font-medium text-md">
                <span class="py-1 px-3 rounded-full bg-primary/10 text-primary me-3">{{ $counter++ }}</span>
                {{ $question->question }}
            </p>
            <p class="capitalize text-slate-500 font-light mt-5 mb-2 text-sm">{{__('exam.feedback.answer')}}:</p>
            @if ($failed)
                <div class="p-3 bg-error/10 rounded-lg flex items-center gap-3 text-error ">
                    <i class="icon-[mdi--close-circle] text-error"></i>
                    <?php $answerId = $answers[$question->id]; ?>
                    {{ $question->quizOptions->find($answerId)->value }}
                </div>
                <hr class="my-5" />
                <p class=" mb-2 capitalize text-slate-500 font-light text-sm">{{__('exam.feedback.correct')}}:</p>
                <p class="bg-primary/10 p-3 flex gap-3 items-center text-primary rounded-xl">
                    <i class="icon-[mdi--check] text-primary"></i>
                    {{ $question->quizOptions->find($question->correctOption)->value  }}
                </p>
            @else
                <div class="p-3 bg-primary/10 rounded-lg flex justify-between items-center gap-3">
                    <div class="flex  items-center gap-3">
                        <i class="icon-[mdi--check-circle] text-primary"></i>
                        <?php $answerId = $answers[$question->id]; ?>
                        {{ $question->quizOptions->find($answerId)->value }}
                    </div>
                    <span class="text-sm text-primary font-semibold me-2">{{__('exam.feedback.welldone')}}!</span>
                </div>
            @endif
        </div>
    @endforeach
</div>
