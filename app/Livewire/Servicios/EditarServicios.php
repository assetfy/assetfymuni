<?php

namespace App\Livewire\Servicios;

use App\Models\ServiciosModel;
use Livewire\Attributes\On;
use Livewire\Component;

class EditarServicios extends Component
{
    public $nombre, $descripcion, $servicio, $updatedNombre, $updatedDescripcion;
    public $open = false;
    public $servicios;
    protected $listeners = ['openModal'];

    public function mount(ServiciosModel $value)
    {
        $this->servicio = ServiciosModel::where('id_servicio', $value->id_servicio)->first();
        $this->servicio = $value;
        $this->updatedNombre = $value->nombre;
        $this->updatedDescripcion = $value->descripcion;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public $rules =
    [
        'updatedNombre' => 'required|max:50',
        'updatedDescripcion' => 'required|max:100',
    ];

    public function openModal($data)
    {
        $this->servicios =  ServiciosModel::find($data['serviciosId']);
        if ($this->servicios) {
            $this->mount($this->servicios);
            $this->actualizarServicios();
            $this->open = true;
        }
    }

    public function guardarCambios()
    {
        $this->dispatch('check');
    }

    #[On('guardado')]
    public function actualizar()
    {
        $this->actualizarServicios();
        $this->dispatch('lucky'); 
    }

    private function actualizarServicios()
    {
        $this->validate();

        $this->servicio->nombre = $this->updatedNombre;
        $this->servicio->descripcion = $this->updatedDescripcion;

        $this->servicio->save();
        $this->close();
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.servicios.editar-servicios');
    }
}
