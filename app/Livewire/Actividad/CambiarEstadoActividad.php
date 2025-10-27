<?php

namespace App\Livewire\Actividad;

use App\Models\ActividadesEconomicasModel;
use App\Services\MiddlewareInvoker;
use App\Traits\SortableTrait;
use Livewire\WithFileUploads; 
use Livewire\Attributes\On;
use Livewire\Component;

class CambiarEstadoActividad extends Component
{
    use SortableTrait;
    use WithFileUploads;

    public $estado, $actividad, $updateEstado, $updatedNombre, $updatedDescripcion;
    public $estadoActividad,$logo;
    public $open = false;
    protected $listeners = ['EditarEstadoActividad'];

    protected $rules = [
        'updateEstado' => 'required',
        'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg',
    ];

    public function EditarEstadoActividad($data)
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->openModal($data);
        }
    }

    public function openModal($data){
        $this->estadoActividad = ActividadesEconomicasModel::find($data);
        if ($this->estadoActividad) {
            $this->mount($this->estadoActividad);
            $this->open = true;
        }
    }

    public function mount(ActividadesEconomicasModel $value)
    {
        $this->actividad = $value;
        $this->updateEstado = $value->estado;
        $this->updatedNombre = $value->nombre;
        $this->updatedDescripcion = $value->descripcion;
    }

    public function guardarCambios()
    {
        $this->dispatch('check');
    }

    #[On('guardado')]
    public function actualizar()
    {
        $this->actualizarEstado();
        $this->dispatch('lucky'); 
    }

    protected function actualizarEstado()
    {
        $this->validate();
        $filename = $this->logo->store('logos', 'public');
        $rutaFoto = $filename;  
        $this->actividad->estado = $this->updateEstado;
        $this->actividad->logo = $rutaFoto;
        $this->actividad->save();
        $this->dispatch('refreshLivewireTable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.actividad.cambiar-estado-actividad');
    }
}
