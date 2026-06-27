<?php

namespace App\Filament\Resources\Estaciones\Pages;



use App\Filament\Resources\Estaciones\EstacionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditEstacion extends EditRecord
{
    protected static string $resource = EstacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
