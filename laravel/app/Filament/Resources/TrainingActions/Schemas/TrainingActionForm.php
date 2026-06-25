<?php

namespace App\Filament\Resources\TrainingActions\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Hamcrest\Core\Set;

class TrainingActionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            //->columns(4)

            ->components([
                TextInput::make('company_name')
                    ->label(__('Empresa'))
                    ->columnSpan(2)
                    ->required()
                    ->maxLength(255),
                TextInput::make('trainer_name')
                    ->label(__('Formador'))
                    ->columnSpan(2)
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->label(__('Tipo de acción'))
                    ->options([
                        'interna' => __('Interna'),
                        'externa' => __('Externa')
                    ])
                    ->required(),
                Select::make('mode')
                    ->label(__('Modalidad'))
                    ->options([
                        'presencial' => __('Presencial'),
                        'online' => __('On Line')
                    ])
                    ->required(),
                TextInput::make('location')
                    ->label(__('Lugar'))
                    ->columnSpan(2)
                    ->required()
                    ->maxLength(255),

                DatePicker::make('start_date')
                    ->label(__('Fecha de inicio'))
                    ->native(false)
                    ->displayformat('d F Y')   // lo que ve el usuario
                    ->format('Y-m-d')
                    ->locale('es')
                    ->closeOnDateSelection()
                    ->after('2020-01-01')
                    ->reactive()
                    ->afterStateUpdated(function($state , callable  $set ){

                    $set('end_date', $state);
                    })
                    ->required(),
                DatePicker::make('end_date')
                    ->afterOrEqual('start_date')
                    ->native(false)
                    ->locale('es')
                    ->closeOnDateSelection()

                    ->displayformat('d F Y')   // lo que ve el usuario
                    ->format('Y-m-d')
                    ->label(__('Fecha de finalización'))
                    ->required(),
                Textarea::make('notes')
                    ->label(__('Notas'))
                    ->columnSpanFull(),
            ]);
    }
}
