<?php

namespace App\Filament\App\Resources\Computos\Pages;

use App\Filament\App\Resources\Computos\ComputoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Attributes\On;

class ViewComputo extends ViewRecord
{
    protected static string $resource = ComputoResource::class;

    #[On('updateData')]
    public function updateData(array $data): void
    {
        $this->refresh();

    }

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }
}
