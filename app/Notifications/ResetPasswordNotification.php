<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Storage;

class ResetPasswordNotification extends Notification
{
    public $token;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        // Verificar si el usuario está dado de baja
        if ($notifiable->estado == 2) {
            // No enviar el correo si el usuario ha sido dado de baja
            return;
        }

        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('Restablecimiento de contraseña')
            ->line('Hemos recibido una solicitud para restablecer tu contraseña. Haz clic en el botón de abajo para restablecer tu contraseña. Este enlace expirará en 24 horas.')
            ->action('Resetear contraseña', $resetUrl)
            ->markdown('emails.reset_password', ['resetUrl' => $resetUrl]);
    }
}