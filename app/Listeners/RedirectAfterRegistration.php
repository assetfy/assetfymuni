<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Redirect;

class RedirectAfterRegistration
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        // Recuperar todos los datos de la sesión en una sola variable
        $userData = session('user_data', []);

        if (isset($userData['user_type']) && in_array($userData['user_type'], ['empresa', 'proveedora'])) {
            // Redirigir a la creación de empresa sin pasar 'userData' como parámetro
            Redirect::route('create-empresas')->send();
        }
    }
}
