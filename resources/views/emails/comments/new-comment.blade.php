@component('mail::layout')

# @lang('emails.new_comment.title')

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
@lang('emails.warm_greeting', ['name' => $user->name]), <br/><br/> @lang('emails.new_comment.inform', ['student' => $comment->user->name, 'lesson' => $comment->lesson->title])</p>
<br />

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
@lang('emails.new_comment.to_view')
</p>

<x-mail::button :url="route('app.lessons.lesson', ['lesson' => $comment->lesson->public_key])">
    @lang('emails.view', ['object' => trans('emails.comment')])
</x-mail::button>

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
    @lang('emails.new_comment.footer') <br /> @lang('emails.new_comment.keepit') <br/><br/> @lang('emails.regards')
</p>

<br/>

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
@lang('emails.team', ['app' => config('app.name')])
</p>

@slot('footer')
    @component('mail::footer')
    © {{ date('Y') }} {{ config('app.name') }}
        <br>
        @lang('emails.unsubscribe', ['link' => $unsubscribe_link])
    @endcomponent
@endslot

@endcomponent
