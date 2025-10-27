<?php

namespace App\Livewire\Perfil;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UpdateUserPassword extends Component
{
    public $state = [
        'current_password' => '',
        'password' => '',
        'password_confirmation' => '',
    ];

    public function updatePassword()
    {
        // logger()->info('Estado actual:', $this->state);

        // Validar los datos de entrada
        $this->validate([
            'state.current_password' => ['required', 'string'],
            'state.password' => ['required', 'string', 'min:8', 'confirmed'],
            'state.password_confirmation' => ['required', 'string', 'min:8'],
        ], [
            'state.current_password.required' => 'La contraseña actual es obligatoria.',
            'state.password.required' => 'La nueva contraseña es obligatoria.',
            'state.password.min' => 'La nueva contraseña debe tener mínimo de 8 caracteres.',
            'state.password_confirmation.required' => 'La confirmación de la nueva contraseña es obligatoria.',
            'state.password_confirmation.min' => 'Debe tener mínimo de 8 caracteres.',
            'state.password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user = User::find(Auth::id());

        // Verificar que la contraseña actual sea correcta
        if (!Hash::check($this->state['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'state.current_password' => ['La contraseña actual no es correcta.'],
            ]);
        }

        // Actualizar la contraseña
        $user->password = Hash::make($this->state['password']);
        $user->save();

        // Mensaje de éxito
        $this->dispatch('exitos');
        
        // Redirigir al usuario a la página de inicio de sesión o donde desees
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.perfil.update-user-password');
    }
}
