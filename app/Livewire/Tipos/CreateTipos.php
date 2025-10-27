<?php

namespace App\Livewire\Tipos;

use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use Livewire\WithFileUploads;
use App\Models\TiposModel;
use Livewire\Component;

class CreateTipos extends Component
{
    use WithFileUploads;
    use VerificacionTrait;
    // Propiedades del componente
    public $open = false;
    public $search = "";
    public $sigla, $nombre, $descripcion;
    public $imagen;
    // Listeners para eventos emitidos desde otros componentes o scripts
    protected $listeners = ['crearTipo'];
    // Reglas de validación
    protected $rules = [
        'sigla' => 'required|max:10|min:3',
        'nombre' => 'required|max:50',
        'descripcion' => 'required|max:100',
        'imagen' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:5120', // Máximo 5MB
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function  crearTipo()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }

    public function save()
    {
        $this->validate();

        $this->registro();
    }

    protected function registro()
    {
        // Almacena la imagen en el disco público y obtiene la ruta]
        $filename = $this->imagen->store('fotos', 'public');
        $rutaFoto = $filename;

        // Crea una nueva instancia de TiposModel con los datos proporcionados
        TiposModel::create([
            'nombre' => $this->nombre,
            'sigla' => $this->sigla,
            'descripcion' => $this->descripcion,
            'imagen' => $rutaFoto,
        ]);
        // Emite un evento para refrescar la tabla si existe
        $this->dispatch('refreshLivewireTable');
        // Cierra el modal y resetea los campos
        $this->close();
    }

    public function removeImagen()
    {
        $this->imagen = null;
    }

    public function close()
    {
        $this->reset(['sigla', 'nombre', 'descripcion', 'imagen']);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.tipos.create-tipos');
    }
}
