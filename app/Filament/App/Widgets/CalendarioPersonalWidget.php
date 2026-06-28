<?php

namespace App\Filament\App\Widgets;

use App\Enum\StatusSolicitudes;
use App\Models\Disfrute;
use App\Models\Reconocimiento;
use App\Models\TrainingAction;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Guava\Calendar\Enums\CalendarViewType;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\EventClickInfo;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\On;

class CalendarioPersonalWidget extends CalendarWidget
{
    use HasWidgetShield;

    protected bool $eventClickEnabled = true;
    // protected bool $dateClickEnabled = true;

    protected ?string $defaultEventClickAction = 'custom';

    protected static ?int $sort = 2;

    protected string $view = 'filament.app.widgets.calendario-personal-widget';

    #[On('refreshResources')]
    public function refrescar(): void
    {

        $this->refreshRecords();
        $this->refreshResources();
    }

    protected function customAction(): Action
    {
        return Action::make('custom')
            ->label(__('Ver detalle'))
            ->modalHeading(__('Detalle del evento'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('Cerrar'))
            ->modalWidth('4xl')
            ->schema(fn(Schema $schema): Schema => $this
                ->getInfolistSchemaForModel($schema, $this->getEventModel())
                ->record($this->getEventRecord()));
    }


    public function defaultSchema(Schema $schema): Schema
    {

        $event_class = $this->eventRecord::class;

        // Chekear que clase es para preparar el esquema adecuado para cada tipo de evento, por ejemplo, si el evento es un disfrute, mostrar el estado del disfrute en el
        if ($event_class === Disfrute::class) {

            return $schema->components([
                // mostrar el estado del disfrute a traves de un badge, por ejemplo, si el disfrute esta aprobado, mostrar un badge verde con el texto "Aprobado", si esta pendiente, mostrar un badge amarillo con el texto "Pendiente", si esta rechazado, mostrar un badge rojo con el texto "Rechazado". Para esto, se puede usar el estado del disfrute obtenido a traves de la relacion con el modelo relacionado para determinar el color y el texto del badge
                Section::make(__('Detalles'))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('sabado_trabajado')
                            ->label(__('Sábado trabajado'))
                            ->hidden(fn($record) => $record->disfrutable_type !== 'App\Models\Sabado')
                            ->getStateUsing(function ($record) {
                                if ($record && $record->disfrutable_type === 'App\Models\Sabado') {
                                    return Carbon::parse($record->disfrutable->sabado_trabajado)->locale(app()->getLocale())->translatedFormat('d F Y');
                                }

                                return __('Desconocido');
                            })
                            ->color('indigo')
                            ->badge(),

                        TextEntry::make('companyday_fecha')
                            ->label(__('Día pedido Empresa'))
                            ->hidden(fn($record) => $record->disfrutable_type !== 'App\Models\Companyday')
                            ->getStateUsing(function ($record) {
                                if ($record && $record->disfrutable_type === 'App\Models\Companyday') {
                                    return Carbon::parse($record->disfrutable->fecha)->locale(app()->getLocale())->translatedFormat('d F Y');
                                }

                                return __('Desconocido');
                            })
                            ->color('indigo')
                            ->badge(),

                        TextEntry::make('year')
                            ->label(__('Año'))
                            ->hidden(fn($record) => $record->disfrutable_type !== 'App\Models\Computo' && $record->disfrutable_type !== 'App\Models\Additionalday')
                            ->getStateUsing(function ($record) {
                                if ($record && ($record->disfrutable_type === 'App\Models\Computo' || $record->disfrutable_type === 'App\Models\Additionalday')) {
                                    return $record->disfrutable->year;
                                }

                                return __('Desconocido');
                            })
                            ->color('indigo')
                            ->badge(),
                        TextEntry::make('fecha_disfrute')
                            ->label(__('Fecha de disfrute'))
                            ->getStateUsing(function ($record) {
                                if ($record) {
                                    return $record->fecha_disfrute->locale(app()->getLocale())->translatedFormat('d F Y');
                                }

                                return __('Desconocido');
                            })
                            ->color('indigo')
                            ->badge(),

                        TextEntry::make('status')
                            ->label(__('Estado'))
                            ->getStateUsing(function ($record) {
                                if ($record) {
                                    $estado = $record->status ?? null;
                                    if ($estado === StatusSolicitudes::Aprobado) {
                                        return StatusSolicitudes::Aprobado;
                                    } elseif ($estado === StatusSolicitudes::Solicitado) {
                                        return StatusSolicitudes::Solicitado;
                                    } elseif ($estado === StatusSolicitudes::Rechazado) {
                                        return StatusSolicitudes::Rechazado;
                                    }
                                }

                                return __('Desconocido');
                            })
                            ->color(function ($record) {
                                if ($record) {
                                    $estado = $record->status ?? null;
                                    if ($estado === StatusSolicitudes::Aprobado) {
                                        return StatusSolicitudes::Aprobado->getColor();
                                    } elseif ($estado === StatusSolicitudes::Solicitado) {
                                        return StatusSolicitudes::Solicitado->getColor();
                                    } elseif ($estado === StatusSolicitudes::Rechazado) {
                                        return StatusSolicitudes::Rechazado->getColor();
                                    }
                                }

                                return 'secondary';
                            })
                            ->icon(Heroicon::Pencil)

                            ->badge(),
                    ]),
            ]);
        } elseif ($event_class === TrainingAction::class) {
            return $schema->components([
                // mostrar el nombre del curso a traves de un TextEntry, para esto, se puede usar la relacion con el modelo de curso para obtener el nombre del curso y mostrarlo en el TextEntry
                Section::make(__('Detalles'))
                    ->columns(4)
                    ->schema([
                        TextEntry::make('course_name')
                            ->label(__('Curso'))
                            ->getStateUsing(function ($record) {
                                if ($record && $record->course) {
                                    return $record->course->name;
                                }

                                return __('Desconocido');
                            })
                            ->color('indigo')
                            ->badge(),
                        // fecha de la accion formativa
                        TextEntry::make('start_date')
                            ->label(__('Fecha inicio'))
                            ->getStateUsing(function ($record) {
                                if ($record) {
                                    return $record->start_date->locale(app()->getLocale())->translatedFormat('d M Y');
                                }

                                return __('Desconocido');
                            })
                            ->color('indigo')
                            ->badge(),
                        TextEntry::make('end_date')
                            ->label(__('Fecha fin'))
                            ->getStateUsing(function ($record) {
                                if ($record) {
                                    return $record->end_date->locale(app()->getLocale())->translatedFormat('d M Y');
                                }

                                return __('Desconocido');
                            })
                            ->color('indigo')
                            ->badge(),
                        // lugar
                        TextEntry::make('lugar')
                            ->label(__('Lugar'))
                            ->getStateUsing(function ($record) {
                                if ($record && $record->course) {
                                    return $record->location ?? __('Desconocido');
                                }

                                return __('Desconocido');
                            })
                            ->color('indigo')
                            ->badge(),

                    ]),
            ]);
        } elseif ($event_class === Reconocimiento::class) {
            return $schema->components([
                // mostrar el lugar del reconocimiento a traves de un TextEntry, para esto, se puede usar el campo lugar del modelo de reconocimiento para mostrarlo en el TextEntry
                Section::make(__('Detalles'))
                    ->columns(4)
                    ->schema([

                        TextEntry::make('lugar')
                            ->label(__('Lugar'))
                            ->getStateUsing(function ($record) {
                                if ($record) {
                                    return $record->lugar ?? __('Desconocido');
                                }
                            })
                            ->color('indigo')
                            ->badge(),
                        TextEntry::make('fecha')
                            ->label(__('Fecha reconocimiento'))
                            ->getStateUsing(function ($record) {
                                if ($record) {
                                    return $record->fecha->locale(app()->getLocale())->translatedFormat('d M Y');
                                }

                                return __('Desconocido');
                            })
                            ->color('indigo')
                            ->badge(),
                    ]),
            ]);
        }

        return $schema->components([
            // ...
        ]);
    }

    protected int|string|array $columnSpan = [
        'md' => 2,
        'xl' => 3,
    ];

    public function getHeading(): string|HtmlString
    {
        return __('Calendario Personal');
    }

    protected CalendarViewType $calendarView = CalendarViewType::DayGridMonth;

    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        $user = Auth::user();

        $reconocimientos = Reconocimiento::where('user_id', $user->id)
            ->whereBetween('fecha', [$info->start, $info->end])
            ->get();

        return

            TrainingAction::whereHas('users', function (Builder $query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->where(function ($query) use ($info) {
                $query->whereBetween('start_date', [$info->start, $info->end])
                    ->orWhereBetween('end_date', [$info->start, $info->end]);
            })
            ->get()
            ->merge(
                $user->disfrutes()
                    ->whereBetween('fecha_disfrute', [$info->start, $info->end])

                    // los disfrutes deben estar aprobados en su relacion con el modelo  para que se muestren en el calendario
                    // ->whereHas('disfrutable', function (Builder $query) {
                    //     $query->where('status', StatusSolicitudes::Aprobado);
                    // })
                    ->get(),

            )
            ->merge($reconocimientos);
    }

    public function getOptions(): array
    {
        return [
            'displayEventTime' => false,
            'headerToolbar' => [
                'start' => 'title',
                'center' => 'dayGridMonth,timeGridWeek',
                'end' => 'today prev,next',
            ],
            'buttonText' => [
                'today' => __('today'),

                'timeGridWeek' => (__('dayGridWeek')),
                // 'listDay' => __('listDay'),
                'dayGridMonth' => __('dayGridMonth'),
                'resourceTimeGridDay' => __('resourceTimeGridDay'),
            ],
        ];
    }
}
