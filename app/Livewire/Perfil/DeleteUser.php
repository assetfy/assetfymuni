<?php 

namespace App\Livewire\Perfil;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Notifications\UserDeletedNotification;

class DeleteUser extends Component
{
    public $confirmingUserDeletion = false;
    public $password;

    protected $rules = [
        'password' => 'required|string',
    ];

    public function confirmUserDeletion()
    {
        $this->confirmingUserDeletion = true;
    }

    public function deleteUser()
    {
        $this->validate();

        // Verifica la contraseña del usuario
        if (!Hash::check($this->password, Auth::user()->password)) {
            $this->addError('password', 'La contraseña es incorrecta.');
            return;
        }

        // Elimina la cuenta del usuario
        $user = User::find(Auth::id());

        // Envía la notificación de eliminación de cuenta
        $user->notify(new UserDeletedNotification($user));

        $user->estado = 2;
        $user->email_verified_at = null;
        $user->primera_session = null;
        $user->save();

        return redirect('/');
    }

    public function render()
    {
        return view('livewire.perfil.delete-user');
    }
}