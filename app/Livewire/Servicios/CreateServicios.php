<?php

namespace App\Livewire\Servicios;
use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use App\Models\ServiciosModel;
use Livewire\Component;

class CreateServicios extends Component
{
    protected $listeners = ['crearServicios'];
    use VerificacionTrait;
    public $servicios,$nombre,$descripcion;
    public $open = false;

    protected $rules = [
        'nombre' => 'required|max:50',
        'descripcion' => 'required|max:100',
    ];
    
    public function updated($propertyName){
        $this->validateOnly($propertyName);
    }

    public function save(){
        $this->validate();
        $this->crearRegistro();
        $this->close();
    }

    private function crearRegistro(){
        $campos = ['nombre', 'descripcion'];
        $valoresNuevos =[
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ];
        $this->create(ServiciosModel::class, $campos, $valoresNuevos);
    }

    public function close(){
        $this->dispatch('refreshLivewireTable');
        $this->reset(['nombre','descripcion']);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.servicios.create-servicios');
    }

    
    public function crearServicios(){
        $this->openModal();
    }

    public function openModal()
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }
}
