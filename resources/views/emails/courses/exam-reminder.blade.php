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
{{ __('emails.course_exam_reminder.title') }}
</h1>

<div style="direction:{{$direction}};font-size:14px;">
{{ __('emails.course_exam_reminder.greeting', ['name' => $user->name]) }}
{{ __('emails.course_exam_reminder.message', ['course' => $course->title]) }}
{{ __('emails.course_exam_reminder.reminder') }}
</div>

<x-mail::button :url="route('app.courses.exam', $course)">
{{ __('emails.course_exam_reminder.button') }}
</x-mail::button>

<div style="direction:{{$direction}};font-size:14px;">
{{ __('emails.course_exam_reminder.closing') }}<br>
{{ config('app.name') }}
</div>

@slot('footer')
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}
<br>
{!! __('emails.course_exam_reminder.unsubscribe', ['link' => $unsubscribe_link]) !!}
</x-mail::footer>
@endslot
</x-mail::layout>
