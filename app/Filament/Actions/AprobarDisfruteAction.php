<?php

namespace App\Filament\Actions;

use App\Enum\StatusSolicitudes;
use App\Notifications\NotificacionAprobarDia;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;

class AprobarDisfruteAction extends Action
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->hiddenLabel(true);
        $this->tooltip(__('Aprobar día solicitado para que el usuario pueda disfrutar de día de descanso'));
        $this->icon('far-calendar-check');
        $this->color('success');
        $this->modalHeading(__('Aprobar día solicitado'))
            ->modalDescription(__('Puedes aprobar para que el usuario pueda disfrutar de su día de descanso'))
            ->modalWidth('md')
            ->modalSubmitActionLabel(__('Aprobar'))
            ->modalIcon(Heroicon::CalendarDays);
        // solo hay que aprobar el sábado trabajado, no es necesario seleccionar la fecha porque ya está registrada en la solicitud, por lo que se puede mostrar como información en el modal y cambiar el estado a aprobado al confirmar la acción
        $this->schema([
            // Aquí puedes agregar campos adicionales para la solicitud, si es necesario
        ]);
        $this->action(function ($record, $data, $livewire) {
            // Cambiar el estado del sábado a aprobado

            $record->status = StatusSolicitudes::Aprobado;
            $record->save();
            $user = $record->user; // Obtener el usuario asociado al sábado trabajado
            // notifiacar al usuario que su sábado trabajado ha sido aprobado
            $user->notify(new NotificacionAprobarDia($record));

            Notification::make()
                ->title(__('Día  aprobado'))
                ->body(__('El día ha sido aprobado. El usuario podrá disfrutar de su día de descanso el :fecha_disfrute.', [

                    'fecha_disfrute' => $record->fecha_disfrute->translatedFormat('d M Y'),
                ]))
                ->success()
                ->send();
            // notificar por DB al usuario para que pueda ver la notificación en su panel de usuario
            Notification::make()
                ->title(__('Día aprobado'))
                ->body(__('El día ha sido aprobado. El usuario podrá disfrutar de su día de descanso el :fecha_disfrute.', [
                    'fecha_disfrute' => $record->fecha_disfrute->translatedFormat('d M Y'),
                ]))
                ->success()
                ->sendToDatabase($user);

            $livewire->dispatch('refresh-sidebar');

        });

    }
}
