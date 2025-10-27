<?php
namespace App\Livewire\Altas;

use App\Services\MiddlewareInvoker;
use App\Models\EstadosAltasModel;
use App\Traits\SortableTrait;
use Livewire\Component;

class CreateEstadosAltas extends Component
{
    use SortableTrait;

    public $open = false;
    public $nombre, $descripcion;

    protected $listeners = ['CreateEstadoAltas'];

    public function CreateEstadoAltas()
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }

    protected $rules = [
        'nombre' => 'required|max:30',
        'descripcion' => 'required|max:30',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validateInput();
        $this->createAlta();
        $this->eventos();
        $this->close();
    }

    public function render()
    {
        return view('livewire.Estados_Alta.create-estados-altas');
    }

    private function createAlta()
    {
        EstadosAltasModel::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ]);
    }

    public function close()
    {
        $this->reset(['nombre', 'descripcion']);
        $this->open = false;
    }

    private function validateInput() // Cambia el nombre del método
    {
        $this->validate([
            'nombre' => 'required|max:50',
            'descripcion' => 'required|max:100',
        ]);
    }
}
