<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Laravel\Jetstream\Jetstream;
use Illuminate\Validation\ValidationException;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules;

    /**
     * Validate and handle the creation of a new user.
     *
     * @param  array<string, mixed>  $input
     */
    public function create(array $input)
    {
        session()->forget('user_data');

        // Validación de datos de entrada con reglas de unicidad
        $validator = Validator::make($input, [
            'apellido' => ['required', 'string', 'max:255'],
            'nombre'   => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users,email', // Asegura que el email sea único
            ],
            'password' => $this->passwordRules(),
            'cuil' => [
                'required',
                'numeric',
                'digits:11',
                'unique:users,cuil', // Asegura que el CUIL sea único
            ],
            'user_type' => 'required|in:normal,empresa,proveedora',
            'tipo' => ['nullable', 'in:0,1'],
            'estado' => ['nullable', 'in:0,1'],
            'terms' => Jetstream::hasTermsAndPrivacyPolicyFeature() ? ['accepted', 'required'] : '',
        ]);

        // Ejecuta la validación y lanza excepciones si falla
        $validator->validate();

        $input['name'] = trim($input['apellido'] . ' ' . $input['nombre']);

        // Si el tipo de usuario no es "normal", guardar los datos en sesión
        if (in_array($input['user_type'], ['empresa', 'proveedora'])) {
            session()->put('user_data', [
                'name' => $input['name'],
                'email' => $input['email'],
                'cuil' => $input['cuil'],
                'password' => $input['password'], // Considera encriptar o manejar de forma segura
                'tipo' => $input['tipo'] ?? 2,
                'estado' => $input['estado'] ?? 1,
                'user_type' => $input['user_type'],
            ]);
            session()->save();
        } else {
            // Crear un nuevo usuario solo si es de tipo "normal"
            return $this->createNormalUser($input);
        }
    }

    /**
     * Crear un usuario normal.
     *
     * @param  array<string, mixed>  $input
     * @return User
     */
    protected function createNormalUser(array $input): User
    {
        $user = User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'cuil' => $input['cuil'],
            'password' => Hash::make($input['password']),
            'tipo' => $input['tipo'] ?? 2,
            'estado' => $input['estado'] ?? 1,
        ]);

        // Enviar notificación de verificación de email si aplica
        $user->setShouldSendEmailVerification(true);
        $user->sendEmailVerificationNotification();

        return $user;
    }
}
