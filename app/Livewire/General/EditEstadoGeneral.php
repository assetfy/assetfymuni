<?php

namespace App\Livewire\General;

use App\Services\MiddlewareInvoker;
use App\Models\EstadoGeneralModel;
use App\Traits\SortableTrait;
use Livewire\Attributes\On;
use Livewire\Component;


class EditEstadoGeneral extends Component
{
    use SortableTrait;
    public $open = false;
    public $estado;
    public $updatedNombre;
    public $updatedDescripcion;
    public $estados;
    protected $listeners = ['EditarEstadoGeneral'];

    protected $rules = [
        'updatedNombre' => 'required|max:50',
        'updatedDescripcion' =>  'required|max:100',
    ];

    public function mount(EstadoGeneralModel $value)
    {
        $this->estado = $value;
        $this->updatedNombre = $value->nombre;
        $this->updatedDescripcion = $value->descripcion;
    }

    public function EditarEstadoGeneral($value)
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->openModal($value);
        }
    }

    public function openModal($value)
    {
        $this->estados = EstadoGeneralModel::find($value);
        if ($this->estados) {
            $this->mount($this->estados);
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
        $this->actualizarGeneral();
        $this->dispatch('lucky');
    }

    protected function actualizarGeneral()
    {
        $this->validate();

        $this->estado->nombre = $this->updatedNombre;
        $this->estado->descripcion = $this->updatedDescripcion;

        $this->estado->save();
        $this->dispatch('refreshLivewireTable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.estadogeneral.edit-estado-general');
    }
}
