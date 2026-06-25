<?php

namespace App\Notifications;


use App\Models\Disfrute;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotificacionAprobarDia extends Notification
{
    use Queueable;

    protected $dia;

    /**
     * Create a new notification instance.
     */
    public function __construct(Disfrute $dia)
    {
        $this->dia = $dia;
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
        $tipo_recurso ='';
        if ($this->dia->disfrutable_type === 'App\Models\Sabado') {
            $tipo_recurso ='/sabados?tab=approved';
        } else if ($this->dia->disfrutable_type === 'App\Models\Additionalday') {
            $tipo_recurso = '/additionaldays?tab=approved';
        } else if ($this->dia->disfrutable_type === 'App\Models\Computo') {
            $tipo_recurso = '/computos';
        }

        return (new MailMessage)
           ->subject(__('Solicitud de día de descanso aprobada'))
            ->line(__('Su solicitud de día de descanso ha sido aprobada. Puede disfrutar de su día de descanso el :fecha_disfrute.', [


                'fecha_disfrute' => $this->dia->fecha_disfrute->translatedFormat('d F Y'),
            ]))
            //la accion debe redirigir al panel de administración de sábados pendientes de aprobación para revisar la solicitud
            ->action(__('Ver días adicionales aprobados'), url($tipo_recurso))
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
