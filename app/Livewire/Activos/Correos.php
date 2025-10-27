<?php

// App\Livewire\Activos\Correos.php
namespace App\Livewire\Activos;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Mail;
use App\Mail\ActivoMensajeMail;
use App\Models\EmpresasModel;

class Correos extends Component
{
    use WithFileUploads;

    public bool $open = false;

    // Se elige en el modal:
    public string $asuntoTipo = 'mensaje'; // 'mensaje' | 'falla'
    public string $mensaje = '';

    // Datos del destinatario y del bien:
    public string $email = '';
    public string $etiqueta = '';
    public int    $activoId = 0;
    public string $activoNombre = '';
    public ?string $urlBien = null;

    public array $adjuntos = []; // opcional

    // Invitado
    public bool $esInvitado = true;
    public string $remitenteNombre = '';
    public string $remitenteApellido = '';
    public string $remitenteNombreApellido = '';
    public string $remitenteEmail = '';

    protected $listeners = ['contactar'];

    // Importante: sin type-hint array para evitar "Unable to resolve dependency"
    public function contactar($datos)
    {
        $this->email        = $datos['email']        ?? '';
        $this->etiqueta     = $datos['etiqueta']     ?? '';
        $this->activoId     = (int)($datos['activoId'] ?? 0);
        $this->activoNombre = $datos['activoNombre'] ?? '';
        $this->urlBien      = $datos['urlBien']      ?? null;

        $user = auth()->user();
        $this->esInvitado = !$user;

        if (!$this->esInvitado) {
            // Relleno con datos del usuario autenticado
            $this->remitenteNombre   = $user->name;
            $this->remitenteApellido = ''; // si querés, podés parsear name
            $this->remitenteEmail    = $user->email;
        } else {
            // Invitado: vacío para que complete
            $this->remitenteNombreApellido   = '';
            $this->remitenteApellido = '';
            $this->remitenteEmail    = '';
        }

        // default
        $this->asuntoTipo = 'mensaje';
        $this->mensaje    = '';
        $this->open = true;
    }

    public function cerrar()
    {
        $this->open = false;
        $this->reset(['asuntoTipo', 'mensaje', 'adjuntos']);
    }

    public function enviar()
    {
        $this->remitenteNombreApellido = trim($this->remitenteNombreApellido);
        $this->remitenteEmail = trim($this->remitenteEmail);
        $this->mensaje = trim($this->mensaje);

        $rules = [
            'email'      => ['required', 'email'],
            'asuntoTipo' => ['required', 'in:mensaje,falla'],
            'mensaje'    => ['required', 'string', 'min:3'],
        ];

        if ($this->esInvitado) {
            $rules += [
                'remitenteNombreApellido'   => ['required', 'string', 'min:2'],
                'remitenteEmail'    => ['required', 'email'],
            ];
        }

        $this->validate($rules);

        $user = auth()->user();
        $empresa = ($user && $user->entidad)
            ? (EmpresasModel::where('cuit', $user->entidad)->value('razon_social') ?? '')
            : '';

        $remNombre = $this->esInvitado ? trim($this->remitenteNombreApellido) : $user->name;
        $remEmail  = $this->esInvitado ? $this->remitenteEmail : $user->email;

        $asunto = $this->asuntoTipo === 'falla'
            ? "{$remNombre} ha reportado una falla sobre el bien {$this->activoNombre}"
            : "{$remNombre} le ha enviado un mensaje sobre el bien {$this->activoNombre}";

        Mail::to($this->email)->send(
            (new ActivoMensajeMail(
                asunto: $asunto,
                mensaje: $this->mensaje,
                empresa: $empresa,
                bienNombre: $this->activoNombre,
                bienUrl: (string) $this->urlBien,
                remitenteNombre: $remNombre,
                remitenteEmail: $remEmail,
                adjuntos: $this->adjuntos ?? []
            ))->replyTo($remEmail, $remNombre) // 👈 responder al remitente (invitado)
        );

        $this->dispatch('lucky');
        $this->cerrar();
    }

    public function render()
    {
        return view('livewire.activos.correos');
    }
}
