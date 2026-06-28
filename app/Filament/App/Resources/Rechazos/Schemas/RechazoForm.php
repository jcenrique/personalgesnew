<?php

namespace App\Filament\App\Resources\Rechazos\Schemas;

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
