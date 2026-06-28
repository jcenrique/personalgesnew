<?php

namespace App\Filament\Resources\Elementoinspecciones\Pages;

use App\Filament\Resources\Elementoinspecciones\ElementoinspeccionResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListElementoinspeccions extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = ElementoinspeccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->createAnother(false),
        ];
    }
}
