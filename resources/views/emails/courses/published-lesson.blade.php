@component('mail::message')

<h1 style="direction: {{ $direction }};font-size: 24px; font-weight: bold; margin-bottom: 20px; text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};">
{{ __('emails.published_lesson.title') }}
</h1>
<div style="text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};direction:{{$direction}};font-size:16px;" >
{{ __('emails.published_lesson.greeting', ['name' => $user->name]) }}
{!! __('emails.published_lesson.message', ['course' => '<a href="'.route('courses.details', $lesson->course->slug).'"><b>'.$lesson->course->title.'</b></a>']) !!}
{{ __('emails.published_lesson.whats_new') }}<br/>

> <p style="text-align:{{$direction == 'ltr' ? 'left' : 'right' }}">{!! __('emails.published_lesson.lesson_title', ['lesson' => '<b>'.$lesson->title.'</b>']) !!}</p>
> <p style="text-align:{{$direction == 'ltr' ? 'left' : 'right' }}">{{ __('emails.published_lesson.period', ['period' => $lesson->textDuration]) }} </p>
> <p style="text-align:{{$direction == 'ltr' ? 'left' : 'right' }}">{{ __('emails.published_lesson.access', ['chapter' => $lesson->courseSection->title]) }} </p>

<p style="text-align:{{$direction == 'ltr' ? 'left' : 'right' }}">{{ __('emails.published_lesson.closing') }}</p>
</div>
@component('mail::button', ['url' => route('app.lessons.lesson', ['lesson' => $lesson->public_key]), 'color' => 'blue'])
<span>{{ __('emails.published_lesson.button') }}</span>
@endcomponent

<div  style="text-align: {{ $direction == 'ltr' ? 'left' : 'right' }};direction:{{$direction}};font-size:16px;" >    
{{ __('emails.published_lesson.support') }}<br/>
{{ __('emails.published_lesson.happy_learning') }}<br/><br/>
{{ __('emails.published_lesson.regards') }}<br>
{{ config('app.name') }}
</div>
@slot('footer')
<x-mail::footer>
    © {{ date('Y') }} {{ config('app.name') }}
        <br>
        {!! __('emails.published_lesson.unsubscribe', ['link' => $unsubscribe_link]) !!}
</x-mail::footer>
@endslot
@endcomponent