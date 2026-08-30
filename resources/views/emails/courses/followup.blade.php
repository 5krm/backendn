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
{{ __('emails.course_followup.title') }}
</h1>



<div style="direction:{{$direction}};font-size:14px;">
{{ __('emails.course_followup.greeting', ['name' => $user->name]) }}
{{ __('emails.course_followup.message', ['course' => $course->title]) }}<br/>
{{ __('emails.course_followup.next_lesson', ['lesson' => $lesson->title]) }}
</div>

<x-mail::button :url="route('app.lessons.lesson', $lesson)">
{{ __('emails.course_followup.button') }}
</x-mail::button>

<div style="direction:{{$direction}};font-size:14px;">
{{ __('emails.course_followup.closing') }}<br>
{{ config('app.name') }}
</div>
@slot('footer')
<x-mail::footer>
    © {{ date('Y') }} {{ config('app.name') }}
        <br>
        {!! __('emails.course_followup.unsubscribe', ['link' => $unsubscribe_link]) !!}
</x-mail::footer>
@endslot
</x-mail::layout>
