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
<h1>{{ __('emails.verify_email.title') }}</h1>
<p>{{ __('emails.verify_email.greeting', ['name' => $user->name]) }}</p>
<p>{{ __('emails.verify_email.message') }}</p>
<br>

@component('mail::button', ['url' => $url, 'color' => 'blue'])
<span>{{ __('emails.verify_email.button') }}</span>
@endcomponent
@component('mail::panel')
{{ __('emails.verify_email.support') }}
@endcomponent

<p>{{ __('emails.verify_email.regards') }}</p>
<p>{{ __('emails.verify_email.team', ['app' => config('app.name')]) }}</p>

@endcomponent