<?php

namespace App\Services\FileImport;

use Exception;
use App\Services\FileImport\ProcessFile;
use Illuminate\Support\Facades\DB;
use App\Helpers\IdHelper;
use App\Models\TiposUbicacionesModel;
use Illuminate\Support\Facades\Cache;

class FileUbicaciones extends ProcessFile
{
    protected $geocodingService;
    public $tipoDatos;
    protected array $tiposUbicacionCache = [];

    public function __construct($tipoDatos = 'UbicacionesPropias')
    {
        $this->tipoDatos = $tipoDatos;

        $lista = Cache::remember('act.tipos_ubicaciones.lookup', 3600, function () {
            return TiposUbicacionesModel::select('id_tipo', 'nombre')->get();
        });

        // índices por id ("12") y nombre ("oficina")
        foreach ($lista as $t) {
            $this->tiposUbicacionCache[(string)$t->id_tipo] = $t->toArray();
            $this->tiposUbicacionCache[strtolower($t->nombre)] = $t->toArray();
        }
    }

    public function importFileUbicacion($file, array $requiredKeys = []): array
    {
        return $this->importFile($file, $requiredKeys, function ($registro) {
            // Validar y normalizar el tipo de ubicación
            $tipoCrudo = $registro['tipo_de_ubicacion'] ?? '';
            $tipoValido = $this->obtenerTipoUbicacionValido($tipoCrudo);

            $registro['tipo_de_ubicacion_valido'] = $tipoValido['nombre'] ?? null;
            $registro['tipo_de_ubicacion_id'] = $tipoValido['id_tipo'] ?? null;

            return $registro;
        });
    }

    // Permite verificar y asignar un valor de tipo de ubicación si es correcto y válido
    protected function obtenerTipoUbicacionValido(string $valor): ?array
    {
        $k = strtolower(trim((string)$valor));
        $kNum = preg_replace('/\D+/', '', $k);

        return $this->tiposUbicacionCache[$k]
            ?? ($kNum !== '' ? ($this->tiposUbicacionCache[$kNum] ?? null) : null);
    }

    public function confirmImportUbicacion(
        array $datos,
        $propiedad,
        $tipoOperacion,
        callable $errorCallback,
        array $localTipoUbicacion = [],
        $globalTipoUbicacion = '',
        array $localGestor = [],
        string $globalGestor = '',
        array $localPropietario = [],
        string $globalPropietario = '',
        array $local_multiples = [],
        string $global_multiples = '',
        array $localPiso = [],
        array $localSubsuelo = [],
        array $localEmpresa = [],
        string $globalEmpresa = ''
    ): mixed {
        $errores = [];
        $propiedad = $this->tipoDatos === 'UbicacionesPropias' ? 'Propio' : 'Cliente';

        // 1. Validar primero todo sin insertar (fuera de la transacción)
        foreach ($datos as $index => &$dato) {
            $nombre = isset($dato['nombre']) ? trim((string)$dato['nombre']) : '';
            $pais = isset($dato['pais']) ? trim((string)$dato['pais']) : '';
            $provincia = isset($dato['provincia']) ? trim((string)$dato['provincia']) : '';
            $ciudad = isset($dato['ciudad']) ? trim((string)$dato['ciudad']) : '';
            $cp = isset($dato['codigo_postal']) ? trim((string)$dato['codigo_postal']) : '';
            $calle = isset($dato['calle']) ? trim((string)$dato['calle']) : '';
            $altura = isset($dato['altura']) ? trim((string)$dato['altura']) : '';

            if ($nombre === '') {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": el campo 'Nombre' es obligatorio y debe venir completado en el archivo.";
                continue;
            }

            if ($pais === '') {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": el campo 'País' es obligatorio y debe venir completado en el archivo.";
                continue;
            }

            if ($cp === '') {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": el campo 'Código Postal' es obligatorio y debe venir completado en el archivo.";
                continue;
            }

            if ($ciudad === '') {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": el campo 'Ciudad' es obligatorio y debe venir completado en el archivo.";
                continue;
            }

            if ($provincia === '') {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": el campo 'Provincia' es obligatorio y debe venir completado en el archivo.";
                continue;
            }

            if ($calle === '') {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": el campo 'Calle' es obligatorio y debe venir completado en el archivo.";
                continue;
            }

            if ($altura === '' || !is_numeric($altura) || (int)$altura <= 0) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": el campo 'Altura' es obligatorio, debe ser numérico.";
                continue;
            }

            $rawMultiples = $this->pickFirstFilled(
                $local_multiples[$index] ?? null,
                $dato['multiples_pisos'] ?? null,
                $global_multiples ?? null
            );

            $multiples = $this->normSiNo($rawMultiples);

            if ($multiples === null) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": debe indicar 'Múltiples pisos' (Sí/No).";
                continue;
            }

            $pisos     = $localPiso[$index]     ?? $dato['piso']     ?? null;
            $subsuelos = $localSubsuelo[$index] ?? $dato['subsuelo'] ?? null;

            if ($multiples === '0') {
                $pisos = null;
                $subsuelos = null;
            } else { // '1' = Sí
                $pisos = trim((string)$pisos);
                if ($pisos === '' || !ctype_digit($pisos) || (int)$pisos < 1) {
                    $errores[$index] = "Error en la fila " . ($index + 1) . ": indicó múltiples pisos; complete 'Piso'.";
                    continue;
                }
                $subsuelos = trim((string)$subsuelos);
                $subsuelos = (ctype_digit($subsuelos) ? (int)$subsuelos : null);
            }

            $dato['multiples_pisos'] = $multiples;
            $dato['piso']            = ($multiples === '1') ? (int)$pisos : null;
            $dato['subsuelo']        = ($multiples === '1') ? $subsuelos  : null;

            if ($propiedad === 'Cliente') {
                $valido = $this->validateAndAssign($dato, 'cuit_propietario', $localPropietario, $globalPropietario, $index, 'CUIT Propietario', $errorCallback);

                if (!$valido) {
                    $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'CUIT Propietario' es inválido o está vacío.";
                    continue;
                }

                $cuitEmpresa = !empty($localEmpresa[$index]) ? $localEmpresa[$index]
                    : (!empty($globalEmpresa) ? $globalEmpresa
                        : (!empty($dato['cuit_propietario']) ? $dato['cuit_propietario'] : null));

                $dato['cuit_propietario'] = $cuitEmpresa;
            }

            if ($tipoOperacion == 'Insertar' && $propiedad === 'Propio') {
                $existe = \App\Models\UbicacionesModel::where('nombre', $dato['nombre'] ?? '')
                    ->where('cuit', '=', IdHelper::idEmpresa())
                    ->exists();

                if ($existe) {
                    $errorCallback("Ubicaciones ya cargadas previamente. Verifíquelas o seleccione la opción 'Actualizar' si desea modificar los datos existentes.", $index);
                    return 'ubicacion_duplicada';
                }
            } elseif ($tipoOperacion == 'Insertar' && $propiedad === 'Cliente') {
                $existe = \App\Models\UbicacionesModel::where('nombre', $dato['nombre'] ?? '')
                    ->where('cuit', '=', $dato['cuit_propietario'])
                    ->exists();
                if ($existe) {
                    $errorCallback("Ubicaciones ya cargadas previamente. Verifíquelas o seleccione la opción 'Actualizar' si desea modificar los datos existentes.", $index);
                    return 'ubicacion_duplicada';
                }
            }

            $valido = $this->validateAndAssign($dato, 'tipo_de_ubicacion', $localTipoUbicacion, $globalTipoUbicacion, $index, 'Tipo de Ubicación', $errorCallback);

            if (!$valido) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Tipo de Ubicación' es inválido o está vacío.";
                continue;
            }

            $tipoValor = trim($dato['tipo_de_ubicacion']);
            $tipoUbicacion = $this->obtenerTipoUbicacionValido($tipoValor);

            if (!$tipoUbicacion) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El tipo de ubicación '{$tipoValor}' es inválido o está vacío.";
                continue;
            }

            $dato['tipo'] = $tipoUbicacion['id_tipo'] ?? null;

            $this->validateAndAssign($dato, 'cuil_gestor', $localGestor, $globalGestor, $index, 'Gestor', $errorCallback);

            $cuit_empresa = null;
            if ($this->tipoDatos === 'UbicacionesUsuarios') {
                $cuit_empresa = IdHelper::idEmpresa();
                $propiedad = 'Cliente';
                $cuit = $dato['cuit_propietario'] ?? null;
            } else {
                $propiedad = 'Propio';
                $cuit = IdHelper::idEmpresa();
            }

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
                    $dato['lat'] = $coordinates['lat'];
                    $dato['long'] = $coordinates['lon'];
                }
            }

            $dato['cuit'] = $cuit;
            $dato['cuit_empresa'] = $cuit_empresa;
            $dato['propiedad'] = $propiedad;
        }

        // 2. Si hay errores, llamar errorCallback con todos y no insertar nada
        if (!empty($errores)) {
            foreach ($errores as $idx => $msg) {
                $errorCallback($msg, $idx);
            }
            return false;
        }

        // 3. Insertar/actualizar dentro de una transacción (todo o nada)
        try {
            DB::transaction(function () use ($datos) {
                foreach ($datos as $dato) {

                    \App\Models\UbicacionesModel::updateOrCreate(
                        [
                            'nombre' => $dato['nombre'],
                            'cuit' => $dato['cuit'],
                        ],
                        [
                            'id_externo'    => $dato['id_externo'] ?? null,
                            'pais'          => $dato['pais'],
                            'provincia'     => $dato['provincia'],
                            'ciudad'        => $dato['ciudad'],
                            'codigo_postal' => $dato['codigo_postal'] ?? null,
                            'calle'         => $dato['calle'],
                            'altura'        => $dato['altura'],
                            'piso'          => $dato['piso'] ?? null,
                            'depto'         => $dato['depto'] ?? null,
                            'lat'           => $dato['lat'] ?? null,
                            'long'          => $dato['long'] ?? null,
                            'cuil'          => null,
                            'cuit'          => $dato['cuit'],
                            'propiedad'     => $dato['propiedad'],
                            'tipo'          => $dato['tipo'],
                            'cuit_empresa'  => $dato['cuit_empresa'],
                            'cuil_gestor'   => $dato['cuil_gestor'] ?? null,
                            'multipisos'    => $dato['multiples_pisos'],
                            'fecha_carga'   => \Carbon\Carbon::now()->format('Y-m-d H:i'),
                            'subsuelo'     => $dato['subsuelo'] ?? null,
                        ]
                    );
                }
            });
            return true;
        } catch (Exception $e) {
            $errorCallback("Error al importar los datos: " . $e->getMessage(), null);
            return false;
        }
    }

    // Helpers (ponelos como métodos privados en la clase)
    private function pickFirstFilled(...$vals)
    {
        foreach ($vals as $v) if ($v !== null && $v !== '') return $v;
        return null;
    }
    private function normSiNo($v): ?string
    {
        $v = strtolower(trim((string)$v));
        if ($v === '' || $v === 'n/a') return null;                 // sin dato
        if (in_array($v, ['si', 'sí', 's', '1', 'yes', 'y', 'true'], true)) return '1';
        if (in_array($v, ['no', 'n', '0', 'false'], true))                return '0';
        return null; // inválido => pedir corrección
    }
}
