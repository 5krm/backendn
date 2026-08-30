@php
    $preview = $preview ?? false;
    $isRtl = isset($direction) ? $direction === 'rtl' : app()->getLocale() === 'ar';
    $storageKey = $banner->storageKey();
    $frameClass = 'relative aspect-[4/1] w-full overflow-hidden rounded-2xl bg-gradient-to-r from-[#00cc99] to-[#00b386] shadow-2xl';
    $wrapperClass = $preview ? 'w-full' : 'w-full animate-slideInDown';
@endphp

@if ($preview)
<div {{ $attributes->class($wrapperClass) }}>
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
@endif
    <div class="{{ $frameClass }}">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" viewBox="0 0 1200 300" aria-hidden="true">
                <defs>
                    <pattern id="promo-dot-pattern" x="0" y="0" width="100" height="100"
                        patternUnits="userSpaceOnUse">
                        <circle cx="50" cy="50" r="30" fill="white" opacity="0.1"></circle>
                    </pattern>
                </defs>
                <rect width="1200" height="300" fill="url(#promo-dot-pattern)"></rect>
            </svg>
        </div>

        <div
            class="relative z-10 flex h-full flex-col items-start justify-center gap-4 px-5 py-4 md:flex-row md:items-center md:gap-6 md:px-10 md:py-6">
            <div class="min-w-0 flex-1">
                <div class="mb-3 flex flex-wrap items-center gap-2">
                    <span
                        class="inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-white backdrop-blur-sm">
                        {{ __('promotions.limited_offer') }}
                    </span>
                    <span class="text-xs font-medium text-white/85">
                        {{ trans_choice('course.sale_ends_in', $banner->daysRemaining()) }}
                    </span>
                </div>

                <h2 class="mb-2 text-3xl font-bold leading-tight text-white md:text-4xl lg:text-5xl">
                    {{ $banner->discountPercent() }}% {{ __('course.off') }} — {{ $banner->headline() }}
                </h2>

                <p class="text-base font-medium text-white/90 md:text-lg">
                    {{ $banner->subheadline() }}
                </p>

                @if ($banner->promoCode() || $banner->coursesCount() > 0)
                    <div class="mt-4 flex flex-wrap items-center gap-3 text-sm text-white/85">
                        @if ($banner->coursesCount() > 0)
                            <span>{{ trans_choice('promotions.courses_included', $banner->coursesCount()) }}</span>
                        @endif
                        @if ($banner->promoCode())
                            <span class="rounded-md bg-white/15 px-2.5 py-1 font-mono text-xs tracking-wide">
                                {{ __('promotions.code_label') }}: <strong>{{ $banner->promoCode() }}</strong>
                            </span>
                        @endif
                    </div>
                @endif
            </div>

            <div class="z-10 flex-shrink-0">
                <a href="{{ $banner->coursesUrl() }}"
                    class="animate-pulse-glow inline-flex whitespace-nowrap rounded-full bg-white px-8 py-4 text-lg font-bold text-secondary shadow-lg transition-all duration-300 hover:scale-105 hover:bg-gray-50 active:scale-95">
                    {{ __('promotions.claim_offer') }}
                </a>
            </div>

            @unless ($preview)
            <button type="button" @click="dismiss()"
                class="absolute top-4 z-20 rounded-full bg-white/20 p-2 backdrop-blur-sm transition-colors hover:bg-white/30 {{ $isRtl ? 'left-4 md:left-5' : 'right-4 md:right-5' }}"
                aria-label="{{ __('promotions.close_banner') }}">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                    stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"
                    class="h-5 w-5 text-white md:h-6 md:w-6" aria-hidden="true">
                    <path d="M18 6 6 18"></path>
                    <path d="m6 6 12 12"></path>
                </svg>
            </button>
            @endunless
        </div>

        <div class="pointer-events-none absolute inset-0 rounded-2xl"
            style="box-shadow: inset 0 1px 0 0 rgba(255, 255, 255, 0.1), inset 0 -1px 0 0 rgba(0, 0, 0, 0.1)"></div>
    </div>
</div>
