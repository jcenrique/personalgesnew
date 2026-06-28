<x-filament-widgets::widget>
    <div wire:poll.20s>
        @if ($this->hasPendingSolicitudes())
            {{ $this->getTable() }}
        @else
            <x-filament::section>
                <div class="text-sm text-center text-gray-600 dark:text-gray-300">
                    {{ __('No hay solicitudes pendientes') }}
                </div>
            </x-filament::section>
        @endif
    </div>

    <br>
    {{-- <x-filament::section>
        <div class="flex mb-4">

                <div wire:poll class="mr-8">
                    {{ $this->reconocimiento }}
                </div>


        </div>




    </x-filament::section> --}}







</x-filament-widgets::widget>
