<?php

namespace App\Filament\Resources\Computos\RelationManagers\Actions;

use App\Enum\StatusSolicitudes;
use App\Models\Computo;
use App\Models\Sabado;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Dom\Text;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification as FacadesNotification;

class RechazarDisfrute extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel(true);
        $this->tooltip(__('Rechazar día de computo'));
        $this->icon('far-calendar-times');
        $this->color('danger');
        $this->modalHeading(__('Rechazar día de computo'))
            ->modalDescription(__('Puedes rechazar para que el usuario no pueda disfrutar de su día de descanso'))
            ->modalWidth('md')
            ->modalIcon(Heroicon::CalendarDays);
        //solo hay que aprobar el sábado trabajado, no es necesario seleccionar la fecha porque ya está registrada en la solicitud, por lo que se puede mostrar como información en el modal y cambiar el estado a aprobado al confirmar la acción
        $this->schema([
            TextInput::make('razon')
                ->label(__('Motivo rechazo'))
                ->required()
            // Aquí puedes agregar campos adicionales para la solicitud, si es necesario
        ]);
        $this->action(function ($record, $data, $livewire) {

            //obtener el registr padre de computos para obtener el año del computo y luego obtener el registro de disfrute asociado al computo y cambiar su estado a rechazado, para esto se puede usar la relacion morph entre computo y disfrute, primero se obtiene el registro de computo usando el metodo getRecord() y luego se obtiene el registro de disfrute asociado a ese computo usando la relacion morph, finalmente se cambia el estado del disfrute a rechazado y se guarda el registro de disfrute
            $computo = Computo::find($record->disfrutable_id);


            //guardar el motivo de rechazo en la tabla de rechazos utilizasndo la relación entre rechazo y sábado, creando un nuevo registro de rechazo asociado al sábado trabajado que se está rechazando
            $computo->rechazos()->create([
                'user_id' => $record->user_id,
                'razon' => $data['razon'],
                'fecha_disfrute' => $record->fecha_disfrute, // Guardar la fecha de disfrute asociada al rechazo
            ]);

            $user = $record->user; // Obtener el usuario asociado al sábado trabajado

            //eleimar el registro del sábado de la tabla dsifrute para que el usuario no pueda disfrutarlo
            $record->delete();
            // Cambiar el estado del sábado a aprobado

            $computo->refresh(); // Refrescar el registro del computo para que se vuelva a calcular el número de minutos disponibles con la eliminación del disfrute rechazado
            Notification::make()
                ->title(__('Día de computo rechazado'))
                ->body(__('El día de computo  ha sido rechazado. El usuario no podrá disfrutar de su día de descanso el :fecha_disfrute.', [

                    'fecha_disfrute' => $record->fecha_disfrute->translatedFormat('d F Y'),
                ]))
                ->success()
                ->send();
            //notificar por DB al usuario para que pueda ver la notificación en su panel de usuario
            Notification::make()
                ->title(__('Día de computo rechazado'))
                ->body(__('El día de computo ha sido rechazado.'))
                ->success()
                ->sendToDatabase($user);

            $record->refresh();
            $livewire->dispatch('refresh-sidebar');
        });
    }
}
