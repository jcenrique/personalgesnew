<?php

namespace App\Filament\Resources\Categoriaelementos\Pages;

use App\Filament\Resources\Categoriaelementos\CategoriaelementoResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCategoriaelemento extends EditRecord
{
    protected static string $resource = CategoriaelementoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
