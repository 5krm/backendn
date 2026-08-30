<x-mail::layout>
# @lang('emails.new_reply.title')

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
@lang('emails.warm_greeting', ['name' => $user->name]),</p> <br/> 
    
<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
    @lang('emails.new_reply.inform', ['colleague' => $reply->user->name, 'lesson' => $reply->lesson->title, 'comment' => substr(strip_tags($reply->parent->content), 0, 50) . '...'])</p>
    <br/>

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">@lang('emails.new_comment.to_view')</p>

<x-mail::button :url="route('app.lessons.lesson', ['lesson' => $reply->lesson->public_key])">
    @lang('emails.view', ['object' => trans('emails.reply')])
</x-mail::button>

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
@lang('emails.new_reply.footer') <br /> @lang('emails.new_reply.keepit') <br/><br/> @lang('emails.regards')
</p>

<br/>

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
@lang('emails.team', ['app' => config('app.name')])
</p>
    
@slot('footer')
<x-mail::footer>
    © {{ date('Y') }} {{ config('app.name') }}
        <br>
        @lang('emails.unsubscribe', ['link' => $unsubscribe_link])
</x-mail::footer>
@endslot
</x-mail::layout>
