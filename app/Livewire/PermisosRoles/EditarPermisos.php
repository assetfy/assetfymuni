<?php

namespace App\Livewire\PermisosRoles;

use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use App\Models\PermisosModel;
use Livewire\Component;

class EditarPermisos extends Component
{
    use VerificacionTrait;
    public $nombre, $id_permisos, $permisos, $updatedNombre, $open;
    protected $listeners = ['openEditarPermisos'];
    protected $rules = [
        'updatedNombre' => 'required|max:10|min:3',
    ];

    public function actualizar()
    {
        $this->actualizarPermiso();
        $this->close();
    }

    protected function actualizarPermiso()
    {
        $campos = ['nombre'];

        $valoresActualizados = ['nombre' => $this->updatedNombre];

        $this->verificar($this->permisos, $campos, $valoresActualizados);

        $this->dispatch('refreshLivewireTable');
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.permisos-roles.editar-permisos');
    }

    public function openEditarPermisos($data)
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->openModal($data);
        }
    }

    public function openModal($value)
    {
        $this->permisos = PermisosModel::where('id_permiso', $value)->first();
        if ($this->permisos) {
            $this->cargarDatos($this->permisos);
            $this->open = true;
        }
    }

    private function cargarDatos($permisos)
    {
        $this->permisos = $permisos;
        $this->updatedNombre = $this->permisos->nombre;
    }
}
