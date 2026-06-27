<?php

namespace App\Filament\App\Resources\Computos\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ComputoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                    TextInput::make('year')
                        ->label(__('Año'))
                        ->required()
                        ->numeric()
                        ->minValue(1900)
                        ->maxValue(2100)
                        ->default(now()->year)
                        ->disabled(fn ($record) => $record !== null), // Deshabilitar el campo si ya existe un registro
            ]);
    }
}
