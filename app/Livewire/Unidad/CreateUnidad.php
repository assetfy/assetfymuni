<?php

namespace App\Livewire\Unidad;

use App\Traits\VerificacionTrait;
use App\Services\MiddlewareInvoker;
use App\Models\UnidadModel;
use Livewire\Component;
class CreateUnidad extends Component
{
    use VerificacionTrait;
    public $nombre;
    public $unidad;
    public $search = "";
    public $open = false;
    protected $listeners = ['crearUnidadMedida'];

    protected $rules = [
        'nombre' => 'required|max:50|min:2'
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {        
        $this->registro();
    }

    protected function registro()
    {
        $campos = ['nombre'];

        $valoresNuevos = ['nombre' => $this->nombre];

        $this->create(UnidadModel::class, $campos, $valoresNuevos);

        $this->dispatch('refreshLivewireTable');
    }
    
    public function close()
    {
        $this->reset(['nombre']);
        $this->open = false;
    }

    public function crearUnidadMedida(){
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }
}