<?php

namespace App\Notifications;

use App\Models\Sabado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificacionSolicitudSabado extends Notification
{
    use Queueable;

    private Sabado $sabado;

    /**
     * Create a new notification instance.
     */
    public function __construct(Sabado $sabado)
    {
        $this->sabado = $sabado;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {


        return (new MailMessage)
         ->subject(__('Nueva solicitud de sábado de descanso'))
            ->line(__('El usuario :name ha solicitado el sábado :fecha, para disfrutar de su día de descanso el :fecha_disfrute.', [
                'name' => $this->sabado->user->name,
                'fecha' => $this->sabado->sabado_trabajado->translatedFormat('d F Y'),
                'fecha_disfrute' => $this->sabado->disfrute->fecha_disfrute->translatedFormat('d F Y'),
            ]))
            //la accion debe redirigir al panel de administración de sábados pendientes de aprobación para revisar la solicitud
            ->action(__('Aprobar sábado trabajado'), url('admin/sabados/?tab=requested'))
            ->line(__('Gracias por usar nuestra aplicación!'))
            ->markdown('vendor.notifications.email', [
            'notifiable' => $notifiable,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
