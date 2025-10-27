<?php

namespace App\Notifications;

use App\Models\OrdenesModel;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class OrdenAsignadaNotification extends Notification
{
    use Queueable;

    public $orden;
    public $asignadoPor;

    /**
     * Crear una nueva instancia de la notificación.
     *
     * @param OrdenesModel $orden
     * @param mixed $asignadoPor Usuario que asigna la orden (por ejemplo, auth()->user())
     */
    public function __construct(OrdenesModel $orden, $asignadoPor)
    {
        $this->orden = $orden;
        $this->asignadoPor = $asignadoPor;
    }

    /**
     * Determinar los canales de envío de la notificación.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        // En este ejemplo, enviamos por correo
        return ['mail'];
    }

    /**
     * Construir el mensaje de correo usando una vista personalizada.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        // Obtenemos el nombre real del representante técnico mediante la relación 'usuarios'
        $nombreTecnico = $notifiable->usuarios->name ?? 'Técnico';

        return (new MailMessage)
            ->subject('Nueva Orden de Trabajo Asignada')
            ->view('emails.orden_asignada', [
                'nombreTecnico' => $nombreTecnico,
                'orden'         => $this->orden,
                'asignadoPor'   => $this->asignadoPor,
                'url'           => route('ordenes'),
            ]);
    }
}
