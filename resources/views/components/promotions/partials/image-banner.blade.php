@php
    $preview = $preview ?? false;
    $isRtl = isset($direction) ? $direction === 'rtl' : app()->getLocale() === 'ar';
    $storageKey = $banner->storageKey();
    $frameClass = 'relative aspect-[4/1] w-full overflow-hidden rounded-2xl shadow-xl';
    $wrapperClass = $preview ? 'w-full' : 'w-full animate-slideInDown';
@endphp

@if ($preview)
    <div {{ $attributes->class($wrapperClass) }}>
        <div class="{{ $frameClass }}">
            <img
                src="{{ $banner->promotion->banner_image_url }}"
                alt="{{ $banner->headline() }}"
                class="h-full w-full object-cover"
            >
        </div>
    </div>
@else
<div
    {{ $attributes->class($wrapperClass) }}
    x-data="{
        open: true,
        key: @js($storageKey),
        init() {
            this.open = localStorage.getItem(this.key) !== '1';
        },
        dismiss() {
            this.open = false;
            localStorage.setItem(this.key, '1');
        }
    }"
    x-show="open"
    x-cloak
    role="region"
    aria-label="{{ __('promotions.banner_aria') }}"
>
    <div class="{{ $frameClass }}">
        <a href="{{ $banner->coursesUrl() }}" class="block h-full w-full">
            <img
                src="{{ $banner->promotion->banner_image_url }}"
                alt="{{ $banner->headline() }}"
                class="h-full w-full object-cover"
            >
        </a>

        <button type="button" @click.prevent="dismiss()"
            class="absolute top-3 z-30 rounded-full bg-black/40 p-2 text-white/90 backdrop-blur-sm transition hover:bg-black/55 {{ $isRtl ? 'left-3' : 'right-3' }}"
            aria-label="{{ __('promotions.close_banner') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                aria-hidden="true">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
            </svg>
        </button>
    </div>
</div>
@endif
