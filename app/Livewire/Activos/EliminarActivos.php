<?php

namespace App\Livewire\Activos;

use Livewire\Component;

use App\Models\ActivosModel;

class EliminarActivos extends Component
{
    public $activo,$open;

    public function mount($activoId)
    {
        $this->activo = $activoId;
    }

    public function deleteRecord()
    {
        ActivosModel::find($this->activo)->delete();
        $this->dispatch('render');
        $this->dispatch('alert', 'El Activo ha sido eliminado');
        $this->open = false;
    }
}