<?php

namespace App\Livewire\Actividad;

use App\Models\ActividadesEconomicasModel;
use App\Traits\VerificacionTrait;
use App\Traits\SortableTrait; 
use Livewire\Component;

class CargarActividad extends Component
{
    use SortableTrait; 
    use VerificacionTrait;
    public $COD_ACTIVIDAD, $Nombre, $Descripcion, $open = false;
    
    protected $rules = [
        'COD_ACTIVIDAD' => 'required',
        'Nombre' => 'required',
        'Descripcion' => 'required',
        'estado' => 'required',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.actividad.cargar-actividad');
    }

    private function save()
    {
        $this->validate();

        $campos = ['COD_ACTIVIDAD'];

        $valoresNuevos =[
            'COD_ACTIVIDAD' => $this->COD_ACTIVIDAD,
            'Nombre' => $this->Nombre,
            'Descripcion' => $this->Descripcion,
            'estado' => $this->estado, 
        ];

        $this->create(ActividadesEconomicasModel::class, $campos, $valoresNuevos);

        $this->dispatch('refreshLivewireTable');
    }

    public function close()
    {
        $this->reset(['COD_ACTIVIDAD', 'Nombre', 'Descripcion']);
        $this->open = false;
    }
}
