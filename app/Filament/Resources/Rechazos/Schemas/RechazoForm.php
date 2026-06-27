<?php

namespace App\Filament\Resources\Rechazos\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class RechazoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Textarea::make('razon')
                    ->label(__('Motivo de rechazo'))
                    ->required()
                    ->columnSpanFull(),
            ]);
    }
}
