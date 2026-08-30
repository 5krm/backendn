@props(['banner', 'preview' => false])

@if ($banner)
@php
    $promotion = $banner->promotion;
    $displayType = $promotion->display_type instanceof \App\Enums\PromotionDisplayType
        ? $promotion->display_type
        : \App\Enums\PromotionDisplayType::tryFrom((string) $promotion->display_type);
@endphp

@if ($displayType === \App\Enums\PromotionDisplayType::Image && $promotion->banner_image_url)
    @include('components.promotions.partials.image-banner', ['banner' => $banner, 'preview' => $preview])
@elseif ($displayType === \App\Enums\PromotionDisplayType::Template)
    @php
        $templateView = \App\Support\PromotionTemplateRegistry::view(
            $promotion->template ?? \App\Support\PromotionTemplateRegistry::defaultTemplate()
        );
    @endphp

    @if ($templateView)
        @include($templateView, ['banner' => $banner, 'preview' => $preview])
    @endif
@endif
@endif
