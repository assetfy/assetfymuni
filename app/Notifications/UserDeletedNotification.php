<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserDeletedNotification extends Notification
{
    public $user;

    /**
     * Create a new notification instance.
     */
    public function __construct($user)
    {
        $this->user = $user;
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
            ->subject('Cuenta eliminada')
            ->line('Lamentamos informarte que tu cuenta ha sido eliminada exitosamente.')
            ->line('Si tienes alguna pregunta, no dudes en ponerte en contacto con nosotros.')
            ->markdown('emails.delete-user', ['user' => $this->user]);
    }
}
