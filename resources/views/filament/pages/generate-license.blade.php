<x-filament-panels::page>
    <form wire:submit="generate">
        {{ $this->form }}

        <x-filament::button type="submit" icon="heroicon-o-key" style="margin-top: 16px;">
            توليد المفتاح
        </x-filament::button>
    </form>
</x-filament-panels::page>