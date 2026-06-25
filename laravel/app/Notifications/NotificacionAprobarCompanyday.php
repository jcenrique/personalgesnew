<?php

namespace App\Notifications;

use App\Models\Companyday;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificacionAprobarCompanyday extends Notification
{
   use Queueable;

    protected $companyday;

    /**
     * Create a new notification instance.
     */
    public function __construct(Companyday $companyday)
    {
        $this->companyday = $companyday;
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
           ->subject(__('Solicitud de día trabajado aprobada'))
            ->line(__('Su solicitud de día ha sido aprobada. Puede disfrutar de su día de descanso el :fecha_disfrute.', [


                'fecha_disfrute' => $this->companyday->disfrute->fecha_disfrute->translatedFormat('d F Y'),
            ]))
            //la accion debe redirigir al panel de administración de sábados pendientes de aprobación para revisar la solicitud
            ->action(__('Ver días aprobados'), url('/additionaldays?tab=approved'))
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
