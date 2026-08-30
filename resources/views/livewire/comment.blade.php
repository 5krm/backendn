<div>
    @if ($isEditing)
        <div class="p-6 text-base  rounded-lg">
            <x-comment.comment-form method="editComment" state="reply" inputId='reply' inputLabel='Write a comment...'
                button='Edit' />
        </div>
    @else
        <div class="p-6 text-base  rounded-lg    ">

            <div class="flex justify-between items-center mb-2 ">
                <div class="flex items-center">
                    <p class="inline-flex items-center me-3 text-sm text-gray-900  ">
                        <img class="me-2 w-6 h-6 rounded-full"
                            src="{{ $comment->user?->profile ?? asset('assets/images/Logo_Icon.png') }}"
                            alt="Michael Gough">
                        {{ $comment->user?->name ?? 'Admin' }}
                    </p>
                    <p class="text-sm text-gray-600  "><time pubdate datetime="2022-02-08"
                            title="February 8th, 2022">{{ $comment->presenter()->relativeCreatedAt() }}</time></p>
                </div>

                @if (Gate::check(['update', 'destroy'], $comment) && !$readonly)
                    <div class="dropdown dropdown-end">
                        <div tabindex="0" role="button" class="btn btn-sm btn-ghost m-1">
                            <i class="text-2xl icon-[mdi--dots-horizontal]"></i>
                        </div>
                        <ul tabindex="0"
                            class="dropdown-content z-[1] menu p-2 shadow border bg-base-100 rounded-box w-40">
                            <li>
                                <button wire:click="$toggle('isEditing')" type="button"
                                    class="flex items-center w-full py-2 px-4 hover:bg-gray-100">
                                    <i class="text-lg  icon-[mdi--pencil-outline]"></i>
                                    {{ __('base.edit') }}
                                </button>
                            </li>
                            <li>
                                <button @click="$dispatch('deleteComment', {id: {{ $comment->id }}})"
                                    class="flex items-center w-full py-2 px-4 hover:bg-gray-100 ">
                                    <i class="text-lg icon-[mdi--trash-can-outline]"></i>
                                    {{ __('base.delete') }}
                                </button>
                            </li>
                        </ul>
                    </div>
                @endif
            </div>
            <p class="text-gray-500  whitespace-pre-wrap">{{ $comment->content }}</p>
            @if ($readonly && $comment->isParent())
                <div class="mt-3 flex">
                    <i class="icon-[mdi--play-circle-outline] text-primary me-1 size-5"></i>
                    <a href="{{ route('app.lessons.lesson', ['lesson' => $comment->lesson->public_key]) }}"
                        class="font-semibold link text-xs ">
                        {{ $comment->lesson->title }}
                    </a>
                </div>
            @endif
            <div class="flex items-center mt-4 space-x-4">
                @if ($comment->isParent() && !$readonly)
                    <button wire:click="$toggle('isReplying')" type="button"
                        class="flex items-center text-sm text-gray-500 hover:underline me-3 font-medium">
                        <i class="me-1.5 text-base icon-[mdi--comment-text-outline]"></i>
                        {{ __('comment.reply') }}
                    </button>
                @endif
                @if ($comment->children_count)
                    <button type="button" wire:click="$toggle('hasReplies')"
                        class="flex items-center text-sm text-gray-500 hover:underline  ">
                        @if (!$hasReplies)
                            {{ __('comment.viewAll') }} ({{ $comment->children_count }})
                        @else
                            {{ __('comment.hideReplies') }}
                        @endif
                    </button>

                @endif
            </div>
        </div>
    @endif
    @if ($isReplying)
        <div class="p-6 text-base  rounded-lg">
            <x-comment.comment-form method="postReply" state="reply" inputId='reply-comment'
                inputLabel='Write Your Reply...' button='Post Reply' />
        </div>
    @endif
    @if ($hasReplies)

        <div class="p-1 mb-1 ml-1 lg:ml-12 border-t border-gray-200  ">
            @foreach ($comment->children as $child)
                <livewire:comment :comment="$child" key="{{ $child->id }}.{{ $loop->index }}" :$readonly />
            @endforeach
        </div>
    @endif

</div>
</script>
