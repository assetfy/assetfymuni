<?php

namespace App\Livewire\Perfil;

use Livewire\Component;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
use App\Models\User;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class UpdateProfileInformation extends Component
{
    public $photo, $state, $user, $photoPreview;

    use WithFileUploads;

    public function mount()
    {
        // Obtiene los valores del usuario que se esta modificando
        $this->user = User::find(Auth::id());

        // Cargar la información inicial del usuario
        $this->state = $this->user->only(['name', 'email', 'profile_photo_path']);
    }

    public function updateProfile()
    {
        $user = $this->user;

        $validatedData = Validator::make($this->state, [
            'name'  => ['required', 'string', 'max:255'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:1024'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
        ])->validateWithBag('updateProfileInformation');


        if ($this->photo instanceof \Illuminate\Http\UploadedFile) {
            // Genero un nombre único
            $filename = 'profile_' . uniqid() . '.' . $this->photo->extension();

            // Lo sube a S3 dentro de StorageMvp/profile-photos
            $path = $this->photo->storeAs(
                'StorageMvp/profile-photos',
                $filename,
                's3'
            );
            // 3) Guardalo en tu modelo
            $user->profile_photo_path = $path;
            $user->save();
        }

        // Actualizo el nombre
        $user->name = $this->state['name'];
        $user->email = $this->state['email'];
        $user->save();

        $this->dispatch('exito');
    }


    // Funcion creada para manejar la eliminacion de una imagen
    public function deleteProfilePhoto()
    {
        // Asigna al campo de profile valor null
        $this->user->profile_photo_path = null;
        $this->user->save();

        // Emite el evento para mostrar un mensaje de que se ha guardado los cambios (definido en la vista)
        $this->dispatch('exito');
    }

    public function render()
    {
        return view('livewire.perfil.update-profile-information');
    }
}
