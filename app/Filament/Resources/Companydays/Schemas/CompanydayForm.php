<?php

namespace App\Filament\Resources\Companydays\Schemas;

use App\Enum\StatusSolicitudes;
use App\Models\Companyday;
use App\Models\Disfrute;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class CompanydayForm
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
                    ->options(User::pluck('name', 'id'))
                    ->disabled(fn ($operation) => $operation === 'edit')
                    // cuando se seleccione una opcion actualizar DatePicker para deshabilitar los sábados ya registrados por ese usuario
                    ->reactive()
                    ->required(),

                DatePicker::make('fecha')
                    ->label(__('Fecha'))
                    ->rules([
                        fn (Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {

                            if ($value) {

                                // ignorar el registro actual si se está editando
                                if ($get('id')) {
                                    $exists = Companyday::where('user_id', $get('user_id'))
                                        ->where('fecha', $value)
                                        ->where('id', '!=', $get('id'))
                                        ->exists();
                                } else {

                                    $exists = Companyday::where('user_id', $get('user_id'))
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
                    // ->minDate(Carbon::now()->addMonths(-12)->startOfMonth())
                    ->maxDate(Carbon::now()->addMonths(12)->endOfMonth()),

                Select::make('disfrute.status')
                    ->label(__('Estado'))
                    ->options(StatusSolicitudes::class)
                    ->default(StatusSolicitudes::Disponible)
                    ->visible(fn ($operation) => $operation !== 'create')
                    ->required()
                    // si el estado cambia a disponible borrar el campo fecha de disfrute,

                    ->reactive()
                    ->afterStateUpdated(function (Get $get, $set, $state, $livewire, $record) {

                        if (! $record->disfrute) {

                            $set('disfrute.fecha_disfrute', null);
                        } elseif (in_array($state, [StatusSolicitudes::Solicitado, StatusSolicitudes::Aprobado])) {
                            // recuperar el valor del campo fecha_disfrute del registro
                            $set('disfrute.fecha_disfrute', $record->fecha_disfrute);
                        } elseif ($state === StatusSolicitudes::Rechazado) {
                            // si el estado cambia a rechazado mostrar el campo  y hacerlo requerido

                            $set('disfrute.fecha_disfrute', $record->fecha_disfrute);
                        }
                    }),

                DatePicker::make('disfrute.fecha_disfrute')
                    ->label(__('Fecha de disfrute'))
                    // valor por defecto al editar que sea el valor del campo fecha_disfrute del registro

                    // ->rules(function (callable $get, $record) {
                    //     return [
                    //         Rule::unique('disfrutes', 'fecha_disfrute')
                    //             ->where('user_id', $get('user_id'))
                    //             ->where('disfrutable_type', get_class($record))
                    //             ->where('disfrutable_id', $record->id)
                    //             ->ignore($record?->disfrute?->id),
                    //     ];
                    // })

                    // incluir pequeña descricion debajo del campo de fecha de disfrute que diga "Selecciona la fecha de disfrute para el día adicional, esta fecha no puede ser anterior a la fecha actual ni posterior a 12 meses a partir de la fecha actual, además se deshabilitarán las fechas ya registradas por el usuario seleccionado para disfrute"
                    ->helperText(__('Selecciona la fecha de disfrute para el día adicional,
                                    esta fecha no puede ser posterior a 12 meses a partir de la fecha actual,
                                    además se deshabilitarán las fechas ya registradas por el usuario seleccionado para disfrute'))

                    // ->visible(fn($operation) => $operation !== 'create' )
                    ->native(false)
                    ->format('Y-m-d')
                    ->displayFormat('d M Y')
                    ->closeOnDateSelection()
                    ->locale('es')
                    // ->minDate(Carbon::now()->addMonths(-12)->startOfMonth())
                    ->maxDate(Carbon::now()->addMonths(12)->endOfMonth())
                    // deshabilitar los días ya registrados por el usuario seleccionado para disfrute, para eso hay que recuperar el valor del campo user_id y hacer una consulta a la base de datos para obtener los días de disfrute registrados por ese usuario y deshabilitarlos en el DatePicker
                    ->disabledDates(function (Get $get) {
                        $userId = $get('user_id');
                        if (! $userId) {
                            return [];
                        }
                        $disfrutes = Disfrute::where('user_id', $userId)->pluck('fecha_disfrute')->toArray();

                        return $disfrutes;
                    })
                    ->visible(fn (Get $get) => $get('disfrute.status') !== StatusSolicitudes::Disponible)
                    ->required(fn (Get $get) => in_array($get('disfrute.status'), [StatusSolicitudes::Solicitado, StatusSolicitudes::Aprobado, StatusSolicitudes::Rechazado]))
                    ->label(__('Fecha de disfrute')),

                TextInput::make('razon')
                    ->label(__('Razón'))
                    ->required(),

            ]);
    }
}
