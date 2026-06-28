<?php

namespace App\Filament\Resources\Categoriaelementos\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class CategoriaelementoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                TextInput::make('nombre_eu')
                    ->label(__('Nombre en Euskera'))
                    ->extraInputAttributes(['class' => 'uppercase'])
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ->required(),
                TextInput::make('nombre_es')
                    ->label(__('Nombre en Castellano'))
                    ->extraInputAttributes(['class' => 'uppercase'])
                    ->dehydrateStateUsing(fn ($state) => strtoupper($state))
                    ->required(),
                Toggle::make('active')
                    ->label(__('Activo'))
                    ->default(true)
                    ->required(),
            ]);
    }
}
