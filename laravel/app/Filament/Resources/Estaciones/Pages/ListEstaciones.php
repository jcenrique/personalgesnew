<?php

namespace App\Filament\Resources\Estaciones\Pages;

use App\Filament\Resources\Estaciones\EstacionResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListEstaciones extends ListRecords
{
    use HasResizableColumn;
    protected static string $resource = EstacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::Small)
            ,
        ];
    }
}
