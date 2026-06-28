<?php

namespace App\Filament\Resources\Computos\Pages;

use App\Filament\Resources\Computos\ComputoResource;
use App\Models\Computo;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class ListComputos extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = ComputoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->modalWidth(Width::Small)

                ->action(function ($data, CreateAction $action) {

                    // comvertir los campos horas y minutos a minutos para guardar en la DB

                    $minutos_computo = ($data['horas'] * 60) + $data['minutos'];

                    Computo::create([
                        'user_id' => $data['user_id'],
                        'year' => $data['year'],
                        'disponible' => $minutos_computo,

                    ]);
                })
                // quitar el boton de crear otro
                ->createAnother(),

            Action::make('importcomputos')
                ->label(__('Importar horas de cómputo'))
                ->color('success')
                ->icon(Heroicon::ArrowUpOnSquareStack)
                // ->action(function(){
                //     dd(AddComputos::getUrl());
                // })
                ->url(AddComputos::getUrl()),

        ];
    }
}
