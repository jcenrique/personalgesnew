<?php

namespace App\Filament\Resources\Sabados\Actions;


use App\Enum\StatusSolicitudes;
use Filament\Actions\Action;

use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

use Filament\Forms\Components\TextInput;

class RechazarSabadoAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel(true);
        $this->tooltip(__('Rechazar sábado'));
        $this->icon(Heroicon::XCircle);
        $this->color('danger');
        $this->modalHeading(__('Rechazar sábado trabajado'))
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
            //elimimar el registro del sábado de la tabla dsifrute para que el usuario no pueda disfrutarlo

            $record->disfrute->delete();



            $record->save();
            $user = $record->user; // Obtener el usuario asociado al sábado trabajado
            //notifiacar al usuario que su sábado trabajado ha sido aprobado
            $user->notify(new \App\Notifications\NotificacionRechazarSabado($record));

            Notification::make()
                ->title(__('Sábado trabajado rechazado'))
                ->body(__('El sábado trabajado para el :fecha ha sido rechazado.', [
                    'fecha' => $record->sabado_trabajado->translatedFormat('d F Y'),
                    'fecha_disfrute' => $record->rechazos()->latest()->first()->fecha_disfrute->translatedFormat('d F Y'),
                ]))
                ->danger()
                ->send();
            //notificar por DB al usuario para que pueda ver la notificación en su panel de usuario
            Notification::make()
                ->title(__('Sábado trabajado rechazado'))
                ->body(__('El sábado trabajado para el :fecha ha sido rechazado.', [
                    'fecha' => $record->sabado_trabajado->translatedFormat('d F Y'),
                     'fecha_disfrute' => $record->rechazos()->latest()->first()->fecha_disfrute->translatedFormat('d F Y'),
                ]))
                 ->danger()
                ->sendToDatabase($user);

                     $livewire->dispatch('refresh-sidebar');


        });

    }
}
