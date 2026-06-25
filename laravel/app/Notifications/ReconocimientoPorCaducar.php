<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReconocimientoPorCaducar extends Notification
{
   use Queueable;

    public $usuarios;

    public function __construct($usuarios)
    {

        $this->usuarios = $usuarios;
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

        ->subject('Reconocimientos próximos a caducar')
        ->line('Los siguientes usuarios tienen reconocimientos caducados o próximos a caducar:')
        ->line($this->usuarios->pluck('name')->join(', '))
        ->action('Ver reconocimientos', url('/admin/reconocimientos'))
        ->markdown('vendor.notifications.email', [
            'notifiable' => $notifiable,
        ]);
;
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
