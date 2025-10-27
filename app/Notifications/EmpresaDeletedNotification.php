<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EmpresaDeletedNotification extends Notification
{
    public $empresa;

    /**
     * Create a new notification instance.
     */
    public function __construct($empresa)
    {
        $this->empresa = $empresa;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Empresa eliminada')
            ->line('Lamentamos informarte que la cuenta de empresa que eras parte ha sido eliminada exitosamente.')
            ->line('Si tienes alguna pregunta, no dudes en ponerte en contacto con nosotros.')
            ->markdown('emails.delete-empresa', ['empresa' => $this->empresa]);
    }
}
