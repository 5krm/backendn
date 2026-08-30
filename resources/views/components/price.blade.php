@props(['price', 'promo'])

@if ($promo)
    <span>
        {{ Number::currency($promo['price']) }}
    </span>
@else
    <span>{{  Number::currency($price) }}</span>
@endif
