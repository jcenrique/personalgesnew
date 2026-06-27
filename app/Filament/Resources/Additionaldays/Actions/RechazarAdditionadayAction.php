<?php

namespace App\Filament\Resources\Additionaldays\Actions;

use App\Enum\StatusSolicitudes;
use Carbon\Carbon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Filament\Forms\Components\TextInput;

class RechazarAdditionadayAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel(true);
        $this->tooltip(__('Rechazar día adicional'));
        $this->icon(Heroicon::XCircle);
        $this->color('danger');
        $this->modalHeading(__('Rechazar día adicional trabajado'))
            ->modalDescription(__('Indique el motivo de rechazo.'))
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


        //guardar el motivo de rechazo en la tabla de rechazos utilizasndo la relación entre rechazo y sábado, creando un nuevo registro de rechazo asociado al sábado trabajado que se está rechazando
            $record->rechazos()->create([
                'user_id' =>$record->user_id,
                'razon' => $data['razon'],
                'fecha_disfrute' => $record->disfrute?->fecha_disfrute, // Guardar la fecha de disfrute asociada al rechazo
            ]);

            $record->disfrute->delete();

            $record->save();
            $user = $record->user; // Obtener el usuario asociado al sábado trabajado
            //notifiacar al usuario que su sábado trabajado ha sido aprobado
            $user->notify(new \App\Notifications\NotificacionRechazarAdditionalday($record));

            Notification::make()
                ->title(__('Día adicional rechazado'))
                ->body(__('El día adicional para el :fecha ha sido rechazado.', [

                    'fecha_disfrute' => $record->disfrute->fecha_disfrute->translatedFormat('d F Y'),
                ]))
                 ->danger()
                ->send();
            //notificar por DB al usuario para que pueda ver la notificación en su panel de usuario
            Notification::make()
                ->title(__('Día adicional rechazado'))
                ->body(__('El día adicional para el :fecha ha sido rechazado.', [

                     'fecha_disfrute' => $record->disfrute->fecha_disfrute->translatedFormat('d F Y'),
                ]))
                 ->danger()
                ->sendToDatabase($user);

                    $livewire->dispatch('refresh-sidebar');
        });

    }
}
