<?php

namespace App\Filament\App\Resources\Computos\Pages;

use App\Filament\App\Resources\Computos\ComputoResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditComputo extends EditRecord
{
    protected static string $resource = ComputoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // DeleteAction::make(),
            // ForceDeleteAction::make(),
            // RestoreAction::make(),
        ];
    }
}
