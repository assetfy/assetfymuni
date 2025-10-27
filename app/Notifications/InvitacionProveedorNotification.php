<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitacionProveedorNotification extends Notification
{
    protected $proveedor, $usuario;

    /**
     * Crear una nueva instancia de notificación.
     *
     * @param array $proveedor Datos del proveedor
     * @param string $usuario Nombre del usuario que envía la invitación
     */
    public function __construct($proveedor, $usuario)
    {
        $this->proveedor = $proveedor;
        $this->usuario = $usuario; // Inicializar la propiedad $usuario
    }

    /**
     * Obtener los canales de entrega de la notificación.
     *
     * @param  mixed  $notifiable
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Obtener la representación del correo de la notificación.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Invitación a Nuestra Plataforma')
            ->view('emails.invitacion-proveedor', [
                'proveedor' => $this->proveedor,
                'usuario' => $this->usuario, // Pasar la propiedad $usuario a la vista
            ]);
    }
}
