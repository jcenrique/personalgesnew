<?php

namespace App\Filament\Resources\Computos\Pages;

use App\Filament\Resources\Computos\ComputoResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewComputo extends ViewRecord
{
    protected static string $resource = ComputoResource::class;

    protected string $view = 'filament.resources.computos.pages.view-computo';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
