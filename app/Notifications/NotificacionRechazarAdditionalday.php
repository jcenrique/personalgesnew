<?php

namespace App\Notifications;

use App\Models\Additionalday;
use App\Models\Sabado;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;


class NotificacionRechazarAdditionalday extends Notification
{
    use Queueable;

     protected $additionalday;

    /**
     * Create a new notification instance.
     */
    public function __construct(Additionalday $additionalday)
    {
        $this->additionalday = $additionalday;
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
             ->subject(__('Solicitud de día adicional ha sido rechazada'))
            ->line(__('Su solicitud de día adicional para el :fecha_disfrute ha sido rechazada.', [


                'fecha_disfrute' => $this->additionalday->disfrute->fecha_disfrute->translatedFormat('d F Y'),
            ]))
            ->line(__('Motivo:'))
            ->line( $this->additionalday->rechazos()->latest()->first()->razon)
            //la accion debe redirigir al panel de administración de sábados pendientes de aprobación para revisar la solicitud
            ->action(__('Ver dias rechazados'), url('/rechazos'))
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
