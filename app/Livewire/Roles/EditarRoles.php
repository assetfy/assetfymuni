<?php

namespace App\Livewire\Roles;

use App\Traits\VerificacionTrait;
use App\Models\EmpresasModel;
use App\Models\RolesModel;
use Livewire\Component;

class EditarRoles extends Component
{
    use VerificacionTrait;
    public $open, $upNombre, $id_rol, $cuit, $roles;
    protected $listeners = ['openModal'];

    protected $rules =
    [
        'upNombre' => 'required|max:100|min:3',
    ];

    public function openModal($value)
    {
        $roles = RolesModel::where('id_rol', $value)->first();

        if ($roles) {
            $this->roles = $roles;
            $this->upNombre = $roles->nombre;
            $this->id_rol = $roles->id_rol;
            $this->cuit = $roles->cuit;
            $this->open = true;
        }
    }

    public function update()
    {
        $this->validate();

        $campos = ['nombre'];

        $valoresActualizados = ['nombre' => $this->upNombre];

        $this->verificar($this->roles, $campos, $valoresActualizados);

        $this->dispatch('refreshLivewireTable');
    }

    public function render()
    {
        $tipos = EmpresasModel::all();
        return view('livewire.roles.editar-roles', [
            'roles' => $this->roles,
        ], compact('tipos'));
    }

    public function close()
    {
        $this->reset(['upNombre']);
        $this->open = false;
    }
}
