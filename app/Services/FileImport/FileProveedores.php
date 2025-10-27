<?php

namespace App\Services\FileImport;

use Exception;
use App\Services\FileImport\ProcessFile;
use Illuminate\Support\Facades\DB;
use App\Helpers\IdHelper;
use App\Models\TiposUbicacionesModel;
use App\Models\EmpresasModel;

class FileProveedores extends ProcessFile
{
    protected $geocodingService;

    public function importarArchivoProveedores($file, array $requiredKeys = []): array
    {
        return $this->importFile($file, $requiredKeys, function ($registro) {
            // Normalización específica para proveedores
            if (isset($registro['cuit'])) {
                $registro['cuit'] = preg_replace('/[^\d]/', '', $registro['cuit']); // eliminar guiones o puntos
            }
            if (isset($registro['email'])) {
                $registro['email'] = strtolower(trim($registro['email']));
            }
            return $registro;
        });
    }

    public function confirmarProveedores(
        array $datos,
        $tipoOperacion,
        callable $errorCallback,
        array $localRazonSocial = [],
        string $globalRazonSocial = '',
        array $localCuit = [],
        string $globalCuit = '',
        array $localLocalidad = [],
        string $globalLocalidad = '',
        array $localProvincia = [],
        string $globalProvincia = '',
    ): mixed {

        $errores = [];

        foreach ($datos as $index => &$dato) {

            // 1) Verificar si ya existe en 'mis_proveedores' (para evitar duplicar)
            if ($tipoOperacion == 'Insertar') {
                $existe = \App\Models\MisProveedoresModel::where('razon_social', $dato['razon_social'] ?? '')
                    ->where('id_usuario', auth()->id())
                    ->where('empresa', IdHelper::idEmpresa())
                    ->exists();
                if ($existe) {
                    $errorCallback("Proveedores ya cargados previamente. Verifique los datos o seleccione la opción 'Actualizar' si desea modificar los datos existentes.", $index);
                    return 'proveedor_duplicado';
                }
            }

            $validoSocial = $this->validateAndAssign($dato, 'razon_social', $localRazonSocial, $globalRazonSocial, $index, 'Razon Social', $errorCallback);

            if (!$validoSocial) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Razon Social' es inválido o está vacío.";
                continue;
            }

            $validoCuit = $this->validateAndAssign($dato, 'cuit', $localCuit, $globalCuit, $index, 'Cuit', $errorCallback);

            if (!$validoCuit) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Cuit' es inválido o está vacío.";
                continue;
            }

            $validoLocalidad = $this->validateAndAssign($dato, 'localidad', $localLocalidad, $globalLocalidad, $index, 'Localidad', $errorCallback);

            if (!$validoLocalidad) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Localidad' es inválido o está vacío.";
                continue;
            }

            $validoProv = $this->validateAndAssign($dato, 'provincia', $localProvincia, $globalProvincia, $index, 'Provincia', $errorCallback);

            if (!$validoProv) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Provincia' es inválido o está vacío.";
                continue;
            }

            // 3) (Opcional) Geocodificar si faltan lat/long
            if (empty($dato['lat']) || empty($dato['long'])) {
                $addressComponents = [
                    'pais'          => $dato['pais'] ?? null,
                    'provincia'     => $dato['provincia'] ?? null,
                    'ciudad'        => $dato['ciudad'] ?? null,
                    'codigo_postal' => $dato['codigo_postal'] ?? null,
                    'calle'         => $dato['calle'] ?? null,
                    'altura'        => $dato['altura'] ?? null,
                ];
                $coordinates = (new GeocodingService())->geocodeAddress($addressComponents);
                if ($coordinates) {
                    $dato['lat']  = $coordinates['lat'];
                    $dato['long'] = $coordinates['lon'];
                }
            }
        }

         if (!empty($errores)) {
                foreach ($errores as $idx => $msg) {
                    $errorCallback($msg, $idx);
                }
                return false;
            }

        try {
            DB::transaction(function () use ($datos) {
                foreach ($datos as $dato) {

                    // 2) Determinar si existe en la plataforma
                    $existePlataformaCollection = $this->existeEnPlataforma($dato);
                    $existePlataforma = $existePlataformaCollection->count() > 0 ? 'Si' : 'No';

                    // 4) Si no existe en la plataforma ('No'), inserta/actualiza primero en 'empresas_o_particulares'
                    if ($existePlataforma === 'No') {
                        \App\Models\EmpresasModel::updateOrCreate(
                            ['cuit' => $dato['cuit'] ?? null],
                            [
                                'razon_social' => $dato['razon_social'] ?? null,
                                'localidad'    => $dato['localidad']    ?? null,
                                'provincia'    => $dato['provincia']    ?? null,
                                'domicilio'    => $dato['calle'] . ' ' . ($dato['altura'] ?? '') ?? null,
                                'lat'          => $dato['lat'] ?? null,
                                'long'         => $dato['long'] ?? null,
                                'codigo_postal' => $dato['codigo_postal'] ?? null,
                                'tipo'         => 2,             // tu valor fijo
                                'estado'       => 'No Registrado', // tu valor fijo
                            ]
                        );
                    }

                    // 5) Finalmente, insertar/actualizar en 'mis_proveedores_favoritos'
                    \App\Models\MisProveedoresModel::updateOrCreate(
                        [
                            'razon_social' => $dato['razon_social'] ?? null,
                            'cuit'         => $dato['cuit']         ?? null,
                            'id_usuario'              => auth()->id(),
                            'empresa'                 => IdHelper::idEmpresa(),
                        ],
                        [
                            'provincia'               => $dato['provincia']    ?? null,
                            'localidad'               => $dato['localidad']    ?? null,
                            'email'                   => $dato['email']        ?? null,
                            'existe_en_la_plataforma' => $existePlataforma,
                        ]
                    );
                }
            });

            return true;
        } catch (\Exception $e) {
            // Capturas la excepción y envías el error a tu callback
            $errorCallback("Error al importar los datos: " . $e->getMessage(), null);
            return false;
        }
    }

    private function existeEnPlataforma($dato)
    {
        return EmpresasModel::where('cuit', $dato['cuit'])->get();
    }
}
