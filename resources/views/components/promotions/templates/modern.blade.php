@php
    $preview = $preview ?? false;
    $isRtl = isset($direction) ? $direction === 'rtl' : app()->getLocale() === 'ar';
    $badgeBg = 'https://hebbkx1anhila5yf.public.blob.vercel-storage.com/image-yx6Yr79IjavGWM40VCHFE95xrlE0Jj.png';
    $fireworkLeft = asset('assets/images/promotions/image-left.png');
    $fireworkRight = asset('assets/images/promotions/image-right.png');
    $storageKey = $banner->storageKey();
    $frameClass = 'relative aspect-[4/1] w-full overflow-hidden rounded-2xl bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950 text-white shadow-2xl';
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
    <div class="{{ $frameClass }}">
        <section class="relative flex h-full w-full items-center justify-center px-4 py-4 md:py-6">
            <div class="absolute inset-0 bg-[#07090d]"></div>
            <div class="absolute -left-[8%] -top-[44%] h-[95%] w-[47%] rounded-full bg-[#173343]/70 blur-3xl"></div>
            <div class="absolute -right-[7%] -top-[30%] h-[110%] w-[45%] rounded-full bg-[#65101e]/55 blur-3xl"></div>
            <div class="absolute bottom-[-80%] left-[29%] h-[105%] w-[42%] rounded-full bg-[#0c4768]/60 blur-3xl">
            </div>
            <div
                class="absolute left-[24%] top-[17%] h-[0.35vw] w-[0.35vw] rounded-full bg-white/70 shadow-[5vw_8vw_0_rgba(255,255,255,0.45),20vw_2vw_0_rgba(255,255,255,0.3),36vw_6vw_0_rgba(255,255,255,0.4),50vw_0_0_rgba(255,255,255,0.35),62vw_10vw_0_rgba(255,255,255,0.45)]">
            </div>

            {{-- Decorative fireworks - left --}}
            <div class="pointer-events-none absolute inset-y-0 start-0  rtl:end-0 hidden md:block lg:w-62" aria-hidden="true">
                <img src="{{ $fireworkLeft }}" alt="" class="h-full w-full object-contain object-left" />
            </div>

            {{-- Decorative fireworks - right --}}
            <div class="pointer-events-none absolute inset-y-0 end-0 rtl:start-0 hidden md:block lg:w-62" aria-hidden="true">
                <img src="{{ $fireworkRight }}" alt="" class="h-full w-full object-contain object-right" />
            </div>

            {{-- Content --}}
            <div class="relative z-10 mx-auto max-w-5xl text-center">
                <div class="my-2 flex flex-col items-center justify-center gap-4 md:flex-row md:gap-8">
                    {{-- Left badge --}}
                    <div class="relative flex h-28 w-28 flex-shrink-0 items-center justify-center md:h-40 md:w-40">
                        <img src="{{ $badgeBg }}" alt="" class="h-full w-full object-contain" width="160"
                            height="160" aria-hidden="true" />
                        <div class="absolute text-center">
                            <div class="text-2xl font-black text-slate-900 md:text-4xl">
                                {{ $banner->discountPercent() }}%</div>
                            <div class="text-xs font-black leading-tight text-slate-900 md:text-4xl">
                                {{ __('course.off') }}
                            </div>
                        </div>
                    </div>

                    {{-- Main title --}}
                    <div class="min-w-0 flex-1 px-2">
                        <h2
                            class="my-2 text-4xl font-black leading-tight text-white drop-shadow-2xl md:text-4xl lg:text-6xl">
                            {{ $banner->headline() }}
                        </h2>
                        <p class="mb-2 px-4 text-base font-bold text-white text-balance md:mb-4 md:text-xl lg:text-2xl">
                            {{ $banner->subheadline() }}
                        </p>
                        @if ($banner->promoCode() || $banner->coursesCount() > 0)
                            <div
                                class="mb-6 flex flex-wrap items-center justify-center gap-3 text-sm text-yellow-100/90 md:mb-8">
                                @if ($banner->coursesCount() > 0)
                                    <span class="rounded-md bg-white/10 px-2.5 py-1 font-mono text-xs tracking-wide text-yellow-200">{{ trans_choice('promotions.courses_included', $banner->coursesCount()) }}</span>
                                @endif
                                @if ($banner->promoCode())
                                    <span
                                        class="rounded-md bg-white/10 px-2.5 py-1 font-mono text-xs tracking-wide text-yellow-200">
                                        {{ __('promotions.code_label') }}:
                                        <strong>{{ $banner->promoCode() }}</strong>
                                    </span>
                                @endif
                                <span class="rounded-md bg-white/10 px-2.5 py-1 font-mono text-xs tracking-wide text-yellow-200">
                                    {{ trans_choice('course.sale_ends_in', $banner->daysRemaining()) }}
                                </span>
                            </div>
                        @endif

                        <a href="{{ $banner->coursesUrl() }}"
                            class="mb-2 inline-flex rounded-full bg-yellow-200 px-10 py-4 text-lg font-black text-slate-900 shadow-2xl transition-all duration-300 hover:scale-105 hover:brightness-110 active:scale-95 md:px-8 md:py-2 md:text-xl">
                            {{ __('promotions.claim_offer') }}
                        </a>
                    </div>

                    {{-- Right badge --}}
                    <div class="relative flex h-28 w-28 flex-shrink-0 items-center justify-center md:h-40 md:w-40">
                        <img src="{{ $badgeBg }}" alt="" class="h-full w-full object-contain" width="160"
                            height="160" aria-hidden="true" />
                        <div class="absolute text-center">
                            <div class="text-2xl font-black text-slate-900 md:text-4xl">
                                {{ $banner->discountPercent() }}%</div>
                            <div class="text-xs font-black leading-tight text-slate-900 md:text-4xl">
                                {{ __('course.off') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        @unless ($preview)
        <button type="button" @click="dismiss()"
            class="absolute top-3 z-30 rounded-full bg-white/10 p-2 text-yellow-200/80 backdrop-blur-sm transition hover:bg-white/20 hover:text-yellow-100 {{ $isRtl ? 'left-3' : 'right-3' }}"
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
