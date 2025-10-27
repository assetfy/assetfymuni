<?php

namespace App\Livewire\Controles;

use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use App\Models\ControlesModel;
use Livewire\Attributes\On;
use Livewire\Component;

class EditarControles extends Component
{
    use VerificacionTrait;
    public $open = false;
    public $control;
    public $updatedNombre;
    public $updatedDescripcion;
    public $controles;
    protected $listeners = ['EditarControles'];

    protected $rules = [
        'updatedNombre' => 'required|max:30',
        'updatedDescripcion' => 'required|max:100',
    ];

    public function mount(ControlesModel $value)
    {
        $this->control = $value;
        $this->updatedNombre = $value->nombre;
        $this->updatedDescripcion = $value->descripcion;
    }

    public function EditarControles($value)
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
        $this->controles = ControlesModel::find($value);
        if($this->controles) {
            $this->mount($this->controles);
            $this->actualizar();
            $this->open = true;
        }
    }

    #[On('guardado')]
    public function actualizar()
    {
        $this->actualizarControl();
    }

    protected function actualizarControl()
    {
        $campos = ['nombre', 'descripcion'];

        $valoresActualizados = ['nombre' => $this->updatedNombre, 'descripcion' => $this->updatedDescripcion];

        $this->verificar($this->control, $campos, $valoresActualizados);

        $this->dispatch('refreshLivewireTable');
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.Controles.editar-controles');
    }
}
