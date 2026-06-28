<?php

namespace App\Filament\Resources\Categoriaelementos\Pages;

use App\Filament\Resources\Categoriaelementos\CategoriaelementoResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;

class ListCategoriaelementos extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = CategoriaelementoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::Medium)
                ->createAnother(false),
        ];
    }
}
