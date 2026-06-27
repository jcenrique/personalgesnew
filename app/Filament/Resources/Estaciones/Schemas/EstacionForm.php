<?php

namespace App\Filament\Resources\Estaciones\Schemas;

use App\Models\Zona;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Icon;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Filament\Support\RawJs;

class EstacionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)

            ->components([
                Select::make('zona_id')
                    ->label(__('Zona'))
                    ->required()
                    ->options(Zona::all()->pluck('name', 'id')),

                TextInput::make('name')
                    ->autocapitalize('characters')
                    ->required()
                    ->extraInputAttributes(['class' => 'uppercase'])
                    ->dehydrateStateUsing(fn($state) => strtoupper($state))

                    ->maxLength(50)
                    ->label(__('Nombre')),
                TextInput::make('nemonico')
                    ->required()
                    ->extraInputAttributes(['class' => 'uppercase'])
                    ->dehydrateStateUsing(fn($state) => strtoupper($state))
                    ->maxLength(10)
                    ->label(__('Nemonico')),

                TextInput::make('pk')
                    ->label('Punto kilométrico')
                    ->required()
                       ->placeholder('-123,456')
                    ->rule('regex:/^-?\d{1,3},\d{3}$/') // formato exacto

                    ->belowContent([
                        Icon::make(Heroicon::InformationCircle),
                       Text::make(__('Formato: -123,456'))->color('primary')->size(Size::Small)
                    ])
                    ->dehydrateStateUsing(fn($state) => str_replace(',', '.', $state))
                    ->afterStateHydrated(fn($state, $set) => $set('pk', str_replace('.', ',', $state)))

                    ->rule('between:-999.999,999.999')


            ]);
    }
}
