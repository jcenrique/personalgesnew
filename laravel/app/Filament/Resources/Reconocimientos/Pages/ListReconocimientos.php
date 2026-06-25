<?php

namespace App\Filament\Resources\Reconocimientos\Pages;

use App\Filament\Resources\Reconocimientos\ReconocimientoResource;
use App\Models\Reconocimiento;
use App\Models\User;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\CreateAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\Width;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Query\Builder;

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
