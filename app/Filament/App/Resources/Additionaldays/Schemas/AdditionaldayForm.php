<?php

namespace App\Filament\App\Resources\Additionaldays\Schemas;

use App\Enum\StatusSolicitudes;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class AdditionaldayForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                DatePicker::make('year')
                    ->required(),
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                Select::make('status')
                    ->options([
                        'disponible' => StatusSolicitudes::Disponible,
                        'solicitado' => StatusSolicitudes::Solicitado,
                        'aprobado' => StatusSolicitudes::Aprobado,
                        'rechazado' => StatusSolicitudes::Rechazado,
                    ])
                    ->default('disponible')
                    ->required(),
                DatePicker::make('fecha_disfrute'),

            ]);
    }
}
