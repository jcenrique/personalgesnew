<?php

namespace App\Filament\App\Resources\Disponibilidades\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class DisponibilidadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('year')
                    ->required(),
                DatePicker::make('fecha')
                    ->required(),
                TextInput::make('razon')
                    ->required(),
            ]);
    }
}
