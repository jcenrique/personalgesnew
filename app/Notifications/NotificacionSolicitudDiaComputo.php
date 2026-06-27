<?php

namespace App\Notifications;

use App\Models\Computo;
use App\Models\Disfrute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificacionSolicitudDiaComputo extends Notification
{
    use Queueable;

        protected $dia_disfrute;
        protected $computo;

    /**
     * Create a new notification instance.
     */
    public function __construct( Disfrute $dia_disfrute, Computo $computo)
    {
        $this->dia_disfrute = $dia_disfrute;
        $this->computo = $computo;
    }
        //


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
           ->subject(__('Nueva solicitud de día a cuenta del Computo'))
            ->line(__('El usuario :name ha solicitado un día a cuenta del computo para disfrutar de un día de descanso el :fecha_disfrute.', [
                'name' => $this->dia_disfrute->user->name,
                'fecha_disfrute' => $this->dia_disfrute->fecha_disfrute->translatedFormat('d F Y'),
            ]))
            //la accion debe redirigir al panel de administración de sábados pendientes de aprobación para revisar la solicitud
            ->action(__('Aprobar día exceso de Computo'), url("admin"))
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
