<x-layouts.app :user="$user" full-width="true">


    <div class="grid grid-cols-1 md:grid-cols-3 items-center card shadow-xl ">
        <div class="text-center p-3">
            <img src="{{ asset('/assets/svg/survey.svg') }}" alt="">
            <h1 class="font-bold text-lg">{{ __('survey.tell') }}</h1>
            <p class="text-start  text-sm mt-2 px-3">
                {!! __('survey.survey_content', [
                    'course' => $course->title,
                    'link' => route('app.courses.details', ['course' => $course->slug]),
                ]) !!}
            </p>
        </div>
        <form method="POST" action="{{ route('app.survey.store', ['course' => $course->slug]) }}"
            class="h-full col-span-2 bg-[#f1f5f9]">
            @csrf
            <div class="h-full w-full  px-5 py-7">
                <div id="steps">
                    <div class=" py-4">
                        <div class="uppercase tracking-wide text-xs font-bold text-gray-500 mb-1 leading-tight"
                            id="question-step"></div>
                        <p class="text-[grey] text-xs mt-2  ">{{ __('survey.note') }}</p>
                    </div>
                    <progress class="progress progress-primary w-full rounded-sm" id="step-progress"
                        max="100"></progress>

                    <div class="py-10 ">
                        <div id="step-1">
                            <div class=" py-5">
                                <h1 class="font-bold text-xl">{{ __('survey.questions.status') }}</h1>
                                <div
                                    class="py-2 emojis divide-x divide-grey-200 grid grid-cols-3 md:grid-cols-5 @error('satisfaction') border-red-500 @enderror">
                                    @foreach (App\Enums\SatisfactionCase::values() as $satisfaction)
                                        <div onclick="setSatisfaction({{ $satisfaction }})"
                                            id="status-{{ $satisfaction }}"
                                            class="bg-white p-5 hover:bg-[#f5f6ff] hover:cursor-pointer satisfaction text-center"
                                            :style="satisfaction == {{ $satisfaction }} ? 'background:#f5f6ff' : '';">
                                            <img class="mx-auto"
                                                src="/assets/svg/emojies/{{ App\Enums\SatisfactionCase::names()[$satisfaction] }}.svg"
                                                alt="" width="56">
                                            <p class="mt-2 text-xs">
                                                {{ __('survey.satisfactions.' . App\Enums\SatisfactionCase::names()[$satisfaction]) }}
                                            </p>
                                        </div>
                                    @endforeach
                                </div>

                                <p class="text-red-500 text-sm validation-error @error('satisfaction') has-error @enderror"
                                    id="status-alert">
                                    <i class="icon-[mdi--alert-circle-outline] text-red-500"></i>
                                    {{ __('survey.validations.status') }}
                                </p>
                            </div>

                            <input type="hidden" name="satisfaction" value="{{ old('satisfaction') }}">
                            <div class="my-2">
                                <label for="comment"
                                    class="block text-gray-700 text-sm font-bold mb-2">{{ __('survey.questions.comment') }}</label>
                                <textarea id="comment" name="comment"
                                    placeholder="{{ __('survey.questions.comment_ex') }}" id="" rows="3"
                                    class="w-full rounded-sm p-3 focus:outline-none @error('comment') border-2 border-red-500 @enderror">{{ old('comment') ?? '' }}</textarea>

                                <p class="text-red-500 text-sm validation-error @error('comment') has-error @enderror"
                                    id="comment-alert">
                                    <i class="icon-[mdi--alert-circle-outline] text-red-500"></i>
                                    {{ __('survey.validations.comment', ['count' => 5]) }}
                                </p>

                            </div>
                        </div>
                        <div x-show.transition.in="step === 2" id="step-2">
                            <h1 class="font-bold text-xl">{{ __('survey.questions.expectation') }}</h1>
                            <div class=" divide-x divide-grey-200  py-3">
                                <div
                                    class="bg-white  hover:bg-[#f5f6ff] hover:cursor-pointer flex items-center my-2 p-3 rounded-sm group">
                                    <input type="radio" name="as_expected" value="yes" id="option-1"
                                        class="radio radio-sm radio-primary me-2" @if(old('as_expected') == 'yes') checked @endif />
                                    <label class="group-hover:cursor-pointer"
                                        for="option-1">{{ __('survey.expectations.yes') }}</label>
                                </div>
                                <div
                                    class="bg-white  hover:bg-[#f5f6ff] hover:cursor-pointer flex items-center my-2 p-3 rounded-sm group">
                                    <input type="radio" name="as_expected" value="somehow" id="option-2"
                                        class="radio radio-sm radio-primary me-2" @if(old('as_expected') == 'somehow') checked @endif/>
                                    <label class="group-hover:cursor-pointer"
                                        for="option-2">{{ __('survey.expectations.somehow') }}</label>
                                </div>
                                <div
                                    class="bg-white  hover:bg-[#f5f6ff] hover:cursor-pointer flex items-center my-2 p-3 rounded-sm group">
                                    <input type="radio" name="as_expected" value="no" id="option-3"
                                        class="radio radio-sm radio-primary me-2" @if(old('as_expected') == 'no') checked @endif/>
                                    <label class="group-hover:cursor-pointer"
                                        for="option-3">{{ __('survey.expectations.no') }}</label>
                                </div>
                            </div>

                            <p class="text-red-500 text-sm validation-error @error('as_expected') has-error @enderror"
                                id="expectation-alert">
                                <i class="icon-[mdi--alert-circle-outline] text-red-500"></i>
                                {{ __('survey.validations.expectation') }}
                            </p>
                        </div>
                        <div x-show.transition.in="step === 3" id="step-3">
                            <h1 class="font-bold text-xl">{{ __('survey.questions.suggestions') }}</h1>
                            <div class="emojis divide-x divide-grey-200  py-3">
                                <div
                                    class="bg-white  hover:bg-[#f5f6ff] hover:cursor-pointer flex items-center my-2 p-3 rounded-sm group">
                                    <input type="checkbox" name="suggestions[]" value="more-interactive-elements"
                                        id="more-interactive-elements"
                                        class="checkbox checkbox-sm checkbox-primary me-2" />
                                    <label class="group-hover:cursor-pointer"
                                        for="more-interactive-elements">{{ __('survey.suggestions.interactive') }}</label>
                                </div>
                                <div
                                    class="bg-white  hover:bg-[#f5f6ff] hover:cursor-pointer flex items-center my-2 p-3 rounded-sm group">
                                    <input type="checkbox" name="suggestions[]" value="use-blended-learning"
                                        id="use-blended-learning" class="checkbox checkbox-sm checkbox-primary me-2" />
                                    <label class="group-hover:cursor-pointer"
                                        for="use-blended-learning">{{ __('survey.suggestions.blending') }}</label>
                                </div>
                                <div
                                    class="bg-white  hover:bg-[#f5f6ff] hover:cursor-pointer flex items-center my-2 p-3 rounded-sm group">
                                    <input type="checkbox" name="suggestions[]" value="add-more-assessments"
                                        id="add-more-assessments"
                                        class="checkbox checkbox-sm checkbox-primary me-2" />
                                    <label class="group-hover:cursor-pointer"
                                        for="add-more-assessments">{{ __('survey.suggestions.assessments') }}</label>
                                </div>
                                <div
                                    class="bg-white  hover:bg-[#f5f6ff] hover:cursor-pointer flex items-center my-2 p-3 rounded-sm group">
                                    <input type="checkbox" name="suggestions[]" value="none" id="none"
                                        class="checkbox checkbox-sm checkbox-primary me-2" />
                                    <label class="group-hover:cursor-pointer"
                                        for="none">{{ __('survey.suggestions.none') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- / Step Content -->
                </div>
                <div class="h-full flex items-center justify-center" id="processing">

                    <div>
                        <p class="my-auto wait text-[grey]">{{ __('survey.processing') }}</p>
                        <progress class="progress w-60 progress-primary"></progress>
                    </div>

                </div>

                <div class="bottom-0 left-0 end-0 py-5 " id="control-buttons">

                    <div class="flex justify-between">
                        <div class="w-1/2">
                            <button type="button" onclick="previous()" id="previous-button"
                                class="w-32 h-10 py-2 px-5 rounded-lg shadow-sm text-center btn btn-sm btn-gray-100 font-medium border">{{ __('base.previous') }}</button>
                        </div>

                        <div class="w-1/2 text-end">
                            <button type="button" onclick="next()" id="next-button"
                                class="w-32 h-10 py-2 px-5 rounded-lg shadow-sm text-center btn btn-sm btn-primary font-medium">{{ __('base.next') }}</button>
                            <input type="submit" value="{{ __('base.submit') }}" onclick="complete()"
                                id="send-button"
                                class="w-32 h-10 py-2 px-5 rounded-lg shadow-sm text-center btn btn-sm btn-primary font-medium" />
                        </div>
                    </div>

                </div>
            </div>
    </div>
    </div>

    <script>
        let step = 1;
        let comment = document.getElementById('comment')
        let satisfaction = '{{ old('satisfaction') ?? '' }}' ?? 0;
        let stepProgress = document.getElementById('step-progress')
        let questionStep = document.getElementById('question-step')
        let Validation = {
            comment: false,
            satisfaction: false,
            expectation: false,
        };

        function show(elementId, display) {
            if (Array.isArray(elementId)) {
                elementId.forEach(item => {
                    show(item, display)
                })
                return;
            }
            var element = document.getElementById(elementId);
            element.style.display = display ?? "block";
        }

        function hide(elementId) {
            if (Array.isArray(elementId)) {
                elementId.forEach(item => {
                    hide(item)
                })
                return;
            }
            var element = document.getElementById(elementId);
            element.style.display = "none";
        }


        hide(['processing', 'send-button', 'step-2', 'step-3', 'previous-button']);
        stepProgress.setAttribute('value', parseInt(step / 3 * 100))
        let alerts = document.querySelectorAll('.validation-error:not(.has-error)')
        for (var i = 0; i < alerts.length; i++) {
            alerts[i].style.display = 'none';
        }

        function complete() {
            show('processing', 'flex')
            hide(['steps', 'control-buttons'])
        }

        function validate() {
            if (step == 1) {
                Validation.comment = comment.value.trim().split(' ').length >= 5;
                if (Validation.comment) hide('comment-alert')
                else show('comment-alert')
                if (Validation.satisfaction) hide('status-alert')
                else show('status-alert')
                return Validation.comment &&
                    Validation.satisfaction
            } else {
                document.getElementsByName('as_expected').forEach((element) => {
                    if (element.checked) {
                        Validation.expectation = true
                    }
                })
                if (Validation.expectation) hide('expectation-alert')
                else show('expectation-alert')
                return Validation.expectation;
            }
        }

        function previous() {
            step--;
            if (step == 1) {
                hide(['step-2', 'previous-button'])
                show('step-1')
            }
            if (step == 2) {
                hide('step-3')
                show('step-2')
            }
            questionStep.innerHTML = `{{ __('survey.question', ['total' => 3, 'current' => '${step}']) }}`
            stepProgress.setAttribute('value', parseInt(step / 3 * 100))
        }

        function next() {
            if (!validate()) return;
            step++;
            if (step == 2) {
                show(['step-2', 'previous-button'])
                show('next-button', 'inline')
                hide(['send-button', 'step-1'])
            }
            if (step == 3) {
                show('step-3')
                show('send-button', 'inline')
                hide(['step-2', 'next-button'])
            }
            questionStep.innerHTML = `{{ __('survey.question', ['total' => 3, 'current' => '${step}']) }}`
            stepProgress.setAttribute('value', parseInt(step / 3 * 100))
        }

        function setSatisfaction(value) {
            satisfaction = value;
            document.getElementsByName('satisfaction')[0].value = value
            Validation.satisfaction = satisfaction > 0;

            let satisfactions = document.getElementsByClassName('satisfaction')
            for (var i = 0; i < satisfactions.length; i++) {
                satisfactions[i].style.background = '#fff';
            }
            document.getElementById(`status-${value}`).style.background = '#f5f6ff'
            if (Validation.satisfaction) hide('status-alert')
            else show('status-alert')
        }
    </script>

</x-layouts.app>
