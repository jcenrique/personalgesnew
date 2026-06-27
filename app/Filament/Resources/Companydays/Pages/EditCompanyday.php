<?php

namespace App\Filament\Resources\Companydays\Pages;

use App\Filament\Resources\Companydays\CompanydayResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyday extends EditRecord
{
    protected static string $resource = CompanydayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
