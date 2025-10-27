<?php

namespace App\Livewire\General;

use App\Services\MiddlewareInvoker;
use App\Models\EstadoGeneralModel;
use App\Traits\SortableTrait;
use Livewire\Component;

class CreateEstadoGeneral extends Component
{
    use SortableTrait;
    public $open = false;
    public $nombre, $descripcion;

    protected $listeners = ['CreateEstadogeneral'];

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
        $this->validar();
        $this->eventos();
        $this->close();
    }

    public function render()
    {
        return view('livewire.estadogeneral.create-estado-general');
    }

    public function close(){
        $this->reset(['nombre','descripcion']);
        $this->open = false;
     }

    private function registro(){
        EstadoGeneralModel::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ]);
    }

    private function validar()
    {
        $this->validate( 
        ['nombre' => 'required|max:50',
        'descripcion' => 'required|max:100']);

        $this->registro();
    }

    public function CreateEstadogeneral(){
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }
}
