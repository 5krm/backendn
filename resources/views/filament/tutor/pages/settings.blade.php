<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}
        
        <div style="margin-top: 1rem;">
            <x-filament::button type="submit" color="primary" wire:loading.attr="disabled" style="color: white;">
                {{__('tutor.save_changes')}}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
