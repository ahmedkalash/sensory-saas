<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            <x-filament::button type="submit">
                حفظ التغييرات
            </x-filament::button>

            <x-filament::button color="gray" tag="a" :href="request()->header('referer') ?? '/'">
                إلغاء
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
