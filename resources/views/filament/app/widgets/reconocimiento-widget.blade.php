<x-filament-widgets::widget>

    <x-filament::section>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_1fr_1.35fr] lg:gap-5">
            <div wire:poll class="min-w-0">
                {{ $this->reconocimiento }}
            </div>

            <div wire:poll class="min-w-0">
                {{ $this->computo }}
            </div>

            <div wire:poll class="min-w-0">
                {{ $this->diasDisponibles }}
            </div>

            <div wire:poll class="min-w-0">
                {{ $this->dias }}
            </div>
        </div>




    </x-filament::section>


</x-filament-widgets::widget>
