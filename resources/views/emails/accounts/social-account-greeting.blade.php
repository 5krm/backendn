<x-mail::message>
# @lang('emails.social_account.title')

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
    @lang('emails.warm_greeting', ['name' => $user->name]), <br/><br/> @lang('emails.social_account.greeting')<br/> @lang('emails.social_account.inform')
</p><br />

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
    @lang('emails.social_account.to_expect')</p>
    
> <p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">@foreach(Lang::get('emails')['social_account']['expectations'] as $expect)  ● {{$expect}} <br/> @endforeach</p>

<br/>
<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};"> 
@lang('emails.social_account.footer')<br/>@lang('emails.social_account.footer2')<br/><br/> @lang('emails.regards')
</p>

<p style="direction:{{$direction}}; text-align: {{$direction == 'rtl'? 'right' : 'left'}};">
<a href="{{config('app.url')}}">@lang('emails.team', ['app' => config('app.name')])</a>
</p>


</x-mail::message>
