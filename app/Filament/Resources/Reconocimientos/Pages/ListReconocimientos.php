<?php

namespace App\Filament\Resources\Reconocimientos\Pages;

use App\Filament\Resources\Reconocimientos\ReconocimientoResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListReconocimientos extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = ReconocimientoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->tooltip(__('Crear reconocimiento si el usuario no lo tiene'))
                ->modalWidth(Width::Small),

        ];
    }
}
