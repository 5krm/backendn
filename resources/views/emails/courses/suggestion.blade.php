<x-mail::layout> 
<x-slot:head>
<style>
body, .wrapper, .header, .footer, .content-cell, .image-content {
direction: {{ $direction }} !important;
text-align: {{ $direction == 'ltr' ? 'left' : 'right' }} !important;
}
.button, .button *, .inner-body, .footer {
text-align: center !important;
}
</style>
</x-slot:head>

<h1 style="font-size: 24px; font-weight: bold; margin-bottom: 20px; text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};">
{{ __('emails.course_suggestion.title') }}
</h1>  

<div style="direction:{{$direction}};font-size:16px;">
{{ __('emails.course_suggestion.greeting', ['name' => $user->name]) }}<br/>
{{ __('emails.course_suggestion.message') }}

<table width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 20px 0; border: 1px solid #e5e7eb; border-radius: 12px; overflow: hidden; background: #ffffff;">
  <tr>
    <td style="padding: 0;">
      <img src="{{ $course->coverImage }}" width="100%" style="display: block; width: 100%; height: 180px; object-fit: cover;" alt="{{ $course->title }}">
    </td>
  </tr>
  <tr>
    <td style="padding: 16px 20px 20px;" class="image-content">
      <p style="text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};direction:{{$direction}};font-size: 17px; font-weight: 700; color: #111827; margin: 0 0 8px 0; line-height: 1.4;">{{ $course->title }}</p>
      <p style="text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};direction:{{$direction}};font-size: 13px; color: #4b5563; margin: 0 0 16px 0; line-height: 1.6;">{{ Str::limit($course->description, 160) }}</p>
      <a href="{{ route('courses.details', $course->slug) }}" style="display: inline-block; background: #00cc99; color: #ffffff; text-decoration: none; padding: 9px 22px; border-radius: 7px; font-size: 13px; font-weight: 600;">{{ __('emails.course_suggestion.button') }}</a>
    </td>
  </tr>
</table>
</div>
<br/>
<div>
<p style="direction:{{$direction}};font-size:16px;text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};">{{ __('emails.course_suggestion.support') }}</p>
<p style="direction:{{$direction}};font-size:16px;text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};">{{ __('emails.course_suggestion.closing') }}</p><br/>
<p style="direction:{{$direction}};font-size:16px;text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};">{{ config('app.name') }}</p>
</div>
@slot('footer')
<x-mail::footer>
© {{ date('Y') }} {{ config('app.name') }}
    <br>
    {!! __('emails.course_suggestion.unsubscribe', ['link' => $unsubscribe_link]) !!}
</x-mail::footer>
@endslot
</x-mail::layout>
