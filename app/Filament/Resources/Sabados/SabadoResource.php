<?php

namespace App\Filament\Resources\Sabados;

use App\Enum\StatusSolicitudes;
use App\Filament\Resources\Sabados\Actions\AprobarSabadoAction;
use App\Filament\Resources\Sabados\Actions\RechazarSabadoAction;
use App\Filament\Resources\Sabados\Pages\ManageSabados;
use App\Models\Disfrute;
use App\Models\Sabado;
use BackedEnum;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use UnitEnum;

class SabadoResource extends Resource
{
    protected static ?string $model = Sabado::class;

    protected static string|BackedEnum|null $navigationIcon = 'fas-calendar-week';

    protected static ?string $recordTitleAttribute = 'user.name';


    public static function getNavigationGroup(): string|UnitEnum|null
    {
        return __('Gestión');
    }

    //funciones de etiquetas singular y plural para el recurso
    public static function getLabel(): string
    {
        return __('Sábado');
    }
    public static function getPluralLabel(): string
    {
        return __('Sábados');
    }

    // funcion para que aparezca el badge del numero de sabados disponibles
    public static function getNavigationBadge(): ?string
    {
        //obtener los dias adicionales del usuario para el año actual
        $sabados_totales =  static::getEloquentQuery()->count();



        //contar los días adicionales  se han solicitado disfrutar
        $sabados_disfrutados =  static::getEloquentQuery()->whereHas('disfrute', function (Builder $query) {
            $query->where('status', StatusSolicitudes::Aprobado);
        })->count();

        $sabados_disponibles = $sabados_totales - $sabados_disfrutados;




        return  $sabados_disponibles;
    }
    //badge color para el numero de usuarios
    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }
    public static function getNavigationBadgeTooltip(): ?string
    {
        return __('Número de sábados totales disponibles');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->label(__('Usuario'))
                    ->searchable()
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->disabled(fn($operation) => $operation === 'edit')
                    //cuando se seleccione una opcion actualizar DatePicker para deshabilitar los sábados ya registrados por ese usuario
                    ->reactive()
                    ->required(),

                DatePicker::make('sabado_trabajado')
                    ->label(__('Fecha del Sábado'))
                    ->native(false)
                    ->format('Y-m-d')
                    ->locale('es')
                    ->displayFormat('d M Y')
                    ->closeOnDateSelection()
                    ->minDate(Carbon::now()->addMonths(-12)->startOfMonth())
                    ->maxDate(Carbon::now()->addMonths(12)->endOfMonth())
                    ->disabledDates(function (Get $get) {
                        // Rango razonable para escoger fechas (por ejemplo 1 año)
                        $start = Carbon::now()->addMonths(-12)->startOfMonth();
                        $end   = Carbon::now()->addMonths(12)->endOfMonth();
                        $period = CarbonPeriod::create($start, $end);
                        $disabled = [];

                        foreach ($period as $date) {
                            if (!$date->isSaturday()) {    // ❌ deshabilitar todo lo que NO sea sábado
                                $disabled[] = $date->translatedFormat('Y-m-d');
                            }
                            //también  deshabilitar sábados ya registrados por el usuario
                            if ($date->isSaturday() && Sabado::where('user_id', $get('user_id'))->where('sabado_trabajado', $date->translatedFormat('Y-m-d'))->exists()) {
                                $disabled[] = $date->translatedFormat('Y-m-d');
                            }
                        }
                        return $disabled;
                    })

                    ->required(),

                Select::make('disfrute.status')
                    ->label(__('Estado'))
                    ->options(StatusSolicitudes::class)
                    ->default('disponible')
                    ->visible(fn($operation) => $operation !== 'create')
                    ->required()
                    // si el estado cambia a disponible borrar el campo  fecha de disfrute,

                    ->reactive()
                    ->afterStateUpdated(function (Get $get, $set, $state, $livewire, $record) {
                        if ($state === StatusSolicitudes::Disponible) {

                            $set('fecha_disfrute', null);
                        } elseif (in_array($state, [StatusSolicitudes::Solicitado, StatusSolicitudes::Aprobado])) {
                            //recuperar el valor del campo fecha_disfrute del registro
                            $set('fecha_disfrute', $record->fecha_disfrute);
                        } else if ($state === StatusSolicitudes::Rechazado) {


                            $set('fecha_disfrute', $record->fecha_disfrute);
                        }
                    }),

                DatePicker::make('disfrute.fecha_disfrute')
                    ->rules(function (callable $get, $record) {
                        return [
                            Rule::unique('disfrutes', 'fecha_disfrute')
                                ->where('user_id', $get('user_id'))
                                ->where('disfrutable_type', get_class($record))
                                ->where('disfrutable_id', $record->id)
                                ->ignore($record?->disfrute?->id),
                        ];
                    })

                    ->disabledDates(function (Get $get) {
                        $userId = $get('user_id');
                        if (!$userId) {
                            return [];
                        }
                        $disfrutes = Disfrute::where('user_id', $userId)->pluck('fecha_disfrute')->toArray();
                        return $disfrutes;
                    })
                    ->visible(fn($operation) => $operation !== 'create')
                    ->native(false)
                    ->format('Y-m-d')
                    ->displayFormat('d M Y')
                    ->closeOnDateSelection()
                    ->locale('es')
                    ->minDate(Carbon::now()->addMonths(-12)->startOfMonth())
                    ->maxDate(Carbon::now()->addMonths(12)->endOfMonth())
                    ->visible(fn(Get $get) => $get('disfrute.status') !== StatusSolicitudes::Disponible)
                    ->required(fn(Get $get) => in_array($get('disfrute.status'), [StatusSolicitudes::Solicitado, StatusSolicitudes::Aprobado, StatusSolicitudes::Rechazado]))
                    ->label(__('Fecha de disfrute')),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->poll(function ($livewire) {


                $livewire->dispatch('refresh-sidebar');
                return '10s';
            })
            ->defaultSort('sabado_trabajado', 'asc')
            ->columns([


                TextColumn::make('user.name')
                    ->label(__('Usuario'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('sabado_trabajado')
                    ->date('d F Y')
                    ->color('info')

                    ->label(__('Sábado trabajado'))
                    ->sortable(),

                TextColumn::make('disfrute.status')
                    ->label(__('Estado'))
                    ->default(StatusSolicitudes::Disponible)
                    ->badge(),

                TextColumn::make('disfrute.fecha_disfrute')
                    ->label(__('Fecha de disfrute'))
                    ->searchable()
                    ->placeholder(StatusSolicitudes::Disponible->getLabel())
                    ->color('success')
                    ->weight(FontWeight::ExtraBold)
                    ->date('d F Y')
                    ->sortable(),



                TextColumn::make('deleted_at')
                    ->label(__('Eliminado en'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label(__('Creado en'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label(__('Actualizado en'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([

                //crear un filtro para usuarios
                SelectFilter::make('user_id')
                    ->label(__('Usuario'))
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                AprobarSabadoAction::make('aprobar')
                    ->visible(fn($record) => $record->disfrute?->status === StatusSolicitudes::Solicitado),

                RechazarSabadoAction::make('rechazar')
                    ->visible(fn($record) => $record->disfrute?->status === StatusSolicitudes::Solicitado),

                EditAction::make()
                    ->hiddenLabel(true)
                    ->recordTitle(function($record){
                        return $record->user->name . ' - ' . $record->sabado_trabajado->translatedFormat('d F Y');
                    })
                    ->mutateRecordDataUsing(function (array $data, $record) {

                        if ($record->disfrute) {
                            $data['disfrute']['fecha_disfrute'] = $record->disfrute->fecha_disfrute;
                            $data['disfrute']['status'] = $record->disfrute->status;
                        } else {

                            $data['disfrute']['status'] = StatusSolicitudes::Disponible;
                        }
                        return $data;
                    })

                    ->tooltip(__('Edit'))
                    ->action(function ($record, $data) {
                        $status = $data['disfrute']['status'];

                        // Si hay disfrute existente, lo cargamos como modelo
                        $disfrute = $record->disfrute;

                        if (in_array($status, [
                            StatusSolicitudes::Solicitado,
                            StatusSolicitudes::Aprobado,
                            StatusSolicitudes::Rechazado
                        ])) {

                            if ($disfrute) {
                                // ACTUALIZAR MODELO (dispara updated)
                                $disfrute->fill([
                                    'fecha_disfrute' => $data['disfrute']['fecha_disfrute'],
                                    'status' => $status,
                                ]);

                                $disfrute->save();
                            } else {
                                // CREAR MODELO (dispara created)
                                $record->disfrute()->create([
                                    'fecha_disfrute' => $data['disfrute']['fecha_disfrute'],
                                    'user_id' => $record->user_id,
                                    'status' => $status,
                                ]);
                            }
                        } elseif ($status === StatusSolicitudes::Disponible) {

                            if ($disfrute) {
                                // ELIMINAR MODELO (dispara deleted)
                                $disfrute->delete();
                            }
                        }

                        $record->sabado_trabajado = $data['sabado_trabajado'];

                        $record->save(); // dispara updated() en Additionalday
                    }),

                DeleteAction::make()
                    ->hiddenLabel(true)
                    //modificar el titulo y descripcion del modal de confirmación para que quede más claro que se va a eliminar una solicitud de sábado y no un registro cualquiera
                    ->modalHeading(__('¿Eliminar  sábado?'))
                    ->modalDescription(__('¿Estás seguro de que deseas eliminar el sábado? Esta acción no se puede deshacer, pero puedes restaurarla desde la pestaña de eliminados si es necesario.'))
                    ->tooltip(__('Delete')),

                ForceDeleteAction::make(),

                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),

                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSabados::route('/'),
        ];
    }

      public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereHas('user.residencias', function (Builder $query) {
                $user = auth()->user();
                $zonaIds = $user->zonas()
                    ->pluck('zonas.id')
                    ->toArray();

                $query->whereIn('zona_id', $zonaIds);
            })
            ->with('disfrute');
    }


}
