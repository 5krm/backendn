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
.rating-prompt {
background-color: #f8fafc;
border: 1px solid #e2e8f0;
border-radius: 8px;
padding: 16px;
margin: 20px 0;
text-align: center;
}
</style>
</x-slot:head>

<h1 style="font-size: 24px; font-weight: bold; margin-bottom: 20px; direction:{{$direction}}; text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};">
{{ __('emails.course_rating_reminder.title') }}
</h1>

<div style="direction:{{$direction}};font-size:14px; line-height: 1.6;">
{{ __('emails.course_rating_reminder.greeting', ['name' => $user->name]) }}<br><br>
{{ __('emails.course_rating_reminder.message', ['course' => $course->title]) }}
</div>

<div class="rating-prompt">
<p style="margin: 11px 0 12px 0; font-size: 15px; font-weight: 600; {{ $direction == 'ltr' ? 'text-align:left;direction:ltr' : 'text-align:right;direction:rtl' }};">
{{ __('emails.course_rating_reminder.prompt') }}
</p>

<x-mail::button :url="route('app.courses.rate', $course)">
{{ __('emails.course_rating_reminder.button') }}
</x-mail::button>
</div>

<div style="direction:{{$direction}};font-size:14px; line-height: 1.6;">
{{ __('emails.course_rating_reminder.closing') }}<br>
<strong>{{ config('app.name') }}</strong>
</div>

@slot('footer')
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}. {{ __('emails.all_rights_reserved') }}
<br>
{!! __('emails.course_rating_reminder.unsubscribe', ['link' => $unsubscribe_link]) !!}
</x-mail::footer>
@endslot
</x-mail::layout>