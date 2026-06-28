<?php

namespace App\Filament\Resources\Rechazos\Pages;

use App\Filament\Resources\Rechazos\RechazoResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Resources\Pages\ListRecords;

class ListRechazos extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = RechazoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
