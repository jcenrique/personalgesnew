<x-filament-widgets::widget>

    <x-filament::section>
        <div class="flex flex-row gap-5">

                <div wire:poll class="basis-1/4">
                    {{ $this->reconocimiento }}
                </div>

                <div  wire:poll class="basis-1/4">
                    {{ $this->computo }}
                </div>


                <div  wire:poll class="basis-1/4">
                    {{ $this->diasDisponibles }}
                </div>


                <div  wire:poll class="basis-2/4">
                    {{ $this->dias }}
                </div>


        </div>




    </x-filament::section>


</x-filament-widgets::widget>
