@component('mail::message')
<style>
.content p, h1, .inner-body {
    direction: {{ $direction }} !important;
    text-align: {{ $direction == 'ltr' ? 'left' : 'right' }} !important;
}
.button, .button *,  .footer, .footer * {
text-align: center !important;
}

[class*="footer"], [class*="footer"] td, [class*="footer"] td *{
    text-align: center !important;
    direction: {{$direction}};
}
</style>
<div class='content' style="direction:{{$direction}}; text-align:{{$direction == 'ltr'?'left':'right'}}">
    <h1>{{ __('emails.reset_password.title') }}</h1>
    <p>{{ __('emails.reset_password.greeting', ['name' => $user->name]) }}</p>
    <p>{{ __('emails.reset_password.message') }}</p>
    <br>
</div>
@component('mail::button', ['url' => $url, 'color' => 'blue'])
<span>{{ __('emails.reset_password.button') }}</span>
@endcomponent
@component('mail::panel')
<div class='content'>
    {{ __('emails.reset_password.expires', ['minutes' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]) }}<br>
    {{ __('emails.reset_password.ignore') }}
</div>
@endcomponent

<div class='content'>
<p >{{ __('emails.reset_password.regards') }}</p>
<p >{{ __('emails.reset_password.team', ['app' => config('app.name')]) }}</p>
</div>
@endcomponent