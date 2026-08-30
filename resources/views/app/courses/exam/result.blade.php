<x-layouts.app :user="$user">
    <?php $passed = $score >= config('app.passing_score'); ?>
    @if ($passed)
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                launchFireworks();
            });
        </script>
        <x-fireworks />
    @endif
    <div class="exam-info min-h-[76vh] bg-gradient-to-tl from-primary/15 via-white to-white py-3">

        <div
            class="card-model md:w-[97vh] mx-auto border border-primary/20 mb-10 h-full bg-white shadow-lg rounded px-8 py-3">
            <div class="text-center">
                <x-percent-circular-progress :percentage="$score" :min="80" />
                @if ($score != 100)
                    <p class="text-slate-500 my-5">{{ __('quizes.scoreResult.message') }}</p>
                @endif


            </div>

            <div class="p-5 border border-primary rounded-xl mx-auto max-w-xl">
                <div class="flex space-y-3 w-full justify-between">
                    <div class="text-start">
                        <h4 class="text-sm text-primary">{{ __('course.certificate.title') }}</h4>
                        <a href="app/courses/{{ $course->slug }}" target="_blank"
                            class="link mb-2 text-2xl font-light">{{ $course->title }}</a>
                    </div>
                    <i class="{{ $passed ? 'text-primary' : 'text-slate-300' }} icon-[mdi--seal] size-8 me-1 mt-1"></i>
                </div>

                <hr class="my-3" />
                <div class="grid grid-cols-2  gap-5 text-start">
                    <div>
                        <p class="text-slate-500 text-sm">{{ __('exam.correct_answers') }}</p>
                        <p class="text-primary font-semibold">{{ $correctNo }}</p>
                    </div>
                    <div>
                        <p class="text-slate-500 text-sm">{{ __('exam.incorrect_answers') }}</p>
                        <p class="text-error font-semibold">{{ $questionsNo - $correctNo }}</p>
                    </div>
                </div>
            </div>
            <div class="mt-8 flex flex-col gap-3 mx-auto max-w-xl w-full ">

                <livewire:course-rating :course_slug="$course->slug"/>
                <button type="button" onclick="document.getElementById('feedback_modal').showModal()"
                    class="btn btn-outline font-normal btn-secondary rounded-full w-full py-3 h-auto capitalize flex items-center justify-center gap-2">
                    <i class="icon-[mdi--eye] size-5"></i>
                    <span>{{ __('exam.feedback.view') }}</span>
                </button>
                @if ($score >= config('app.passing_score'))
                    <a target="_blank" href="{{ route('app.courses.certificate', ['course' => $course]) }}"
                        class="btn btn-outline btn-primary rounded-full py-3 capitalize ">
                        <i class="icon-[mdi--tray-arrow-down] size-5"></i>
                        {{ __('course.getCertificate') }}
                    </a>
                    <a href="{{ route('app.courses.exam-info', ['course' => $course]) }}"
                        class="btn  btn-primary rounded-full py-3 capitalize ">
                        <i class="icon-[mdi--sync] size-5 mb-1"></i>
                        {{ __('exam.retake') }}
                    </a>

                    <div class="bg-accent rounded-xl p-5 text-start my-3 ">
                        <p class="text-sm mb-2">{{ __('exam.share_achievement') }}</p>
                        <div class="flex gap-10 my-3">
                            <a href="{{ $certificate->shareLink() }}" target="_blank" class="btn-linkedin ">
                                <i class="icon-[mdi--linkedin] size-5 me-1"></i>
                                {{ __('course.shareToLinkedIn') }}
                            </a>

                            <a href="{{ $certificate->addToLinkedin() }}" target="_blank" class="btn-linkedin">

                                <i class="icon-[mdi--linkedin] size-5 me-1"></i>
                                {{ __('course.addToLinkedInProfile') }}
                            </a>
                        </div>
                    </div>
                @else
                    <a href="{{ route('app.courses.exam-info', ['course' => $course]) }}"
                        class="btn btn-primary w-full rounded-full py-3 capitalize">
                        <i class="icon-[mdi--sync] size-5"></i>
                        {{ __('exam.retake') }}
                    </a>
                @endif

            </div>

            <dialog id="feedback_modal" class="modal modal-scroll modal-screen">
                <div class="modal-box max-w-5xl w-full h-full rounded-none p-0 overflow-y-auto bg-white">
                    <div
                        class="sticky top-0 z-10 flex items-center justify-between border-b bg-white  py-4 md:w-[100vh] mx-auto">
                        <div>
                            <h2 class="text-2xl font-light capitalize">{{ __('exam.review_your_answers') }}</h2>

                        </div>
                        <form method="dialog">
                            <button class="btn btn-ghost btn-circle">
                                <i class="icon-[mdi--times]"></i>
                            </button>
                        </form>
                    </div>
                    <div class="mx-auto max-w-4xl p-6 md:p-10">
                        <x-results-feedback :questions="$questions" :answers="$answers" />
                    </div>
                </div>
            </dialog>
        </div>
        <hr class="my-5" />
        <a href="{{ route('app.lessons.by-course', ['course' => $course->slug]) }}"
            class="link mx-1 flex items-center justify-center mb-3" type="button">
            @if ($direction == 'rtl')
                <i class="icon-[mdi--arrow-right] size-4 me-1 mt-1"></i>
            @else
                <i class="icon-[mdi--arrow-left] size-4 me-1 mt-1"></i>
            @endif
            {{ __('course.backToCourse') }}
        </a>
    </div>

</x-layouts.app>
