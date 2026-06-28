<?php

namespace App\Filament\App\Resources\Computos\Pages;

use App\Filament\App\Resources\Computos\ComputoResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListComputos extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = ComputoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // CreateAction::make(),
        ];
    }
}
