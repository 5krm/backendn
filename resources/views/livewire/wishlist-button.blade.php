<button
    type="button"
    wire:click="toggle"
    wire:loading.attr="disabled"
    class="btn   btn-sm shadow tooltip rtl:tooltip-right ltr:tooltip-left"
    data-tip="{{ $isWishlisted ? __('course.in_wishlist') : __('course.add_to_wishlist') }}"
>
    @if ($isWishlisted)
        <span class="icon-[mdi--heart] w-5 h-5 text-primary" wire:loading.remove wire:target="toggle"></span>
    @else
        <span class="icon-[mdi--heart-outline] w-5 h-5" wire:loading.remove wire:target="toggle"></span>
    @endif
    <span class="loading loading-spinner loading-xs" wire:loading wire:target="toggle"></span>
</button>
