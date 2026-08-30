@if ($banner)
    @php
        $isImageMode = ($displayType ?? null) === \App\Enums\PromotionDisplayType::Image;
        $hasPreviewContent = $isImageMode
            ? filled($bannerImagePreviewUrl ?? null)
            : true;
    @endphp

    @if ($isImageMode && ! $hasPreviewContent)
        <div class="w-full overflow-hidden rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400" style="aspect-ratio: 4 / 1;">
            <div class="flex h-full items-center justify-center">
                {{ __('tutor.promotions.preview_image_empty') }}
            </div>
        </div>
    @else
        @php
            $frameHtml = view('promotions.preview-frame', ['banner' => $banner])->render();
        @endphp

        <div
            wire:key="promotion-banner-preview-{{ md5($frameHtml) }}"
            x-data="{ html: @js($frameHtml) }"
            x-init="$refs.previewFrame.srcdoc = html"
            class="w-full overflow-hidden rounded-2xl"
            style="aspect-ratio: 4 / 1;"
        >
            <iframe
                x-ref="previewFrame"
                title="{{ __('tutor.promotions.preview') }}"
                scrolling="no"
                style="display: block; width: 100%; height: 100%; border: 0; background: #f3f4f6;"
                loading="lazy"
            ></iframe>
        </div>
    @endif
@else
    <div class="w-full overflow-hidden rounded-2xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-sm text-gray-500 dark:border-gray-600 dark:bg-gray-900 dark:text-gray-400" style="aspect-ratio: 4 / 1;">
        <div class="flex h-full items-center justify-center">
            {{ __('tutor.promotions.preview_empty') }}
        </div>
    </div>
@endif
