@php
    $preview = $preview ?? false;
    $isRtl = isset($direction) ? $direction === 'rtl' : app()->getLocale() === 'ar';
    $storageKey = $banner->storageKey();
    $frameClass = 'relative aspect-[4/1] w-full overflow-hidden rounded-2xl shadow-xl';
    $wrapperClass = $preview ? 'w-full font-sans' : 'w-full animate-slideInDown font-sans';
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
    <div class="{{ $frameClass }}" style="background: #dce8ef;">

        {{-- Soft gray wave curves --}}
        <svg class="absolute inset-0 h-full w-full" viewBox="0 0 800 400" preserveAspectRatio="xMidYMid slice"
            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M0 180 Q200 120 400 200 Q600 280 800 160 L800 0 L0 0 Z" fill="rgba(255,255,255,0.35)" />
            <path d="M0 220 Q180 160 380 240 Q580 310 800 200 L800 0 L0 0 Z" fill="rgba(255,255,255,0.18)" />
        </svg>

        {{-- Bottom wave --}}
        <svg class="absolute bottom-0 start-0 w-full" viewBox="0 0 800 80" preserveAspectRatio="none"
            xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
            <path d="M0 45 Q200 10 400 40 Q600 70 800 20 L800 80 L0 80 Z" fill="#0d826f" />
            <path d="M0 58 Q200 22 400 52 Q600 80 800 35 L800 80 L0 80 Z" fill="#111827" />
        </svg>

        {{-- Dot grid decorations --}}
        <div class="absolute top-6 start-60 grid grid-cols-4 gap-1 opacity-40" aria-hidden="true">
            @for ($i = 0; $i < 12; $i++)
                <div class="h-1 w-1 rounded-full bg-gray-400"></div>
            @endfor
        </div>
        <div class="absolute top-4 end-40 grid grid-cols-4 gap-1 opacity-30" aria-hidden="true">
            @for ($i = 0; $i < 16; $i++)
                <div class="h-1 w-1 rounded-full bg-gray-500"></div>
            @endfor
        </div>

        {{-- Main content --}}
        <div class="relative z-10 flex h-full flex-col md:flex-row md:items-center">
            {{-- Left column --}}
            <div class="flex min-w-0 flex-1 flex-col justify-center gap-6 p-6 pe-4 md:p-8 md:pe-6">
                <div class="min-w-0">
                    <div class="mb-3 flex flex-wrap items-center gap-2">
                        <span
                            class="inline-flex items-center rounded-full bg-primary/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-primary">
                            {{ __('promotions.limited_offer') }}
                        </span>
                        <span class="text-xs font-medium text-gray-500">
                            {{ trans_choice('course.sale_ends_in', $banner->daysRemaining()) }}
                        </span>
                    </div>

                    <h2 class="mb-2 text-3xl font-bold leading-tight text-gray-900 md:text-4xl lg:text-5xl">
                        <span class="text-primary">{{ $banner->discountPercent() }}% {{ __('course.off') }}</span>
                        — {{ $banner->headline() }}
                    </h2>

                    <p class="max-w-xl text-base font-medium text-gray-600 md:text-lg">
                        {{ $banner->subheadline() }}
                    </p>

                    @if ($banner->coursesCount() > 0)
                        <div class="mt-4 text-sm text-gray-600">
                            <span>{{ trans_choice('promotions.courses_included', $banner->coursesCount()) }}</span>
                        </div>
                    @endif
                </div>

                <div class="flex-shrink-0">
                    <a href="{{ $banner->coursesUrl() }}"
                        class="animate-pulse-glow inline-flex whitespace-nowrap rounded-full bg-primary px-8 py-4 text-lg font-bold text-white shadow-lg transition-all duration-300 hover:scale-105 hover:brightness-110 active:scale-95">
                        {{ __('promotions.claim_offer') }}
                    </a>
                </div>
            </div>

            {{-- Right column --}}
            <div class="relative mx-auto flex w-full max-w-[320px] items-center justify-center pb-10 md:me-16 md:pb-0">
                <div
                    class="relative -mt-5 h-[300px] w-[300px] overflow-hidden rounded-full border-4 border-white shadow-xl">
                    <img src="{{ $banner->imageUrl() }}" alt="" class="h-full w-full object-cover" />
                </div>

                {{-- Starburst discount badge --}}
                <div
                    class="absolute bottom-6 -start-4 flex h-24 w-24 items-center justify-center bg-primary text-center text-white shadow-lg sm:-start-12 sm:h-28 sm:w-28"
                    style="clip-path: polygon(50% 0%, 63% 12%, 79% 6%, 82% 22%, 97% 28%, 90% 42%, 100% 50%, 90% 58%, 97% 72%, 82% 78%, 79% 94%, 63% 88%, 50% 100%, 37% 88%, 21% 94%, 18% 78%, 3% 72%, 10% 58%, 0% 50%, 10% 42%, 3% 28%, 18% 22%, 21% 6%, 37% 12%);">
                    <div class="leading-none">
                        <div class="text-2xl font-black sm:text-3xl">-{{ $banner->discountPercent() }}%</div>
                    </div>
                </div>
            </div>
        </div>

        @unless ($preview)
        <button type="button" @click="dismiss()"
            class="absolute top-3 z-30 rounded-full p-2 text-gray-400 transition hover:bg-white/70 hover:text-gray-700 {{ $isRtl ? 'left-3' : 'right-3' }}"
            aria-label="{{ __('promotions.close_banner') }}">
            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none"
                stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"
                aria-hidden="true">
                <path d="M18 6 6 18"></path>
                <path d="m6 6 12 12"></path>
            </svg>
        </button>
        @endunless
    </div>
</div>
