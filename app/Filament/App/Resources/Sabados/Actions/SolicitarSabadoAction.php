<?php

namespace App\Filament\App\Resources\Sabados\Actions;

use App\Enum\StatusSolicitudes;
use App\Models\User;
use App\Notifications\NotificacionSolicitudSabado;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class SolicitarSabadoAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel(true);
        $this->tooltip(__('Solicitar sábado'));
        $this->icon('far-calendar-plus');
        $this->color('primary');
        $this->modalHeading(__('Solicitar sábado de descanso'))
            ->modalDescription(__('Selecciona la fecha en la que deseas disfrutar tu sábado de descanso.'))
            ->modalWidth('md')
            ->modalIcon(Heroicon::CalendarDays);
        $this->schema([
            DatePicker::make('fecha_disfrute')
                ->label(__('Fecha de disfrute'))
                ->required()
                ->native(false)
                ->locale('es')
                ->format('Y-m-d')
                ->displayFormat('d M Y')
                ->closeOnDateSelection()
                ->disabledDates(function () {
                    $start = Carbon::now()->addYear(-10)->startOfMonth();
                    $end = Carbon::now();

                    $period = CarbonPeriod::create($start, $end);
                    $disabled = [];

                    foreach ($period as $date) {
                        // desabilitar fechas anteriores a la actual
                        if ($date->isPast()) {
                            $disabled[] = $date->translatedFormat('Y-m-d');
                        }
                    }
                    // desabilitar fechas ya solicitadas
                    $diasSolicitados = Auth::user()->disfrutes()->get();

                    foreach ($diasSolicitados as $dia) {

                        $disabled[] = Carbon::parse($dia->fecha_disfrute)->translatedFormat('Y-m-d');
                    }

                    return $disabled;
                }),

        ]);
        $this->requiresConfirmation();

        $this->action(function ($record, $data, $livewire) {

            $record->disfrute()->create(['fecha_disfrute' => $this->data['fecha_disfrute'], 'user_id' => $record->user_id, 'status' => StatusSolicitudes::Solicitado]);
            $record->refresh();

            $admins = User::withoutGlobalScopes()->notifiable()->role('admin')->get();
            Notification::make()
                ->title(__('Solicitud enviada'))
                ->success()
                ->send();
            // notificar al admin por correo
            Notification::make()
                ->title(__('Nuevo sábado solicitado'))
                ->body(__('new_saturday_requested', ['user' => $record->user->name, 'sabado' => Carbon::parse($record->sabado_trabajado)->translatedFormat('d F Y'), 'dia' => Carbon::parse($record->disfrute->fecha_disfrute)->translatedFormat('d F Y')]))
                ->success()
                ->sendToDatabase($admins);
            FacadesNotification::send($admins, new NotificacionSolicitudSabado($record));

            $livewire->dispatch('refresh-sidebar');

        });
    }

    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'solicitar_sabado');
    }
}
