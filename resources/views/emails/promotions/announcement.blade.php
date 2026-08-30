<x-mail::layout>
<x-slot:head>
<style>
body, .wrapper, .header, .footer, .content-cell {
direction: {{ $direction }} !important;
text-align: {{ $direction == 'ltr' ? 'left' : 'right' }} !important;
}
.button, .button *, .inner-body, .footer {
text-align: center !important;
}
</style>
</x-slot:head>

<h1 style="font-size: 24px; font-weight: bold; margin-bottom: 20px; text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};">
{{ __('emails.promotion_announcement.title') }}
</h1>

<div style="direction:{{$direction}};font-size:14px;">
{{ __('emails.promotion_announcement.greeting', ['name' => $user->name]) }}
<br/>
{{ __('emails.promotion_announcement.message', [
    'title' => $promotion->title,
    'percent' => $promotion->discount_percent,
]) }}
</div>
<br />

@if ($promotion->description)
<div style="direction:{{$direction}};font-size:14px; color: #4b5563; margin-bottom: 16px;">
{{ $promotion->description }}
</div>
@endif

<x-mail::button :url="route('promotions.show', $promotion)">
{{ __('emails.promotion_announcement.button') }}
</x-mail::button>

<div style="direction:{{$direction}};font-size:14px;">
{{ __('emails.promotion_announcement.closing') }}<br>
{{ config('app.name') }}
</div>

@slot('footer')
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}
<br>
{!! __('emails.promotion_announcement.unsubscribe', ['link' => $unsubscribe_link]) !!}
</x-mail::footer>
@endslot
</x-mail::layout>
