<?php

namespace App\Filament\App\Resources\Inspecciones\Schemas;


use App\Models\Estacion;
use App\Models\Inspeccion;
use App\Models\User;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\IconSize;
use Filament\Support\Enums\Size;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class InspeccionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema



            ->components([
                Section::make(__('Inspección'))
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([

                        DateTimePicker::make('fecha_hora')
                            ->label(__('Fecha y hora'))
                            ->required()
                            ->reactive()
                            ->closeOnDateSelection()
                            ->native()
                            ->seconds(false),

                        Select::make('type')
                            ->label(__('Tipo inspección'))
                            ->disabledOn('edit')
                            ->dehydrated()
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($set, $operation) {

                                if ($operation == 'create') {
                                    $set('estacion_id', null);
                                }
                            })
                            ->options([
                                'periodica' => __('Periódica'),
                                'especial' => __('Especial')
                            ])
                            ->default('periodica'),

                        Select::make('estacion_id')
                            ->required()
                            ->disabledOn('edit')
                            ->dehydrated()
                            ->disabled(function (callable $get, $operation) {
                                return $get('fecha_hora') == null || $operation == 'edit';
                            })
                            ->label('Estación')
                            ->reactive()
                            ->searchable()
                            ->optionsLimit(120)
                            ->preload()

                            ->options(function (callable $get) {
                                //hay que tener en cuenta las estaciones que pertenecen a la zona del usuario logueado
                                $zonas_ids = User::find(Auth::id())->zonas()->pluck('zona_id')->toArray();

                                $fecha = $get('fecha_hora');
                                $estacionActual = $get('estacion_id');

                                $fecha = \Carbon\Carbon::parse($fecha);

                                // Obtener rango del cuatrimestre
                                [$inicio, $fin] = self::rangoCuatrimestre($fecha);

                                // Estaciones que ya tienen inspección en ese cuatrimestre
                                $ocupadas = Inspeccion::where('type', 'periodica')->whereBetween('fecha_hora', [$inicio, $fin])
                                    ->pluck('estacion_id')->toArray();


                                // Si estamos editando, permitir la estación actual
                                if ($estacionActual) {
                                    $ocupadas = array_diff($ocupadas, [$estacionActual]);
                                }
                                if ($get('type') === 'especial') {
                                    //solo estaciones de la zona del usuario logueado
                                    return Estacion::orderBy('name')
                                        ->whereHas('zona', function (Builder $query) use ($zonas_ids) {
                                            $query->whereIn('id', $zonas_ids);
                                        })
                                        ->pluck('name', 'id');
                                } else {
                                    //solo estaciones de la zona del usuario logueado que no tengan una inspeccion periodica en el cuatrimestre actual
                                    return Estacion::whereNotIn('id', $ocupadas)
                                        ->whereHas('zona', function (Builder $query) use ($zonas_ids) {
                                            $query->whereIn('id', $zonas_ids);
                                        })
                                        ->orderBy('name')
                                        ->pluck('name', 'id');
                                }
                            })




                    ]),


                Section::make(__('Agentes'))
                    ->icon('fas-asterisk')
                    ->iconColor('danger')
                    ->columns(2)
                    ->iconSize(IconSize::ExtraSmall)

                    ->columnSpanFull()
                    ->schema([
                        Select::make('user_id_1')
                            ->label(__('JS'))
                            ->searchable()
                            //solo usuarios que pertenezcan a las estaciones de la zona del usuario logueado
                            ->options(User::whereHas(
                                'roles',
                                fn($q) =>
                                $q->where('name', 'jefe_servicio')
                            )->whereHas(
                                'zonas',
                                function (Builder $query) {
                                    $zonas_ids = User::find(Auth::id())->zonas()->pluck('zona_id')->toArray();

                                    $query->whereIn('zonas.id', $zonas_ids);
                                }

                            )->orderBy('name', 'asc')->pluck('name', 'id')),

                        Select::make('user_id_2')
                            ->label(__('TR o TRH'))

                            ->options(
                                User::whereHas(
                                    'roles',
                                    fn($q) =>
                                    $q->whereIn('name', ['tecnico_red', 'tecnico_red_habilitado'])
                                )->whereHas(
                                    'zonas',
                                    function (Builder $query) {
                                        $zonas_ids = User::find(Auth::id())->zonas()->pluck('zona_id')->toArray();

                                        $query->whereIn('zonas.id', $zonas_ids);
                                    }

                                )->orderBy('name', 'asc')
                                    ->get()

                                    ->mapWithKeys(function ($user) {
                                        $roles = $user->roles()
                                            ->pluck('name')
                                            ->map(fn($role) => str_replace('_', ' ', ucwords($role)))
                                            ->join(', ');

                                        return [$user->id  => "{$user->name} ({$roles})"];
                                    })
                            )
                            ->searchable()
                            ->preload(),

                        TextEntry::make('user_id_1')
                            ->columnSpanFull()
                            ->hiddenLabel()
                            ->state('Hello, world!')
                            ->formatStateUsing(function ($state, $get) {
                                if ($get('type') == 'periodica') {
                                    return __('Los campos JS y TR no pueden estar vacíos');
                                } elseif ($get('type') == 'especial') {
                                    return __('Al menos se debe completar 1 de los campos de JS o el TR');
                                }

                                return '';
                            })
                            ->color('primary')
                            ->iconColor('primary')
                            ->icon(Heroicon::InformationCircle)
                    ]),

                // Campos solo para inspección especial
                Section::make(__('Inspección especial'))
                    ->schema([
                        TextInput::make('tema')
                            ->required(fn(Get $get) => $get('type') === 'especial')
                            ->label(__('Tema de la visita')),
                        RichEditor::make('cuestiones')
                            ->required(fn(Get $get) => $get('type') === 'especial')
                            ->label(__('Cuestión objeto de la inspección'))
                            ->extraAttributes(['style' => 'min-height: 300px;']),
                        Group::make([
                            DatePicker::make('fecha_comunicacion')
                                ->label(__('Fecha comunicación anomalias'))

                                ->reactive()
                                ->closeOnDateSelection()
                                ->native(),
                            Toggle::make('actions')
                                ->label(fn(Get $get) => $get('actions') ? 'SI' : 'No')
                                ->reactive()
                                ->required(fn(Get $get) => $get('type') === 'especial')
                                ->aboveLabel(__('Acciones correctivas'))
                                ->onColor('success')
                                ->offColor('danger')
                                ->inline()
                                ->onIcon(Heroicon::CheckCircle)
                                ->offIcon(Heroicon::XCircle),
                        ])->columns(2)

                    ])
                    ->columnSpanFull()
                    ->visible(fn(Get $get) => $get('type') === 'especial'),

                Section::make(__('Observaciones/Incidencias'))
                    ->columnSpanFull()

                    ->schema([
                        RichEditor::make('observaciones')
                            ->hiddenLabel()
                            ->extraAttributes(['style' => 'min-height: 300px;'])
                            ->toolbarButtons([
                                ['bold', 'italic', 'underline', 'strike', 'subscript', 'superscript'],
                                ['h2', 'h3'],
                                ['alignStart', 'alignCenter', 'alignEnd'],
                                ['bulletList', 'orderedList'],
                                ['table'], // The `customBlocks` and `mergeTags` tools are also added here if those features are used.
                                ['undo', 'redo'],
                            ])

                    ]),


            ]);
    }

    private static function  rangoCuatrimestre(Carbon $fecha)
    {
        $mes = $fecha->month;

        if ($mes <= 4) {
            return [
                $fecha->copy()->startOfYear(),
                $fecha->copy()->startOfYear()->addMonths(3)->endOfMonth(),
            ];
        }

        if ($mes <= 8) {
            return [
                $fecha->copy()->startOfYear()->addMonths(4),
                $fecha->copy()->startOfYear()->addMonths(7)->endOfMonth(),
            ];
        }

        return [
            $fecha->copy()->startOfYear()->addMonths(8),
            $fecha->copy()->endOfYear(),
        ];
    }
}
