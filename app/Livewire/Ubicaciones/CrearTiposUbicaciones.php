<?php

namespace App\Livewire\Ubicaciones;
use App\Models\TiposUbicacionesModel;
use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use App\Traits\SortableTrait;
use Livewire\Component;

class CrearTiposUbicaciones extends Component
{
    use VerificacionTrait;
    use SortableTrait;
    public $open = false;
    public $nombre;

    protected $listeners = ['crearTipoUbicacion'];
    protected $rules = [
        'nombre' => 'required|max:30',
    ];

    public function crearTipoUbicacion()
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }

    public function updated($propertyName){
        $this->validateOnly($propertyName);
    }

    public function save(){
        $this->validate();
        $this->crearRegistro();
        $this->close();
    }

    private function crearRegistro(){
        $campos = ['nombre'];
        $valoresNuevos =[
            'nombre' => $this->nombre,
        ];
        $this->create(TiposUbicacionesModel::class, $campos, $valoresNuevos);
    }

    public function render()
    {
        return view('livewire.ubicaciones.crear-tipos-ubicaciones');
    }
    
    public function close(){
        $this->reset(['nombre']);
        $this->dispatch('refreshLivewireTable');
        $this->open = false;
     }
}
