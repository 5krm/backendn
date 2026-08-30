<x-mail::layout>
# {{$mail->subject}}

<div dir="{{ $direction }}" style="text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};">
    {!! $content !!}
</div>

@slot('footer')
<x-mail::footer>
    © {{ date('Y') }} {{ config('app.name') }}
    <br>
    {!! __('emails.unsubscribe', ['link' => $unsubscribe_link]) !!}
</x-mail::footer>
@endslot
</x-mail::layout>
