<x-filament-panels::page>
    {{-- `$this->getRecord()` will return the current Eloquent record for this page --}}
  {{-- Assuming your record has a 'name' attribute, you can display it like this --}}
  <div wire:poll>
     {{ $this->content }}
  </div>

</x-filament-panels::page>
