<?php

namespace App\Livewire\Unidad;

use App\Traits\VerificacionTrait;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\UnidadModel;

class EditUnidad extends Component
{
    use VerificacionTrait;
    public $open = false;
    public $unidad;
    public $unidades;
    public $updatedNombre;
    protected $listeners = ['openModalUnidad'];

    protected $rules = [
        'updatedNombre' => 'required|max:50'
    ];

    public function mount(UnidadModel $value)
    {
        $this->unidad = $value;
        $this->updatedNombre = $value->nombre;
    }

    public function openModalUnidad($value)
    {
        $this->unidades = UnidadModel::find($value['unidadMedidaId']);
        if($this->unidades) {
            $this->mount($this->unidades);
            $this->actualizar();
            $this->open = true;
        }
    }

    #[On('guardado')]
    public function actualizarUnidad()
    {
        $this->actualizar();
    }

    protected function actualizar()
    {
        $campos = ['nombre'];

        $valoresActualizados = ['nombre' => $this->updatedNombre];
    
        $this->verificar($this->unidad, $campos, $valoresActualizados);
    
        $this->dispatch('refreshLivewireTable');
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.unidad.edit-unidad');
    }
}
