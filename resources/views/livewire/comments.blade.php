<div>
    @if ($this->comments->count())
        @foreach ($this->comments as $comment)
            <div class=" my-6  border border-gray-200 rounded-lg">
                <livewire:comment :comment="$comment" :key="$comment->id" />
            </div>
        @endforeach
    @else
        <div class="mb-6 text-center  ">
            <i class="size-10 icon-[mdi--comment-text-multiple-outline] text-[#aaa]"></i>
            <p class="font-bold text-[#aaa] mt-3">
                {{ __('comment.noComments') }}
            </p>
        </div>
    @endif

    <div class="my-4">
        <x-comment.comment-form method="postComment" state="content" inputId='comment' inputLabel='Write a comment...' />
    </div>

    <dialog id="confirm_modal" class="modal modal-scroll modal-sm">
        <div class="modal-box text-center p-5">
            <div class="avatar rounded-full p-2 bg-red-50">
                <i class="icon-[mdi--trash-can-outline] text-red-500 size-8"></i>
            </div>
            <h1 class="py-3 font-bold text-lg blink_me ">{{ __('base.yousure') }}</h1>
            <p class="py-3  text-md blink_me text-[grey] ">{{ __('base.yousuremsg') }} </p>

            <div class="flex justify-center my-5">
                <div class="loader"></div>
            </div>
            <form method='dialog'>
                <button id="popup-close"
                    class="btn-grey btn btn-sm  focus:outline-none mt-3">{{ __('base.cancel') }}</button>
                <a @click="remove" class="btn-error btn btn-sm  focus:outline-none mt-3">{{ __('base.yes') }}</a>
            </form>
        </div>
    </dialog>
</div>

<script>
    let commentId = 0;
    document.addEventListener('deleteComment', (data) => {
        commentId = data.detail.id
        console.log("🚀 ~ document.addEventListener ~ commentId:", commentId)
        confirm_modal.showModal();

    });

    function remove() {
        @this.deleteComment(commentId)
        var closer = document.querySelector('#confirm_modal #popup-close');
        closer.click();
    }
</script>
