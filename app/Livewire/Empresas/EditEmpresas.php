<?php

namespace App\Livewire\Usuarios;

use App\Models\EmpresasModel;
use Illuminate\Validation\Rule;
use Livewire\Component;

class EditEmpresas extends Component
{
    public $usuario;
    public $update_estado;
    public $update_tipo_usuario;
    public $open = false;

    public function render()
    {
        // MODIFICADO: Cargar tipos de usuarios para la vista
        $empresasAll = EmpresasModel::all();

        return view('livewire.empresas.edit-empresas', [
            'empresas' => $this->empresas,
        ], compact('empresasAll'));
    }

    public function updateProfileInformation(UpdateUserProfileInformation $updater)
    {
        try {
            $this->validate([
                'update_estado' => 'nullable|in:0,1',
            ]);

            $updater->update($this->empresas, [
                'cuit' => $this->empresas->cuit,
                'razon_social' => $this->empresas->razon_social,
                'tipo' => $this->empresas->tipo,
                'estado' => $this->update_estado,
            ]);

            $this->dispatch('render');
            
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
