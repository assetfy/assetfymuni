<?php

namespace App\Livewire\Altas;

use App\Services\MiddlewareInvoker;
use App\Models\EstadosAltasModel;
use App\Traits\SortableTrait; 
use Livewire\Component;

class EditarEstadosAltas extends Component
{
    protected $listeners = ['guardado' => 'actualizarAlta', 'cerrar' => 'cerrarModal','editarEstadoAlta'];
    use SortableTrait; 
    public $open = false;
    public $altas;
    public $updatedNombre,$value;
    public $updatedDescripcion;

    protected $rules = [
        'updatedNombre' => 'required|max:30',
        'updatedDescripcion' =>  'required|max:30',
    ];


    public function     editarEstadoAlta($data){
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
        $this->value = EstadosAltasModel::find($value);
        if($this->value) {
            $this->mount($this->value);
            $this->open = true;
        }
    }

    public function mount(EstadosAltasModel $value){
        $this->altas = $value;
        $this->updatedNombre = $value->nombre;
        $this->updatedDescripcion = $value->descripcion;
    }

    public function actualizarAlta()
    {
        $this->validate();
        $this->altas->nombre = $this->updatedNombre;
        $this->altas->descripcion = $this->updatedDescripcion;
        // Guardar cambios en la base de datos
        $this->altas->save();
        // Cerrar el modal después de la actualización
        $this->dispatch('dataUpdated');
        $this->eventos();
    }

    public function cerrarModal()
    {
        $this->open = false;
    }
    
    public function render()
    {
        return view('livewire.Estados_Alta.editar-estados-altas');
    }
}