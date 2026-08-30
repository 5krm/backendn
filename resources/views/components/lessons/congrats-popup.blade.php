<dialog id="exam_modal" class="modal">
    <div class=" modal-box  max-w-max p-10 rounded-2xl">
        <img src="{{ asset('/assets/svg/celebration.svg') }}" class="rounded-2xl" alt="Congrats">
        <div class="grid gap-5 mt-5 px-3 lg:text-lg text-base text-gray-700">

            <h1 class="text-3xl font-bold text-secondary">{{ __('course.congrats_popup.title') }}</h1>
            <div>
                <p>{!! __('course.congrats_popup.subtitle', [
                    'course' => $course['data']->title,
                ]) !!} </p>
                <p>{{ __('course.congrats_popup.content') }}</p>
                <p class="text-gray-500 text-sm mt-3">{{ __('course.congrats_popup.note') }}</p>
            </div>
            <div class="flex justify-end">
                <a href="/app/courses/{{ $course['data']->slug }}/exam/info" class="btn px-5 btn-md mx-1 btn-primary"
                    type="button">{{ __('exam.takeit') }}</a>

                <form method="dialog">
                    <button class="btn btn-ghost btn-md">{{ __('course.congrats_popup.cancel_btn') }}</button>
                </form>
            </div>
        </div>
    </div>
</dialog>
<script>
        window.addEventListener('congratulate-user', (e) => {
        var closer = document.querySelector('.lesson #my_modal #dialog-close')
        closer.click();
        exam_modal.showModal()
    });
</script>
