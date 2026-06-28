<?php

namespace App\Filament\App\Resources\Disponibilidades\Pages;

use App\Filament\Resources\Disponibilidades\DisponibilidadResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDisponibilidades extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = DisponibilidadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //  CreateAction::make(),
        ];
    }
}
