<?php

namespace App\Filament\Resources\Rechazos\Pages;

use App\Filament\Resources\Rechazos\RechazoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRechazo extends EditRecord
{
    protected static string $resource = RechazoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
