<?php

namespace App\Filament\App\Resources\Additionaldays\Pages;

use App\Filament\App\Resources\Additionaldays\AdditionaldayResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditAdditionalday extends EditRecord
{
    protected static string $resource = AdditionaldayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
