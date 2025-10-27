<?php

namespace App\Services\FileImport;

use Exception;
use App\Services\FileImport\ProcessFile;
use App\Helpers\IdHelper;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class FileClientes extends ProcessFile
{
    protected string $tipoOperacion;

    // Podés agregar un método para llamar importFile() directamente
    public function importarClientes($file, array $requiredKeys = [])
    {
        return $this->importFile($file, $requiredKeys);
    }


    public function importFileClientes($file, array $requiredKeys = []): array
    {
        return $this->importFile($file, $requiredKeys);
    }

    public function confirmarClientes(
        array $datos,
        $tipoOperacion,
        callable $errorCallback,
        array $localName = [],
        string $globalName = '',
        array $localEmail = [],
        string $globalEmail = '',
        array $localPassword = [],
        string $globalPassword = '',
        array $localCuil = [],
        string $globalCuil = ''
    ): mixed {
        $errores = [];

        // Al inicio del método
        $emails = array_column($datos, 'email');
        $cuils  = array_column($datos, 'cuil');

        $usuariosExistentes = \App\Models\User::whereIn('email', $emails)
            ->orWhereIn('cuil', $cuils)
            ->get(['email', 'cuil'])
            ->toArray();

        foreach ($datos as $index => &$dato) {

            $yaExiste = collect($usuariosExistentes)->contains(function ($u) use ($dato) {
                return $u['email'] === $dato['email'] || $u['cuil'] === $dato['cuil'];
            });

            // 1) Verificar si ya existe (para Insertar)
            if ($yaExiste && $tipoOperacion === 'Insertar') {
                $errorCallback("Clientes ya cargados previamente. Verifique los datos o seleccione la opción 'Actualizar' si desea modificar los datos existentes.", $index);
                return 'cliente_duplicado';
            }

            $valido = $this->validateAndAssign($dato, 'name', $localName, $globalName, $index, 'Nombre', $errorCallback);

            if (!$valido) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Nombre' es inválido o está vacío.";
                continue;
            }

            $validoEmail = $this->validateAndAssign($dato, 'email', $localEmail, $globalEmail, $index, 'Email', $errorCallback);

            if (!$validoEmail) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Email' es inválido o está vacío.";
                continue;
            }

            $validoPass = $this->validateAndAssign($dato, 'password', $localPassword, $globalPassword, $index, 'Password', $errorCallback);

            if (!$validoPass) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Password' es inválido o está vacío.";
                continue;
            }

            $validoCuil = $this->validateAndAssign($dato, 'cuil', $localCuil, $globalCuil, $index, 'Cuil', $errorCallback);

            if (!$validoCuil) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Cuil' es inválido o está vacío.";
                continue;
            }
        }

        // 2. Si hay errores, llamar errorCallback con todos y no insertar nada
        if (!empty($errores)) {
            foreach ($errores as $idx => $msg) {
                $errorCallback($msg, $idx);
            }
            return false;
        }

        try {
            DB::transaction(function () use ($datos) {
                foreach ($datos as $dato) {
                    // 2) Insertar/actualizar en 'Users'
                    $user = \App\Models\User::updateOrCreate(
                        [
                            'email' => $dato['email'] ?? null,
                        ],
                        [
                            'name'     => $dato['name']     ?? null,
                            'cuil'     => $dato['cuil']     ?? null,
                            'password' => Hash::make($dato['password']) ?? null,
                            'tipo'     => $dato['tipo']     ?? 2,
                            'estado'   => $dato['estado']   ?? 1,
                        ]
                    );
                    // 3) Enviar notificación de verificación de email solo si fue creación
                    // (puedes modificar la condición si deseas otro comportamiento)
                    if ($user->wasRecentlyCreated) {
                        $user->setShouldSendEmailVerification(true);
                        $user->sendEmailVerificationNotification();
                    }
                    \App\Models\ClientesEmpresaModel::updateOrCreate(
                        [
                            'user_id' => $user->id,
                            'empresa_cuit'    => IdHelper::idEmpresa(),
                        ],
                        [
                            'cuil'        => $dato['cuil'],
                            'verificado' => $user->email_verified_at ? 'Si' : 'No',
                        ]
                    );
                }
            });
            return true;
        } catch (\Exception $e) {
            // Capturamos la excepción y enviamos el error a nuestro callback
            $errorCallback("Error al importar los datos: " . $e->getMessage(), null);
            return false;
        }
    }
}
