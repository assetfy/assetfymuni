<?php

namespace App\Livewire\Servicios;

use App\Models\SolicitudesServiciosModel;
use App\Services\MiddlewareInvoker;
use Livewire\Component;

class ServicioMotivoRechazo extends Component
{
    public $motivoRechazo, $servicio;
    public $open = false;

    protected $listeners = ['rechazar', 'save'];

    protected $rules = [
        'motivoRechazo' => 'required|max:140',
    ];

    public function rechazar($data)
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            $this->dispatch('cancelar');
            return;
        } else {
            $this->openModal($data);
        }
    }

    public function openModal($value)
    {
        if (is_array($value) && isset($value['servicioId']['id_solicitud'])) {
            $id_solicitud = $value['servicioId']['id_solicitud'];
        } else {
            $id_solicitud = $value;
        }
        $this->servicio = SolicitudesServiciosModel::where('id_solicitud', $id_solicitud)->first();
        if ($this->servicio) {
            $this->open = true;
        }
    }

    public function rechazarServicio()
    {
        $this->dispatch('cancelar');
    }

    public function save()
    {
        $this->validate();
        if(auth()->user()->pane_actual == 'cliente') {
            $this->servicio->estado_presupuesto = 'Rechazado por Cliente';
        } else {
            $this->servicio->estado_presupuesto = 'Rechazado por Prestadora';
        }
        $this->servicio->motivo_cancelacion = $this->motivoRechazo;
        $this->servicio->save();
        $this->dispatch('ServicioEliminado');
        $this->dispatch('refreshLivewireTable');
        $this->close();
    }

    public function close()
    {
        $this->reset('motivoRechazo');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.servicios.servicio-motivo-rechazo');
    }
}
