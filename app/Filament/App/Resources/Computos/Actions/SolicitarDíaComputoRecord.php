<?php

namespace App\Filament\App\Resources\Computos\Actions;

use App\Enum\StatusSolicitudes;
use App\Models\User;
use App\Notifications\NotificacionSolicitudDiaComputo;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TimePicker;
use Filament\Notifications\Notification;
use Filament\Support\Enums\Alignment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class SolicitarDíaComputoRecord extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel(true);
        $this->tooltip(__('Solicitar día de computo'));
        $this->icon('far-calendar-plus');

        $this->color('primary');
        $this->modalSubmitActionLabel(__('Enviar solicitud'));
        $this->modalFooterActionsAlignment(Alignment::Right);
        $this->modalHeading(__('Solicitar día de computo'))
            ->modalDescription(__('Selecciona la fecha en la que deseas disfrutar tu día de computo.'))
            ->modalWidth('md')
            ->modalIcon('far-calendar-plus');
        $this->schema([
            DatePicker::make('fecha_disfrute')
                ->label(__('Fecha de disfrute'))
                ->required()
                ->native(false)
                ->suffixIcon('heroicon-o-calendar')
                ->locale('es')
                ->format('Y-m-d')
                ->displayFormat('d F Y')
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

            TimePicker::make('minutos_solicitados')
                ->label(__('Horas solicitadas'))
                ->required()
                ->native(false)
                // limitar maximo de horas a solicitar a 9 horas
                ->maxDate(Carbon::createFromTime(9, 0, 0))

                ->suffixIcon('heroicon-o-clock')
                ->seconds(false)
                ->locale('es')
                ->format('H:i')
                ->displayFormat('H:i'),

        ]);
        // $this->requiresConfirmation();

        $this->action(function ($record, $data, $livewire) {

            // no permitir solicitar con 00:00 horas y con un valor inferior a 6:00 horas e inferior a 9:00 horas
            if ($data['minutos_solicitados'] == '00:00' || $data['minutos_solicitados'] < '06:00' || $data['minutos_solicitados'] > '09:00') {

                Notification::make()
                    ->title(__('Selecciona una cantidad de horas válida superando las 6 horas'))->icon('heroicon-o-x-circle')->danger()->send();

                return;
            }

            $minutos_solicitados = (Carbon::parse($data['minutos_solicitados'])->hour * 60) + (Carbon::parse($data['minutos_solicitados'])->minute);
            // antes de crear el disfrute, verificar que el usuario tenga minutos disponibles para solicitar el día de computo, para esto se debe restar los minutos solicitados a los minutos disponibles del computo, si el resultado es negativo, mostrar una notificación de error y no crear el disfrute, si el resultado es positivo o cero, crear el disfrute normalmente
            // para poder solicitar minimo debes disponer de la menos 3 hora 30 minutos para solicitar un día de computo, esto es para evitar que los usuarios soliciten días de computo sin tener suficientes minutos disponibles, para esto se puede hacer una validación antes de crear el disfrute, si el usuario no tiene al menos 30 minutos disponibles, mostrar una notificación de error y no crear el disfrute

            $min_disponibles = $record->disponible;
            $min_solicitados = $record->disfrutes()->sum('minutos_solicitados');
            $restantes = $min_disponibles - $min_solicitados - $minutos_solicitados;
            if ($restantes < -210) {
                Notification::make()
                    ->title(__('No tienes suficientes minutos disponibles'))
                    ->body(__('Dispones de :minutos minutos disponibles, has excedido el límite de solicitud de días de computo. Para solicitar un día de computo debes disponer de al menos 3 horas y 30 minutos disponibles.'))
                    ->icon('heroicon-o-x-circle')
                    ->danger()
                    ->send();

                return;
            }

            $dia_solicitado = $record->disfrutes()->create([
                'fecha_disfrute' => $this->data['fecha_disfrute'],
                'user_id' => $record->user_id,
                'status' => StatusSolicitudes::Solicitado,
                'minutos_solicitados' => $minutos_solicitados,
            ]);

            Notification::make()
                ->title(__('Solicitud enviada'))
                ->icon('heroicon-o-document-text')

                ->success()
                ->send();
            // notificar al admin por correo

            $record->refresh();
            $admins = User::withoutGlobalScopes()->notifiable()->role('admin')->get();
            Notification::make()
                ->title(__('Nuevo día de computo solicitado'))
                ->body(__('Nuevo día de computo solicitado por :user para el :fecha_para_disfrute.', ['user' => $record->user->name, 'fecha_para_disfrute' => Carbon::parse($data['fecha_disfrute'])->translatedFormat('d F Y')]))
                ->success()
                ->sendToDatabase($admins);
            FacadesNotification::send($admins, new NotificacionSolicitudDiaComputo($dia_solicitado, $record));
            // actualiza el registro del computo para reflejar el nuevo número de minutos disponibles, esto se puede hacer refrescando el registro del computo para que se vuelva a calcular el número de minutos disponibles con la nueva solicitud de día de computo

            // actulaizar el sidebar para reflejar el nuevo número de minutos disponibles
            $livewire->dispatch('refresh-sidebar');

            $livewire->dispatch('updateData', $this->data); // enviar un evento a la infolist para que se actualice el número de minutos disponibles en el sidebar
            // volver a recargar la pagina para que se actualice el número de minutos disponibles en la tabla, esto se puede hacer redireccionando a la misma página después de enviar la solicitud

        });
    }

    public static function make(?string $name = null): static
    {

        return parent::make($name ?? 'solicitar_dia_computo');
    }
}
