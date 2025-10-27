<?php

namespace App\Livewire\Usuarios;

use App\Models\TiposUsuariosModel;
use Livewire\Component;

class CreateTiposUsuarios extends Component
{
    public $search = "";
    public $open = false;
    public $nombre, $descripcion;

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

        $this->validate();

        TiposUsuariosModel::create([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
        ]);

        $this->reset(['nombre', 'descripcion']);
        $this->dispatch('render');
        $this->dispatch('lucky', 'El tipo se creó correctamente');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.usuarios.create-tipos-usuarios');
    }

    public function close()
    {
        $this->reset(['nombre', 'descripcion']);
        $this->open = false;
    }
}
