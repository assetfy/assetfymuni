<?php

namespace App\Livewire\Controles;

use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use App\Models\ControlesModel;
use Livewire\Component;

class CreateControles extends Component
{
    use VerificacionTrait;
    public $open = false;
    public $nombre, $descripcion;
    protected $listeners = ['CreateControles'];


    protected $rules = [
        'nombre' => 'required|max:50',
        'descripcion' => 'required|max:100',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function save()
    {
        $campos = ['nombre'];

        $valoresNuevos = ['nombre' => $this->nombre, 'descripcion' => $this->descripcion];

        $this->create(ControlesModel::class, $campos, $valoresNuevos);

        $this->dispatch('refreshLivewireTable');
    }

    public function close()
    {
        $this->reset(['nombre','descripcion']);
        $this->open = false;
    }

    public function CreateControles(){
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }
}