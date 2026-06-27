<?php

namespace App\Filament\Resources\Rechazos\Pages;

use App\Filament\Resources\Rechazos\RechazoResource;
use Asmit\ResizedColumn\HasResizableColumn;

use Filament\Resources\Pages\ListRecords;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;


class ListRechazos extends ListRecords

{


    use HasResizableColumn;

    protected static string $resource = RechazoResource::class;

    protected function getHeaderActions(): array
    {
        return [
           // CreateAction::make(),
        ];
    }


}
