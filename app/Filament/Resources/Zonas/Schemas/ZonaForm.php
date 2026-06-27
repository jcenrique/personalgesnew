<?php

namespace App\Filament\Resources\Zonas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ZonaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label(__('Nombre de la zona'))
                    ->required()


                    ->extraInputAttributes(['class' => 'uppercase'])
                    ->dehydrateStateUsing(fn($state) => strtoupper($state))


                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
            ]);
    }
}
