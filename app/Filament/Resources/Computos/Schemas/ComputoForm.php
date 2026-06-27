<?php

namespace App\Filament\Resources\Computos\Schemas;

use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Illuminate\Validation\Rule;

class ComputoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->columns(3)
            ->components([



                Select::make('user_id')
                    ->columnSpanFull()
                    ->rules([
                        //si el registro se esta editando, ignorar el registro actual para la validación de unicidad
                            fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                                if ($get('year') && $value) {
                                    //ignorar el registro actual si se está editando
                                    if ($get('id')) {
                                        $exists = \App\Models\Computo::where('user_id', $value)
                                            ->where('year', $get('year'))
                                            ->where('id', '!=', $get('id'))
                                            ->exists();
                                    } else {
                                        $exists = \App\Models\Computo::where('user_id', $value)
                                            ->where('year', $get('year'))
                                            ->exists();
                                    }


                                    if ($exists) {
                                        $fail(__('El usuario ya tiene un cómputo registrado para el año seleccionado.'));
                                    }
                                }

                            if ($get('year') === 'foo' && $value !== 'bar') {
                                $fail("The {$attribute} is invalid.");
                            }
                        },
                    ])


                    ->label(__('Usuario'))
                    ->searchable()
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->disabled(fn($operation) => $operation === 'edit')
                    //cuando se seleccione una opcion actualizar DatePicker para deshabilitar los sábados ya registrados por ese usuario
                    ->reactive()
                    ->required(),

                Select::make('year')
                    ->label(__('Año'))
                    ->columnSpan(1)
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

                TextInput::make('horas')
                    ->rules([
                        //el valor del campo horas y minutos no puede ser 0 al mismo tiempo
                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $horas = $value;
                            //valor entero
                            $minutos = (int) $get('minutos');

                            if ($horas === 0 && $minutos === 0) {
                                $fail(__('El valor del campo horas y minutos no puede ser 0 al mismo tiempo.'));
                            }
                        },
                    ])
                    ->label(__('Horas'))
                    ->default(0)
                    ->columnSpan(1)
                    ->required()
                    ->minValue(0)


                    //->suffixIcon('heroicon-o-clock')
                    ->numeric(),
                TextInput::make('minutos')
                    ->label(__('Minutos'))
                    ->default(0)
                    ->columnSpan(1)
                    ->maxValue(59)
                    ->required()
                    ->minValue(0)
                    // ->suffixIcon('heroicon-o-clock')
                    ->numeric(),
            ]);
    }
}
