<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Mailable as MailableContract;
use Illuminate\Mail\Mailable as BaseMailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

/**
 * @implements \Illuminate\Contracts\Mail\Mailable
 */
class SoporteMail extends BaseMailable implements MailableContract
{
    use Queueable, SerializesModels;

    public string $asunto;
    public string $descripcion;
    public string $nombreUsuario;
    public string $correoUsuario;
    public string $empresa;
    public $adjunto;

    /**
     * Ahora acepta 6 parámetros.
     */
    public function __construct(
        string $asunto,
        string $descripcion,
        string $nombreUsuario,
        string $correoUsuario,
        string $empresa,
        $adjunto = null
    ) {
        $this->asunto        = $asunto;
        $this->descripcion   = $descripcion;
        $this->nombreUsuario = $nombreUsuario;
        $this->correoUsuario = $correoUsuario;
        $this->empresa       = $empresa;
        $this->adjunto       = $adjunto;
    }


    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto,
            // hace que “Responder” en Jira vaya al usuario que envió el form
            replyTo: [new Address($this->correoUsuario, $this->nombreUsuario)],
        );
    }

    public function content(): Content
    {
        return new Content(
            // Enviamos solo texto plano para que Jira lo muestre prolijo
            text: 'emails.support_text',
            with: [
                'asunto'        => $this->asunto,
                'descripcion'   => $this->descripcion,
                'nombreUsuario' => $this->nombreUsuario,
                'correoUsuario' => $this->correoUsuario,
                'empresa'       => $this->empresa,
                'adjuntos'      => $this->adjunto,
            ],
        );
    }

    public function attachments(): array
    {
        if (! $this->adjunto) {
            return [];
        }

        // Si adjunto es un array de archivos
        $out = [];
        foreach ((array) $this->adjunto as $file) {
            $out[] = Attachment::fromPath($file->getRealPath())
                ->as($file->getClientOriginalName());
        }
        return $out;
    }
}
