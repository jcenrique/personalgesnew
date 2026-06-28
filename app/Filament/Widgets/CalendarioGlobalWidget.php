<?php

namespace App\Filament\Widgets;

use App\Enum\StatusSolicitudes;
use App\Models\Disfrute;
use App\Models\Reconocimiento;
use App\Models\TrainingAction;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Guava\Calendar\Contracts\Eventable;
use Guava\Calendar\Enums\CalendarViewType;
use Guava\Calendar\Filament\CalendarWidget;
use Guava\Calendar\ValueObjects\CalendarEvent;
use Guava\Calendar\ValueObjects\FetchInfo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\On;

class CalendarioGlobalWidget extends CalendarWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected bool $eventClickEnabled = true;

    protected ?string $defaultEventClickAction = 'custom';

    protected string $view = 'filament.widgets.calendario-global-widget';

    protected CalendarViewType $calendarView = CalendarViewType::DayGridMonth;

    #[On('refreshResources')]
    public function refrescar(): void
    {
        $this->refreshRecords();
        $this->refreshResources();
    }

    public function getHeading(): string|HtmlString
    {
        return __('Calendario global de usuarios');
    }

    protected function customAction(): Action
    {
        return Action::make('custom')
            ->label(__('Ver detalle'))
            ->modalHeading(__('Detalle del evento'))
            ->modalSubmitAction(false)
            ->modalCancelActionLabel(__('Cerrar'))
            ->modalWidth('5xl')
            ->schema(fn(Schema $schema): Schema => $this
                ->getInfolistSchemaForModel($schema, $this->getEventModel())
                ->record($this->getEventRecord()));
    }

    public function defaultSchema(Schema $schema): Schema
    {
        $eventClass = $this->eventRecord::class;

        if ($eventClass === Disfrute::class) {
            return $schema->components([
                Section::make(__('Detalles'))
                    ->columns(4)
                    ->schema([
                        TextEntry::make('user_name')
                            ->label(__('Usuario'))
                            ->getStateUsing(fn(Disfrute $record): string => $record->user?->name ?? __('Desconocido'))
                            ->badge(),
                        TextEntry::make('disfrutable_type')
                            ->label(__('Tipo de recurso solicitado'))
                            ->getStateUsing(function (Disfrute $record): string {
                                return match ($record->disfrutable_type) {
                                    'App\\Models\\Sabado' => __('Sábado'),
                                    'App\\Models\\Additionalday' => __('Día adicional'),
                                    'App\\Models\\Computo' => __('Computo'),
                                    'App\\Models\\Companyday' => __('Día solicitado por la empresa'),
                                    default => __('Desconocido'),
                                };
                            })
                            ->badge(),
                        TextEntry::make('fecha_disfrute')
                            ->label(__('Fecha de disfrute'))
                            ->getStateUsing(fn(Disfrute $record): string => $record->fecha_disfrute?->translatedFormat('d F Y') ?? __('Desconocido'))
                            ->badge(),
                        TextEntry::make('status')
                            ->label(__('Estado'))
                            ->getStateUsing(fn(Disfrute $record): string => $record->status?->getLabel() ?? __('Desconocido'))
                            ->color(fn(Disfrute $record): string => $record->status?->getColor() ?? StatusSolicitudes::Solicitado->getColor())
                            ->badge(),
                    ]),
            ]);
        }

        if ($eventClass === TrainingAction::class) {
            return $schema->components([
                Section::make(__('Detalles'))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('course_name')
                            ->label(__('Curso'))
                            ->getStateUsing(fn(TrainingAction $record): string => $record->course?->name ?? __('Desconocido'))
                            ->badge(),
                        TextEntry::make('start_date')
                            ->label(__('Fecha inicio'))
                            ->getStateUsing(fn(TrainingAction $record): string => $record->start_date?->translatedFormat('d F Y') ?? __('Desconocido'))
                            ->badge(),
                        TextEntry::make('end_date')
                            ->label(__('Fecha fin'))
                            ->getStateUsing(fn(TrainingAction $record): string => $record->end_date?->translatedFormat('d F Y') ?? __('Desconocido'))
                            ->badge(),
                        TextEntry::make('company_name')
                            ->label(__('Empresa'))
                            ->getStateUsing(fn(TrainingAction $record): string => $record->company_name ?? __('Desconocido')),
                        TextEntry::make('trainer_name')
                            ->label(__('Formador'))
                            ->getStateUsing(fn(TrainingAction $record): string => $record->trainer_name ?? __('Desconocido')),
                        TextEntry::make('location')
                            ->label(__('Lugar'))
                            ->getStateUsing(fn(TrainingAction $record): string => $record->location ?? __('Desconocido')),
                        TextEntry::make('attendees')
                            ->label(__('Asistentes'))
                            ->columnSpanFull()
                            ->getStateUsing(function (TrainingAction $record): string {
                                $names = $record->users()->orderBy('name')->pluck('name')->all();

                                return empty($names) ? __('Sin asistentes') : implode(', ', $names);
                            }),
                    ]),
            ]);
        }

        if ($eventClass === Reconocimiento::class) {
            return $schema->components([
                Section::make(__('Detalles'))
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user_name')
                            ->label(__('Usuario'))
                            ->getStateUsing(fn(Reconocimiento $record): string => $record->user?->name ?? __('Desconocido'))
                            ->badge(),
                        TextEntry::make('fecha')
                            ->label(__('Fecha reconocimiento'))
                            ->getStateUsing(fn(Reconocimiento $record): string => $record->fecha?->translatedFormat('d F Y') ?? __('Desconocido'))
                            ->badge(),
                        TextEntry::make('lugar')
                            ->label(__('Lugar'))
                            ->getStateUsing(fn(Reconocimiento $record): string => $record->lugar ?? __('Desconocido'))
                            ->badge(),
                    ]),
            ]);
        }

        return $schema->components([]);
    }

    protected function getEvents(FetchInfo $info): Collection|array|Builder
    {
        $trainingActions = TrainingAction::query()
            ->where(function (Builder $query) use ($info): void {
                $query
                    ->whereBetween('start_date', [$info->start, $info->end])
                    ->orWhereBetween('end_date', [$info->start, $info->end]);
            })
            ->get();

        $disfrutes = Disfrute::query()
            ->whereBetween('fecha_disfrute', [$info->start, $info->end])
            ->get();

        $reconocimientos = Reconocimiento::query()
            ->whereBetween('fecha', [$info->start, $info->end])
            ->get();

        return $trainingActions
            ->merge($disfrutes)
            ->merge($reconocimientos)
            ->map(fn(Eventable $event): CalendarEvent => $event->toCalendarEvent()->allDay());
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
                'timeGridWeek' => __('dayGridWeek'),
                'dayGridMonth' => __('dayGridMonth'),
                'resourceTimeGridDay' => __('resourceTimeGridDay'),
            ],
        ];
    }
}
