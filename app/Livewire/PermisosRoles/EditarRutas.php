<?php

namespace App\Livewire\Permisosroles;

use App\Models\RutasModel;
use App\Services\MiddlewareInvoker;
use Livewire\Component;

class EditarRutas extends Component
{
    public $open;
    public $datosRuta;
    public $nombre;
    public $ruta;
    public $configurable;

    protected $listeners = ['EditarRutas'];

    public function EditarRutas($data)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        $this->cargarDatos($data);
        $this->open = true;
    }

    private function cargarDatos($data)
    {
        $ruta = RutasModel::find($data);
        $this->datosRuta = $ruta;
        // Asignamos a propiedades separadas
        $this->nombre = $ruta->nombre;
        $this->ruta   = $ruta->ruta;
        $this->configurable = $ruta->configurable;
    }

    public function actualizar()
    {
        // Actualizamos el modelo con las propiedades editadas
        $this->datosRuta->nombre = $this->nombre;
        $this->datosRuta->ruta = $this->ruta;
        $this->datosRuta->configurable = $this->configurable;
        $this->datosRuta->save();

        $this->dispatch('refreshLivewireTable');
        $this->dispatch('lucky');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.permisosroles.editar-rutas');
    }
}
