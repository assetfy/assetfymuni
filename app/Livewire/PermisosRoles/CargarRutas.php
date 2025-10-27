<?php

namespace App\Livewire\PermisosRoles;

use App\Traits\VerificacionTrait;
use App\Models\RutasModel;
use Livewire\Component;

class CargarRutas extends Component
{
    public $nombre, $ruta, $open, $configurable;
    use VerificacionTrait;
    protected $listeners = ['permisosRolescargarRutas'];

    public function render()
    {
        return view('livewire.permisos-roles.cargar-rutas');
    }

    public function permisosRolescargarRutas()
    {
        $this->open = true;
    }

    protected $rules =
    [
        'nombre' => 'required|max:100|min:3',
        'ruta' => 'required',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();
        $campos = ['nombre', 'ruta'];

        $valoresNuevos = [
            'nombre' => $this->nombre,
            'ruta' => $this->ruta,
            'configurable' => $this->configurable ?? 'No'
        ];

        $this->create(RutasModel::class, $campos, $valoresNuevos);

        $this->dispatch('refreshLivewireTable');

        $this->close();
    }

    public function close()
    {
        $this->reset(['nombre', 'ruta', 'configurable']);
        $this->open = false;
    }
}
