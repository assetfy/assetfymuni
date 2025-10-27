<?php

namespace App\Livewire\Perfil\Empresas;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\EmpresasModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Notifications\EmpresaDeletedNotification;

class DeleteEmpresa extends Component
{
    public $confirmingEmpresaDeletion = false;
    public $password, $empresa;

    protected $rules = [
        'password' => 'required|string',
    ];

    public function confirmEmpresaDeletion()
    {
        $this->confirmingEmpresaDeletion = true;
    }

    public function deleteEmpresa()
    {
        $this->validate();

        // Verifica la contraseña del usuario
        if (!Hash::check($this->password, Auth::user()->password)) {
            $this->addError('password', 'La contraseña es incorrecta.');
            return;
        }

        // Elimina la cuenta del usuario
        $user = User::find(Auth::id());
        $this->empresa = EmpresasModel::find($user->entidad);

        if ($this->empresa) {
            // Envía la notificación de eliminación de cuenta
            $user->notify(new EmpresaDeletedNotification($this->empresa));

            $this->empresa->estado = 'Baja';
            $this->empresa->save();
            
            return redirect('usuarios/dashboard-usuario');
        }
    }

    public function render()
    {
        return view('livewire.perfil.empresas.delete-empresa');
    }
}
