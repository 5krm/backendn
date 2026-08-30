<x-mail::layout> # {{ __('emails.tutor_invitation.title') }}
{{ __('emails.tutor_invitation.greeting', ['name' => $user->name]) }}

{{ __('emails.tutor_invitation.message') }}

<x-mail::button :url="$setup_link">
    {{ __('emails.tutor_invitation.button') }}
</x-mail::button>

{{ __('emails.tutor_invitation.support') }}

{{ __('emails.tutor_invitation.regards') }} <br>
{{ config('app.name') }}

@slot('footer')
    <x-mail::footer>
        © {{ date('Y') }} {{ config('app.name') }}
    </x-mail::footer>
@endslot
</x-mail::layout>
