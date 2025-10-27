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
use Illuminate\Support\Arr;

class ActivoMensajeMail extends BaseMailable implements MailableContract
{
    use Queueable, SerializesModels;

    public string $asunto;
    public string $mensaje;
    public string $empresa;
    public string $bienNombre;
    public string $bienUrl;
    public string $remitenteNombre;
    public string $remitenteEmail;

    /** @var array<string|\Symfony\Component\HttpFoundation\File\UploadedFile> */
    public array $adjuntos;

    public function __construct(
        string $asunto,
        string $mensaje,
        string $empresa,
        string $bienNombre,
        string $bienUrl,
        string $remitenteNombre,
        string $remitenteEmail,
        array $adjuntos = []
    ) {
        $this->asunto          = $asunto;
        $this->mensaje         = $mensaje;
        $this->empresa         = $empresa;
        $this->bienNombre      = $bienNombre;
        $this->bienUrl         = $bienUrl;
        $this->remitenteNombre = $remitenteNombre;
        $this->remitenteEmail  = $remitenteEmail;
        $this->adjuntos        = $adjuntos;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->asunto,
            replyTo: [new Address($this->remitenteEmail, $this->remitenteNombre)],
        );
    }

    public function content(): Content
    {
        // Mandamos HTML simple + fallback en texto plano
        return new Content(
            view: 'emails.activo_contacto_html',
            with: [
                'asunto'          => $this->asunto,
                'mensaje'         => $this->mensaje,
                'empresa'         => $this->empresa,
                'bienNombre'      => $this->bienNombre,
                'bienUrl'         => $this->bienUrl,
                'remitenteNombre' => $this->remitenteNombre,
                'remitenteEmail'  => $this->remitenteEmail,
            ]
        );
    }

    public function attachments(): array
    {
        // Soporta UploadedFile (HTTP o Livewire) o rutas string
        return collect(Arr::wrap($this->adjuntos))
            ->filter() // saca nulls
            ->map(function ($file) {
                if (is_string($file)) {
                    return Attachment::fromPath($file);
                }

                // UploadedFile / TemporaryUploadedFile
                $path = method_exists($file, 'getRealPath') ? $file->getRealPath() : null;
                $name = method_exists($file, 'getClientOriginalName') ? $file->getClientOriginalName() : 'adjunto';
                $mime = method_exists($file, 'getMimeType') ? $file->getMimeType() : null;

                $att = Attachment::fromPath($path)->as($name);
                return $mime ? $att->withMime($mime) : $att;
            })
            ->all();
    }
}
