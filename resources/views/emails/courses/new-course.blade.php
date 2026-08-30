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
{{ __('emails.new_course.title') }}
</h1>

<div style="direction:{{$direction}};font-size:14px;">
{{ __('emails.new_course.greeting', ['name' => $user->name]) }}
{{ __('emails.new_course.message', ['course' => $course->title]) }}
@if ($course->organization)
{{ __('emails.new_course.by') }} <a style="color:#00cc99;" href="{{ route('organization.index', $course->organization->slug) }}">{{ $course->organization->name }}</a>
@endif.
</div>
<br />

<x-mail::button :url="route('courses.details', $course->slug)">
{{ __('emails.new_course.button') }}
</x-mail::button>

<div style="direction:{{$direction}};font-size:14px;">
{{ __('emails.new_course.closing') }}<br>
{{ config('app.name') }}
</div>

@slot('footer')
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}
<br>
{!! __('emails.new_course.unsubscribe', ['link' => $unsubscribe_link]) !!}
</x-mail::footer>
@endslot
</x-mail::layout>