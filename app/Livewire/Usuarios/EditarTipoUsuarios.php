<?php

namespace App\Livewire\Usuarios;

use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\TiposUsuariosModel;
use Livewire\Component;

class EditarTipoUsuarios extends Component
{
    public $usuario;
    public $update_estado;
    public $update_tipo_usuario;
    public $open = false;

    public function render()
    {
        // MODIFICADO: Cargar tipos de usuarios para la vista
        $tiposUsuarios = TiposUsuariosModel::all();

        return view('livewire.usuarios.editar-tipo-usuarios', [
            'usuario' => $this->usuario,
        ], compact('tiposUsuarios'));
    }

    public function updateProfileInformation(UpdateUserProfileInformation $updater)
    {
        try {
            $this->validate([
                'update_estado' => 'nullable|in:0,1',
                // MODIFICADO: Validar el campo update_tipo_usuario
                'update_tipo_usuario' => 'nullable|in:0,1,2',
            ]);

            $updater->update($this->usuario, [
                'name' => $this->usuario->name,
                'email' => $this->usuario->email,
                'estado' => $this->update_estado,
                // MODIFICADO: Agregar el campo tipo
                'tipo' => $this->update_tipo_usuario,
            ]);

            $this->dispatch('render');
            $this->dispatch('alert','El Usuario se actualizo');
            
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }
}
