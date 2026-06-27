<?php

namespace App\Filament\Resources\Disponibilidades\Schemas;

use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class DisponibilidadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([


                Select::make('user_id')
                    ->label(__('Usuario'))
                   // ->unique(ignoreRecord: true)
                    ->searchable()
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->disabled(fn($operation) => $operation === 'edit')
                    //cuando se seleccione una opcion actualizar DatePicker para deshabilitar los sábados ya registrados por ese usuario
                    ->reactive()
                    ->required(),

                Select::make('year')
                    ->label(__('Año'))
                    ->options(function () {
                        $currentYear = date('Y');
                        return [
                            $currentYear - 2 => $currentYear - 2,
                            $currentYear - 1 => $currentYear - 1,
                            $currentYear => $currentYear,
                            $currentYear + 1 => $currentYear + 1,
                            $currentYear + 2 => $currentYear + 2,
                        ];
                    })
                    ->default(date('Y'))
                    ->required(),


                DatePicker::make('fecha')
                    ->label(__('Fecha'))
                    ->rules([
                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            if ($value) {

                                    //ignorar el registro actual si se está editando
                                    if ($get('id')) {
                                        $exists = \App\Models\Disponibilidad::where('user_id',$get('user_id'))
                                            ->where('fecha', $value)
                                            ->where('id', '!=', $get('id'))
                                            ->exists();
                                    } else {
                                        $exists = \App\Models\Disponibilidad::where('user_id',$get('user_id'))
                                            ->where('fecha', $value)
                                            ->exists();
                                    }


                                    if ($exists) {
                                        $fail(__('El usuario ya tiene registrada la fecha seleccionada.'));
                                    }
                                }
                        },
                    ])
                    ->required()
                    ->native(false)
                    ->format('Y-m-d')
                    ->displayFormat('d M Y')
                    ->closeOnDateSelection()
                    ->locale('es')
                    //->minDate(Carbon::now()->addMonths(-12)->startOfMonth())
                    ->maxDate(Carbon::now()->addMonths(12)->endOfMonth())
                //deshabilitar los días ya registrados por el usuario seleccionado para disfrute, para eso hay que recuperar el valor del campo user_id y hacer una consulta a la base de datos para obtener los días de disfrute registrados por ese usuario y deshabilitarlos en el DatePicker


                ,

                TextInput::make('razon')
                    ->label(__('Razón'))
                    ->required(),
            ]);
    }
}
