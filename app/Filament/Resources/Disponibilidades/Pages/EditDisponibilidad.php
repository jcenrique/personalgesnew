<?php

namespace App\Filament\Resources\Disponibilidades\Pages;

use App\Filament\Resources\Disponibilidades\DisponibilidadResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDisponibilidad extends EditRecord
{
    protected static string $resource = DisponibilidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
