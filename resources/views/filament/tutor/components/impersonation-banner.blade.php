@if (session()->has('impersonator_id'))
    <x-filament::link 
    href="{{ route('impersonation.leave') }}" 
    tag="a"
    icon="heroicon-m-arrow-right-start-on-rectangle" 
    color="warning"
>
    {{ __('tutor.tutors.end_impersonation', ['user' => auth()->user()->name])}}
</x-filament::link>
@endif
