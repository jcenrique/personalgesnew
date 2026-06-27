<?php

namespace App\Filament\App\Resources\Sabados\Actions;

use App\Models\Sabado;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class AnotarSabadoAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel(true);
        $this->tooltip(__('Anotar sábado trabajado para disfrutar de día de descanso en SAP'));
        $this->icon('far-calendar-plus');
        $this->color('warning');
        $this->modalHeading(__('Anotar sábado trabajado'))
            ->modalDescription(__('Selecciona la fecha en la que deseas anotar tu sábado trabajado.'))
            ->modalWidth('md')
            ->modalIcon(Heroicon::CalendarDays);
        $this->schema([
            // Aquí puedes agregar campos adicionales para la solicitud, si es necesario
            DatePicker::make('sabado_trabajado')
                ->label(__('Fecha del Sábado'))

                ->native(false)
                //->format('Y-m-d')
                ->locale('es')
                ->format('Y-m-d')
                ->displayFormat('d F Y')

                ->closeOnDateSelection()
                ->disabledDates(function () {
                    // Rango razonable para escoger fechas (por ejemplo 1 año)
                    $start = Carbon::now()->addMonths(-2)->startOfMonth();
                    $end   = Carbon::now()->addMonths(12)->endOfMonth();

                    $period = CarbonPeriod::create($start, $end);

                    $disabled = [];

                    foreach ($period as $date) {
                        if (!$date->isSaturday()) {    // ❌ deshabilitar todo lo que NO sea sábado
                            $disabled[] = $date->translatedFormat('Y-m-d');
                        }
                        //también  deshabilitar sábados ya registrados por el usuario
                        if ($date->isSaturday() && Sabado::where('user_id', Auth::id())->where('sabado_trabajado', $date->translatedFormat('Y-m-d'))->exists()) {
                            $disabled[] = $date->translatedFormat('Y-m-d');
                        }

                        //Deshabilitar los dias hasta la fecha actual menos una semana
                        if ($date->isBefore(Carbon::now()->addWeek(-1))) {
                            $disabled[] = $date->translatedFormat('Y-m-d');
                        }

                        //Deshabilitar los dias despues de 2 semanas a partir de la fecha actual
                        if ($date->isAfter(Carbon::now()->addWeeks(5))) {
                            $disabled[] = $date->translatedFormat('Y-m-d');
                        }
                    }

                    return $disabled;
                })

                ->required(),


        ]);

        $this->requiresConfirmation();

        $this->action(function (array $data , $livewire) {

            //validar que el usuario no tenga ya un sábado registrado en la misma fecha
            if (Sabado::where('user_id', Auth::id())->where('sabado_trabajado', $data['sabado_trabajado'])->exists()) {
                // Aquí puedes mostrar un mensaje de error o simplemente retornar sin hacer nada
                // Por ejemplo, usando una notificación de Filament:
                \Filament\Notifications\Notification::make()
                    ->title(__('Error'))
                    ->body(__('Ya tienes un sábado registrado para esta fecha.'))
                    ->danger()
                    ->send();

                return;
            }
            //validar que la fecha es un sabado
            $fecha = Carbon::parse($data['sabado_trabajado']);
            if (!$fecha->isSaturday()) {
                \Filament\Notifications\Notification::make()
                    ->title(__('Error'))
                    ->body(__('La fecha seleccionada no es un sábado.'))
                    ->danger()->send();
                return;
            }

            // Lógica para crear una nueva solicitud de sábado
            $sabado = \App\Models\Sabado::create([
                'sabado_trabajado' => $data['sabado_trabajado'],

                'user_id' => Auth::id(),

                //'fecha_disfrute' => $data['fecha_disfrute'],
            ]);
            //enviar notificacion de correo al admin para revisar la solicitud
            $admins = User::withoutGlobalScopes()->notifiable()->role('admin')->get();

            Notification::make()
                ->title(__('Solicitud enviada'))
                ->success()
                ->send();
            //notificar al admin por correo
            Notification::make()
                ->title(__('Anotar sábado en SAP'))
                ->body(__('user_requested_saturday', ['user' => Auth::user()->name, 'sabado' =>  Carbon::parse($data['sabado_trabajado'])->translatedFormat('d F Y')]))
                ->success()
                ->sendToDatabase($admins);
            FacadesNotification::send($admins, new \App\Notifications\NotificacionAnotarSabado($sabado));
            // ...

            $livewire->dispatch('refresh-sidebar');


        });
    }

    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'anotar_sabado');
    }
}
