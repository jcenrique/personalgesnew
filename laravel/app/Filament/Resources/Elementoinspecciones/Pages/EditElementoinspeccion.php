<?php

namespace App\Filament\Resources\Elementoinspecciones\Pages;

use App\Filament\Resources\Elementoinspecciones\ElementoinspeccionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditElementoinspeccion extends EditRecord
{
    protected static string $resource = ElementoinspeccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
