<?php

namespace App\Filament\Resources\Zonas\Pages;

use App\Filament\Resources\Zonas\ZonaResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListZonas extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = ZonaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
