<?php

namespace App\Services;

use Exception;
use App\Models\UbicacionesModel;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Helpers\IdHelper;
use App\Models\EmpresasModel;
use App\Models\TiposEmpresaModel;
use App\Models\ActivosCompartidos;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FileImportService
{
    protected $geocodingService;
    public $fecha_asignacion;

    /**
     * Inyecta el servicio de geocodificación.
     *
     * @param GeocodingService $geocodingService
     */
    public function __construct(GeocodingService $geocodingService)
    {
        $this->geocodingService = $geocodingService;
    }
    /**
     * Importa un archivo (JSON, XML o XLSX) y devuelve un arreglo con los registros normalizados.
     *
     * Se normalizan las claves: se convierten a minúsculas y se reemplazan los espacios por guiones bajos,
     * para que por ejemplo "Codigo Postal" se convierta en "codigo_postal".
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param array $requiredKeys Array de columnas requeridas (ej. ['Nombre', 'Pais', ...])
     * @return array Arreglo de registros normalizados.
     * @throws Exception
     */
    public function importFileUbicacion($file, array $requiredKeys = []): array
    {
        // 1. Procesar el archivo y obtener registros sin normalizar
        $datos = $this->procesarArchivo($file);

        // 2. Validar que el archivo contenga las columnas requeridas, si se especifican
        if (!empty($requiredKeys)) {
            $primerRegistro = reset($datos);
            if (!is_array($primerRegistro)) {
                throw new Exception('El archivo no contiene registros válidos.');
            }
            $this->validarColumnas($primerRegistro, $requiredKeys);
        }

        // 3. Normalización de claves: minúsculas y espacios→guiones bajos
        $datosNormalizados = [];
        foreach ($datos as $registro) {
            $registroNormalizado = [];
            foreach ($registro as $key => $value) {
                $normalizedKey = str_replace(' ', '_', strtolower(trim($key)));
                $registroNormalizado[$normalizedKey] = $value;
            }

            // 4. Saltar filas que queden completamente vacías
            $todosVacios = true;
            foreach ($registroNormalizado as $valor) {
                if ((is_string($valor) && trim($valor) !== '') || (!is_string($valor) && !is_null($valor))) {
                    $todosVacios = false;
                    break;
                }
            }
            if ($todosVacios) {
                continue; // descartar esta fila
            }

            // 5. Añadir al resultado
            $datosNormalizados[] = $registroNormalizado;
        }

        return $datosNormalizados;
    }


    public function importarArchivoProveedores($file, array $requiredKeys = []): array
    {
        $datos = $this->procesarArchivo($file);
        // 2. Validar columnas requeridas (si aplican)
        if (!empty($requiredKeys)) {
            $primerRegistro = reset($datos);
            if (!is_array($primerRegistro)) {
                throw new Exception('El archivo no contiene registros válidos.');
            }
            $this->validarColumnas($primerRegistro, $requiredKeys);
        }

        // 3. Normalizar todas las claves (minúsculas, espacios->guiones bajos)
        $datosNormalizados = [];
        foreach ($datos as $registro) {
            $registroNormalizado = [];
            foreach ($registro as $key => $value) {
                $normalizedKey = str_replace(' ', '_', strtolower(trim($key)));
                $registroNormalizado[$normalizedKey] = $value;
            }
            $todosVacios = true;
            foreach ($registroNormalizado as $valor) {
                if ((is_string($valor) && trim($valor) !== '') || (!is_string($valor) && !is_null($valor))) {
                    $todosVacios = false;
                    break;
                }
            }
            if ($todosVacios) {
                continue; // descartar esta fila
            }

            $datosNormalizados[] = $registroNormalizado;
        }

        return $datosNormalizados;
    }

    public function importFileActivo($file, array $requiredKeys = []): array
    {
        // 1. Procesar el archivo y obtener registros sin normalizar
        $datos = $this->procesarArchivo($file);

        // 2. Validar columnas requeridas (si se pasaron)
        if (!empty($requiredKeys)) {
            $primerRegistro = reset($datos);
            if (!is_array($primerRegistro)) {
                throw new Exception('El archivo no contiene registros válidos.');
            }
            $this->validarColumnas($primerRegistro, $requiredKeys);
        }

        // 3. Normalizar claves y limpiar valores
        $datosNormalizados = [];
        foreach ($datos as $registro) {
            // 3.1. Normalizar cada clave: trim(), lowercase, espacios → guiones bajos
            $registroNormalizado = [];
            foreach ($registro as $key => $value) {
                $normalizedKey = str_replace(' ', '_', strtolower(trim($key)));
                $registroNormalizado[$normalizedKey] = $value;
            }

            // 3.2. Validar/convertir campos numéricos a null si no lo son
            foreach (
                [
                    'id_subcategoria',
                    'id_categoria',
                    'id_tipo',
                    'id_estado_sit_alta',
                    'id_estado_sit_general'
                ] as $campoNum
            ) {
                if (isset($registroNormalizado[$campoNum]) && !is_numeric($registroNormalizado[$campoNum])) {
                    $registroNormalizado[$campoNum] = null;
                }
            }

            // 3.3. Campos string: convertir strings vacíos a null
            foreach (
                [
                    'estado_inventario',
                    'nombre'
                ] as $campoStr
            ) {
                if (isset($registroNormalizado[$campoStr]) && trim($registroNormalizado[$campoStr]) === '') {
                    $registroNormalizado[$campoStr] = null;
                }
            }

            // 4. Saltar filas que queden completamente vacías
            $todosVacios = true;
            foreach ($registroNormalizado as $valor) {
                // Si encuentra un valor no-null y no-vacío, no todos están vacíos
                if ((is_string($valor) && trim($valor) !== '') || (!is_string($valor) && !is_null($valor))) {
                    $todosVacios = false;
                    break;
                }
            }
            if ($todosVacios) {
                continue; // descartar esta fila
            }

            // 5. Añadir al array final
            $datosNormalizados[] = $registroNormalizado;
        }

        return $datosNormalizados;
    }

    /**
     * Confirma la importación masiva de ubicaciones.
     *
     * Recorre el arreglo de registros ya normalizados, verifica duplicados,
     * geocodifica las direcciones (si es necesario) y guarda cada registro en la base de datos.
     *
     * @param array $datos Arreglo de registros ya normalizados.
     * @throws Exception
     */
    public function confirmImportUbicacion(
        array $datos,
        array $localTipoUbicacion = [],
        $globalTipoUbicacion = '',
        $propiedad,
        array $localGestor = [],
        $globalGestor = '',
        $tipoOperacion,
        callable $errorCallback
    ): bool {
        try {
            // dd($datos);
            DB::transaction(function () use (
                $datos,
                $localTipoUbicacion,
                $globalTipoUbicacion,
                $propiedad,
                $localGestor,
                $globalGestor,
                $tipoOperacion,
                $errorCallback
            ) {
                foreach ($datos as $index => $dato) {
                    // Validar y asignar 'tipo_de_ubicacion' y 'cuil_gestor'
                    if (
                        !$this->validateAndAssign($dato, 'tipo_de_ubicacion', $localTipoUbicacion, $globalTipoUbicacion, $index, 'Tipo de Ubicación', $errorCallback) ||
                        !$this->validateAndAssign(
                            $dato,
                            'cuil_gestor',
                            $localGestor,
                            $globalGestor,
                            $index,
                            'Gestor',
                            $errorCallback,
                            auth()->user()->cuil
                        )
                    ) {
                        // Lanza excepción para que DB::transaction realice el rollback automáticamente
                        throw new \Exception("Error en la fila " . ($index + 1) . ": Falta un valor requerido.");
                    }

                    if ($tipoOperacion == 'Insertar') {
                        $existe = \App\Models\UbicacionesModel::where('nombre', $dato['nombre'] ?? '')
                            ->where('cuit', '=', IdHelper::idEmpresa())
                            ->exists();
                        if ($existe) {
                            continue;
                        }
                    }
                    // Si falta latitud o longitud, se realiza la geocodificación.
                    if (empty($dato['lat']) || empty($dato['long'])) {
                        $addressComponents = [
                            'pais'          => $dato['pais'] ?? null,
                            'provincia'     => $dato['provincia'] ?? null,
                            'ciudad'        => $dato['ciudad'] ?? null,
                            'codigo_postal' => $dato['codigo_postal'] ?? null,
                            'calle'         => $dato['calle'] ?? null,
                            'altura'        => $dato['altura'] ?? null,
                        ];
                        $coordinates = $this->geocodingService->geocodeAddress($addressComponents);
                        if ($coordinates) {
                            $dato['lat']  = $coordinates['lat'];
                            $dato['long'] = $coordinates['lon'];
                        }
                    }

                    \App\Models\UbicacionesModel::updateOrCreate(
                        [
                            'nombre' => $dato['nombre'] ?? null,
                            'cuit'   => IdHelper::idEmpresa(),
                        ],
                        [
                            'id_externo'    => $dato['id_externo'] ?? null,
                            'pais'          => $dato['pais'] ?? null,
                            'provincia'     => $dato['provincia'] ?? null,
                            'ciudad'        => $dato['ciudad'] ?? null,
                            'codigo_postal' => $dato['codigo_postal'] ?? null,
                            'calle'         => $dato['calle'] ?? null,
                            'altura'        => $dato['altura'] ?? null,
                            'piso'          => $dato['piso'] ?? null,
                            'depto'         => $dato['depto'] ?? null,
                            'lat'           => $dato['lat'] ?? null,
                            'long'          => $dato['long'] ?? null,
                            'cuil'          => null,
                            'cuit'          => IdHelper::idEmpresa(),
                            'propiedad'     => 'Propio',
                            'tipo'          => $dato['tipo_de_ubicacion'] ?? null,
                            'cuit_empresa'  => null,
                            'cuil_gestor'   => $dato['cuil_gestor'],
                            'fecha_carga'   => \Carbon\Carbon::now()->format('Y-m-d H:i'),
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

    public function confirmImportUbicacionClientes(
        array $datos,
        array $localTipoUbicacion = [],
        $globalTipoUbicacion = '',
        $propiedad,
        array $localGestor = [],
        $globalGestor = '',
        $tipoOperacion,
        callable $errorCallback
    ): bool {
        try {
            // dd($datos);
            DB::transaction(function () use (
                $datos,
                $localTipoUbicacion,
                $globalTipoUbicacion,
                $propiedad,
                $localGestor,
                $globalGestor,
                $tipoOperacion,
                $errorCallback
            ) {
                foreach ($datos as $index => $dato) {
                    // Validar y asignar 'tipo_de_ubicacion' y 'cuil_gestor'
                    if (
                        !$this->validateAndAssign($dato, 'tipo_de_ubicacion', $localTipoUbicacion, $globalTipoUbicacion, $index, 'Tipo de Ubicación', $errorCallback) ||
                        !$this->validateAndAssign(
                            $dato,
                            'cuil_gestor',
                            $localGestor,
                            $globalGestor,
                            $index,
                            'Gestor',
                            $errorCallback,
                            auth()->user()->cuil
                        )
                    ) {
                        // Lanza excepción para que DB::transaction realice el rollback automáticamente
                        throw new \Exception("Error en la fila " . ($index + 1) . ": Falta un valor requerido.");
                    }

                    if ($tipoOperacion == 'Insertar') {
                        $existe = \App\Models\UbicacionesModel::where('nombre', $dato['nombre'] ?? '')
                            ->where('cuit', '=', $dato['cuit_propietario'])
                            ->exists();

                        if ($existe) {
                            throw new \Exception("Las ubicaciones ya fueron cargadas previamente");
                        }
                    }

                    // Si falta latitud o longitud, se realiza la geocodificación.
                    if (empty($dato['lat']) || empty($dato['long'])) {
                        $addressComponents = [
                            'pais'          => $dato['pais'] ?? null,
                            'provincia'     => $dato['provincia'] ?? null,
                            'ciudad'        => $dato['ciudad'] ?? null,
                            'codigo_postal' => $dato['codigo_postal'] ?? null,
                            'calle'         => $dato['calle'] ?? null,
                            'altura'        => $dato['altura'] ?? null,
                        ];
                        $coordinates = $this->geocodingService->geocodeAddress($addressComponents);
                        if ($coordinates) {
                            $dato['lat']  = $coordinates['lat'];
                            $dato['long'] = $coordinates['lon'];
                        }
                    }

                    \App\Models\UbicacionesModel::updateOrCreate(
                        [
                            'nombre' => $dato['nombre'] ?? null,
                            'cuit'   => $dato['cuit_propietario'],
                        ],
                        [
                            'id_externo'    => $dato['id_externo'] ?? null,
                            'pais'          => $dato['pais'] ?? null,
                            'provincia'     => $dato['provincia'] ?? null,
                            'ciudad'        => $dato['ciudad'] ?? null,
                            'codigo_postal' => $dato['codigo_postal'] ?? null,
                            'calle'         => $dato['calle'] ?? null,
                            'altura'        => $dato['altura'] ?? null,
                            'piso'          => $dato['piso'] ?? null,
                            'depto'         => $dato['depto'] ?? null,
                            'lat'           => $dato['lat'] ?? null,
                            'long'          => $dato['long'] ?? null,
                            'cuil'          => null,
                            'cuit'          => $dato['cuit_propietario'],
                            'propiedad'     => 'Cliente',
                            'tipo'          => $dato['tipo_de_ubicacion'] ?? null,
                            'cuit_empresa'  => IdHelper::idEmpresa(),
                            'cuil_gestor'   => $dato['cuil_gestor'],
                            'fecha_carga'   => \Carbon\Carbon::now()->format('Y-m-d H:i'),
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

    public function confirmImportActivo(
        array $datos,
        array $localSubcategoria = [],
        $globalSubcategoria = '',
        array $localCategoria = [],
        $globalCategoria = '',
        array $localTipo = [],
        $globalTipo = '',
        array $localSitAlta = [],
        $globalSitAlta = '',
        array $localEstadoGeneral = [],
        $globalEstadoGeneral = '',
        array $localUbicacion = [],
        $globalUbicacion = '',
        array $localGestores = [],
        $globalGestores = '',
        array $localResponsable = [],
        $globalResponsable = '',
        array $localAsignado = [],
        $globalAsignado = '',
        $tipoOperacion,
        callable $errorCallback
    ): bool {
        try {
            DB::transaction(function () use (
                $datos,
                $localSubcategoria,
                $globalSubcategoria,
                $localCategoria,
                $globalCategoria,
                $localTipo,
                $globalTipo,
                $localSitAlta,
                $globalSitAlta,
                $localEstadoGeneral,
                $globalEstadoGeneral,
                $localUbicacion,
                $globalUbicacion,
                $localGestores,
                $globalGestores,
                $localResponsable,
                $globalResponsable,
                $localAsignado,
                $globalAsignado,
                $errorCallback
            ) {
                foreach ($datos as $index => $dato) {
                    // 1) Validaciones obligatorias
                    if (
                        !$this->validateAndAssign($dato, 'id_subcategoria', $localSubcategoria, $globalSubcategoria, $index, 'Subcategoría', $errorCallback)
                        || !$this->validateAndAssign($dato, 'id_categoria',    $localCategoria,    $globalCategoria,    $index, 'Categoría',       $errorCallback)
                        || !$this->validateAndAssign($dato, 'id_tipo',         $localTipo,         $globalTipo,         $index, 'Tipo',            $errorCallback)
                        || !$this->validateAndAssign($dato, 'id_estado_sit_general', $localEstadoGeneral, $globalEstadoGeneral, $index, 'Estado Sit General', $errorCallback)
                        || !$this->validateAndAssign($dato, 'id_estado_sit_alta',  $localSitAlta,      $globalSitAlta,      $index, 'Estado Sit Alta',  $errorCallback)
                        || !$this->validateAndAssign($dato, 'id_ubicacion',   $localUbicacion,    $globalUbicacion,    $index, 'Ubicación',       $errorCallback)
                    ) {
                        throw new \Exception("Error en la fila " . ($index + 1) . ": Falta un valor requerido.");
                    }

                    // 2) Truncar el nombre al largo máximo
                    $nombreRecortado = Str::limit($dato['nombre'] ?? '', 50, '');

                    // 3) Crear SIEMPRE un nuevo activo (no se hace UPDATE sobre este modelo)
                    $activo = \App\Models\ActivosModel::create([
                        'nombre'                => $nombreRecortado,
                        'id_externo'            => $dato['id_externo'] ?? null,
                        'id_estado_sit_alta'    => $dato['id_estado_sit_alta'],
                        'estado_inventario'     => 'Alta',
                        'id_ubicacion'          => $dato['id_ubicacion'] ?? null,
                        'id_subcategoria'       => $dato['id_subcategoria'] ?? null,
                        'id_categoria'          => $dato['id_categoria'] ?? null,
                        'id_tipo'               => $dato['id_tipo'] ?? null,
                        'id_estado_sit_general' => $dato['id_estado_sit_general'] ?? null,
                        'usuario_titular'       => is_numeric($dato['usuario_titular'] ?? null)
                            ? $dato['usuario_titular']
                            : null,
                        'empresa_titular'       => IdHelper::idEmpresa(),
                        'propietario'           => 'Propio',
                    ]);

                    // 4) Si hay datos de asignación, validar e insertar/actualizar relaciones hijas
                    $tieneAsignacion =
                        !empty($localGestores)   || !empty($globalGestores)   ||
                        !empty($localResponsable) || !empty($globalResponsable) ||
                        !empty($localAsignado)    || !empty($globalAsignado);

                    if ($tieneAsignacion) {
                        // 4a) Validar campos opcionales
                        if (
                            !$this->validateAndAssign($dato, 'gestor',      $localGestores,    $globalGestores,    $index, 'Gestor',      $errorCallback)
                            || !$this->validateAndAssign($dato, 'responsable', $localResponsable, $globalResponsable, $index, 'Responsable', $errorCallback)
                            || !$this->validateAndAssign($dato, 'asignado',    $localAsignado,    $globalAsignado,    $index, 'Asignado',    $errorCallback)
                        ) {
                            throw new \Exception("Error en la fila " . ($index + 1) . ": Falta un valor de asignación.");
                        }

                        // 4c) Activos asignación
                        $gestor      = $dato['gestor']      ?? auth()->user()->id;
                        $responsable = $dato['responsable'] ?? auth()->user()->id;
                        $asignado    = $dato['asignado']    ?? auth()->user()->id;

                        \App\Models\ActivosAsignacionModel::updateOrCreate(
                            ['id_activo' => $activo->id_activo],
                            [
                                'id_tipo'           => $dato['id_tipo'] ?? null,
                                'id_categoria'      => $dato['id_categoria'] ?? null,
                                'id_subcategoria'   => $dato['id_subcategoria'] ?? null,
                                'asignado_a'        => $asignado,
                                'gestionado_por'    => $gestor,
                                'fecha_asignacion'  => now()->format('Y-m-d H:i:s'),
                                'responsable'       => $responsable,
                                'empresa_empleados' => IdHelper::idEmpresa(),
                                'estado_asignacion' => 'Aceptado',
                            ]
                        );
                    }
                }
            });

            return true;
        } catch (\Exception $e) {
            $errorCallback("Error al importar los datos: " . $e->getMessage(), null);
            return false;
        }
    }


    public function confirmImportBien(
        array $datos,
        array $localSubcategoria = [],
        $globalSubcategoria = '',
        array $localCategoria = [],
        $globalCategoria = '',
        array $localTipo = [],
        $globalTipo = '',
        array $localSitAlta = [],
        $globalSitAlta = '',
        array $localEstadoGeneral = [],
        $globalEstadoGeneral = '',
        array $localUbicacion = [],
        $globalUbicacion = '',
        array $localGestores = [],
        $globalGestores = '',
        array $localResponsable = [],
        $globalResponsable = '',
        array $localAsignado = [],
        $globalAsignado = '',
        $tipoOperacion,
        callable $errorCallback
    ): bool {
        try {
            // Utilizamos DB::transaction() que maneja automáticamente el commit y rollback.
            DB::transaction(function () use (
                $datos,
                $localSubcategoria,
                $globalSubcategoria,
                $localCategoria,
                $globalCategoria,
                $localTipo,
                $globalTipo,
                $localSitAlta,
                $globalSitAlta,
                $localEstadoGeneral,
                $localGestores,
                $localResponsable,
                $localAsignado,
                $globalEstadoGeneral,
                $localUbicacion,
                $globalUbicacion,
                $globalGestores,
                $globalResponsable,
                $globalAsignado,
                $errorCallback
            ) {
                foreach ($datos as $index => $dato) {
                    if (
                        !$this->validateAndAssign($dato, 'id_subcategoria', $localSubcategoria, $globalSubcategoria, $index, 'Subcategoría', $errorCallback)
                        || !$this->validateAndAssign($dato, 'id_categoria', $localCategoria, $globalCategoria, $index, 'Categoría', $errorCallback)
                        || !$this->validateAndAssign($dato, 'id_tipo', $localTipo, $globalTipo, $index, 'Tipo', $errorCallback)
                        || !$this->validateAndAssign($dato, 'id_estado_sit_general', $localEstadoGeneral, $globalEstadoGeneral, $index, 'Estado Sit General', $errorCallback)
                        || !$this->validateAndAssign($dato, 'id_estado_sit_alta', $localSitAlta, $globalSitAlta, $index, 'Estado Sit Alta', $errorCallback)
                        || !$this->validateAndAssign($dato, 'id_ubicacion', $localUbicacion, $globalUbicacion, $index, 'Ubicación', $errorCallback)
                        || !$this->validateAndAssign($dato, 'gestor', $localGestores, $globalGestores, $index, 'Gestor', $errorCallback)
                        || !$this->validateAndAssign($dato, 'responsable', $localResponsable, $globalResponsable, $index, 'Responsable', $errorCallback)
                        || !$this->validateAndAssign($dato, 'asignado', $localAsignado, $globalAsignado, $index, 'Asignado', $errorCallback)
                    ) {
                        // Si no pasa la validación, lanzar una excepción para que la transacción se revierta
                        throw new \Exception("Error en la fila " . ($index + 1) . ": Falta un valor requerido.");
                    }

                    if (empty($dato['id_ubicacion'])) {
                        throw new \Exception("Error en la fila " . ($index + 1) . ": La Ubicación no puede estar vacía.");
                        return false;
                    }

                    $empresaTitular = $dato['empresa_titular'] ?? null;

                    // Buscar empresa en la tabla empresas_o_particulares
                    $empresaEncontrada = EmpresasModel::where(function ($query) use ($empresaTitular) {
                        if (is_numeric($empresaTitular)) {
                            $query->where('cuit', $empresaTitular)
                                ->where('estado', 'Aceptado');
                        } else {
                            $query->where('razon_social', $empresaTitular)
                                ->where('estado', 'Aceptado');
                        }
                    })->first();

                    $idEmpresaTitular = $empresaEncontrada->cuit ?? null;

                    // Crear o actualizar el registro en la base de datos
                    $activo = \App\Models\ActivosModel::updateOrCreate(
                        [
                            'nombre' => $dato['nombre'] ?? null,
                        ],
                        [
                            'id_externo' => $dato['id_externo'] ?? null,
                            'id_estado_sit_alta' => $dato['id_estado_sit_alta'],
                            'estado_inventario' => 'Alta',
                            'id_ubicacion' => $dato['id_ubicacion'] ?? null,
                            'id_subcategoria' => $dato['id_subcategoria'] ?? null,
                            'id_categoria' => $dato['id_categoria'] ?? null,
                            'id_tipo' => $dato['id_tipo'] ?? null,
                            'id_estado_sit_general' => $dato['id_estado_sit_general'] ?? null,
                            'usuario_titular' => (isset($dato['usuario_titular']) && is_numeric($dato['usuario_titular']))
                                ? $dato['usuario_titular']
                                : null,
                            'empresa_titular' => $idEmpresaTitular,
                            'propietario' => 'Cliente',
                        ]
                    );


                    // Insertar en la tabla activos_compartidos
                    \App\Models\ActivosCompartidosModel::updateOrCreate(
                        [
                            'id_activo' => $activo->id_activo, // Condición para buscar el registro
                        ],
                        // Valores a actualizar o crear
                        [
                            'id_subcat' => $dato['id_subcategoria'] ?? null,
                            'id_cat' => $dato['id_categoria'] ?? null,
                            'id_tipo' => $dato['id_tipo'] ?? null,
                            'empresa_titular' => $idEmpresaTitular,
                            'empresa_proveedora' => IdHelper::idEmpresa(),
                            'estado_asignacion' => 'Aceptado'
                        ]
                    );

                    // Si el gestor no se pasa, asignar el usuario logueado como el gestor por defect
                    $gestor = isset($dato['gestor']) && $dato['gestor'] ? $dato['gestor'] : auth()->user()->id;
                    $responsable = isset($dato['asignado']) && $dato['asignado'] ? $dato['asignado'] : auth()->user()->id;

                    // // Insertar en la tabla activos_asignacion
                    \App\Models\ActivosAsignacionModel::updateOrCreate(
                        [
                            'id_activo' => $activo->id_activo,
                        ],
                        [
                            'id_tipo' => $dato['id_tipo'] ?? null,
                            'id_categoria' => $dato['id_categoria'] ?? null,
                            'id_subcategoria' => $dato['id_subcategoria'] ?? null,
                            'asignado_a' => $dato['responsable'] ?? null,
                            'gestionado_por' => $gestor,
                            'fecha_asignacion' => Carbon::parse($this->fecha_asignacion)->format('Y-m-d H:i:s'),
                            'responsable' => $responsable,
                            'empresa_empleados' => IdHelper::idEmpresa(),
                            'estado_asignacion' => 'Aceptado'
                        ]
                    );
                }
            });

            // Si todo fue exitoso, devolver true
            return true;
        } catch (\Exception $e) {
            // En caso de error, llamar al callback de error
            $errorCallback("Error al importar los datos: " . $e->getMessage(), null);
            return false;
        }
    }

    /**
     * Valida y asigna un valor para un campo, usando primero el valor local y luego el global.
     *
     * @param array $dato           Referencia al arreglo de datos del registro.
     * @param string $fieldKey       Clave del campo a asignar (ej. 'id_subcategoria').
     * @param array $localArray      Arreglo con los valores locales.
     * @param mixed $globalValue     Valor global a usar si el local no está definido.
     * @param int $index             Índice de la fila.
     * @param string $fieldDisplay   Nombre para mostrar en el mensaje de error.
     * @param callable $errorCallback Callback para reportar errores.
     * @return bool                  Retorna true si se asignó un valor; false en caso contrario.
     */
    protected function validateAndAssign(
        array &$dato,
        string $fieldKey,
        array &$localArray,
        &$globalValue,
        int $index,
        string $fieldDisplay,
        callable $errorCallback
    ): bool {
        // Asignar el valor del array local si está presente y no vacío
        if (isset($localArray[$index]) && $localArray[$index] !== '') {
            $dato[$fieldKey] = $localArray[$index];
        }
        // Si no está en el local, asignar el valor global
        elseif ($globalValue !== '') {
            $dato[$fieldKey] = $globalValue;
        }

        // Validación si el campo es obligatorio y está vacío (sin contar los valores nulos)
        if ($dato[$fieldKey] === null || $dato[$fieldKey] === '') {
            // Llamar al callback con un mensaje de error si no se asignó un valor válido
            if (!in_array($fieldKey, ['gestor', 'responsable', 'asignado'])) {
                $errorCallback("Error en la fila " . ($index + 1) . ": Falta el valor de $fieldDisplay.", $index);
                return false;
            }
        }
        return true;
    }

    private function procesarArchivo($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $datos = [];

        if ($extension === 'json') {
            $content = file_get_contents($file->getRealPath());
            $datos = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al procesar el archivo JSON: ' . json_last_error_msg());
            }
            // Si el JSON tiene una clave "Worksheet", usarla para obtener el array de registros
            if (isset($datos['Worksheet']) && is_array($datos['Worksheet'])) {
                $datos = $datos['Worksheet'];
            }
        } elseif ($extension === 'xml') {
            $content = file_get_contents($file->getRealPath());
            $xmlObject = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xmlObject === false) {
                throw new Exception('Error al procesar el archivo XML.');
            }
            $json = json_encode($xmlObject);
            $datos = json_decode($json, true);
        } elseif ($extension === 'xlsx') {
            try {
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                if (count($sheetData) > 0) {
                    // La primera fila son los encabezados
                    $headers = array_shift($sheetData);
                    $datos = [];
                    foreach ($sheetData as $row) {
                        $record = [];
                        foreach ($headers as $col => $header) {
                            $record[$header] = $row[$col] ?? null;
                        }
                        $datos[] = $record;
                    }
                }
            } catch (Exception $e) {
                throw new Exception('Error al procesar el archivo XLSX: ' . $e->getMessage());
            }
        } else {
            throw new Exception('Tipo de archivo no soportado.');
        }

        return $datos;
    }

    /**
     * Valida que el registro contenga todas las columnas requeridas.
     *
     * @param array $record Registro a validar.
     * @param array $requiredKeys Claves requeridas.
     * @throws Exception Si falta alguna columna requerida.
     */
    private function validarColumnas(array $datos, array $requiredKeys): void
    {
        // Normaliza las claves del registro.
        $normalizarDatos = [];
        foreach ($datos as $key => $value) {
            $normalizarDatos[$this->normalizarKey($key)] = $value;
        }
        // Normaliza las claves requeridas.
        $normalizarRequeridos = array_map([$this, 'normalizarKey'], $requiredKeys);

        // Busca claves faltantes.
        $missingKeys = [];
        foreach ($normalizarRequeridos  as $reqKey) {
            if (!array_key_exists($reqKey, $normalizarDatos)) {
                $missingKeys[] = $reqKey;
            }
        }

        if (!empty($missingKeys)) {
            throw new Exception(
                'El archivo no contiene las siguientes columnas requeridas: ' . implode(', ', $missingKeys)
            );
        }
    }
    /**
     * Normaliza una cadena: quita espacios, pasa a minúsculas y reemplaza espacios por guiones bajos.
     */
    private function normalizarKey(string $key): string
    {
        return str_replace(' ', '_', strtolower(trim($key)));
    }

    public function confirmarProveedores(
        array $datos,
        $tipoOperacion,
        callable $errorCallback
    ): bool {
        try {
            DB::transaction(function () use ($datos, $tipoOperacion, $errorCallback) {
                foreach ($datos as $index => $dato) {
                    // 1) Verificar si ya existe en 'mis_proveedores' (para evitar duplicar)
                    if ($tipoOperacion == 'Insertar') {
                        $existe = \App\Models\MisProveedoresModel::where('razon_social', $dato['razon_social'] ?? '')
                            ->where('id_usuario', auth()->id())
                            ->where('empresa', IdHelper::idEmpresa())
                            ->where(function ($query) use ($dato) {
                                if (isset($dato['cuit'])) {
                                    $query->where('cuit', $dato['cuit']);
                                } elseif (isset($dato['cuil'])) {
                                    $query->where('cuil', $dato['cuil']);
                                }
                            })
                            ->exists();
                        if ($existe) {
                            // Si ya existe, omite este registro y continúa
                            continue;
                        }
                    }

                    // 2) Determinar si existe en la plataforma
                    $existePlataformaCollection = $this->existeEnPlataforma($dato);
                    $existePlataforma = $existePlataformaCollection->count() > 0 ? 'Si' : 'No';

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
                        $coordinates = $this->geocodingService->geocodeAddress($addressComponents);
                        if ($coordinates) {
                            $dato['lat']  = $coordinates['lat'];
                            $dato['long'] = $coordinates['lon'];
                        }
                    }
                    // 4) Si no existe en la plataforma ('No'), inserta/actualiza primero en 'empresas_o_particulares'
                    if ($existePlataforma === 'No') {
                        \App\Models\EmpresasModel::updateOrCreate(
                            ['cuit' => $dato['cuit'] ?? null],
                            [
                                'razon_social' => $dato['razon_social'] ?? null,
                                'localidad'    => $dato['localidad']    ?? null,
                                'provincia'    => $dato['provincia']    ?? null,
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


    public function importFileClientes($file, array $requiredKeys = []): array
    {
        //funcion para refactorizar el codigo de verificacion de archivo para evitar repetir codigo
        $datos = $this->procesarArchivo($file);
        // Validar que el archivo contenga las columnas requeridas, si se especifican.
        if (!empty($requiredKeys)) {
            $primerRegistro = reset($datos);
            if (!is_array($primerRegistro)) {
                throw new Exception('El archivo no contiene registros válidos.');
            }
            $this->validarColumnas($primerRegistro, $requiredKeys);
        }
        // Normalización de todos los registros: convertir todas las claves a minúsculas y reemplazar espacios por guiones bajos.
        $datosNormalizados = [];
        foreach ($datos as $registro) {
            $registroNormalizado = [];
            foreach ($registro as $key => $value) {
                $normalizedKey = str_replace(' ', '_', strtolower(trim($key)));
                $registroNormalizado[$normalizedKey] = $value;
            }
            $todosVacios = true;
            foreach ($registroNormalizado as $valor) {
                if ((is_string($valor) && trim($valor) !== '') || (!is_string($valor) && !is_null($valor))) {
                    $todosVacios = false;
                    break;
                }
            }
            if ($todosVacios) {
                continue; // descartar esta fila
            }
            $datosNormalizados[] = $registroNormalizado;
        }

        return $datosNormalizados;
    }

    public function confirmarClientes(
        array $datos,
        $tipoOperacion,
        callable $errorCallback
    ): bool {
        try {
            DB::transaction(function () use ($datos, $tipoOperacion, $errorCallback) {
                foreach ($datos as $index => $dato) {
                    // 1) Verificar si ya existe (para Insertar)
                    if ($tipoOperacion === 'Insertar') {
                        $existe = \App\Models\User::where('cuil', $dato['cuil'] ?? '')
                            ->orWhere('email', $dato['email'])->exists();
                        if ($existe) {
                            // Si ya existe, omite este registro y continúa
                            continue;
                        }
                    }
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
                            'email'      => $dato['email'],
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

    public function importFileUsuario($file, array $requiredKeys = []): array
    {
        //funcion para refactorizar el codigo de verificacion de archivo para evitar repetir codigo
        $datos = $this->procesarArchivo($file);
        // Validar que el archivo contenga las columnas requeridas, si se especifican.
        if (!empty($requiredKeys)) {
            $primerRegistro = reset($datos);
            if (!is_array($primerRegistro)) {
                throw new Exception('El archivo no contiene registros válidos.');
            }
            $this->validarColumnas($primerRegistro, $requiredKeys);
        }
        // Normalización de todos los registros: convertir todas las claves a minúsculas y reemplazar espacios por guiones bajos.
        $datosNormalizados = [];
        foreach ($datos as $registro) {
            $registroNormalizado = [];
            foreach ($registro as $key => $value) {
                $normalizedKey = str_replace(' ', '_', strtolower(trim($key)));
                $registroNormalizado[$normalizedKey] = $value;
            }
            $todosVacios = true;
            foreach ($registroNormalizado as $valor) {
                if ((is_string($valor) && trim($valor) !== '') || (!is_string($valor) && !is_null($valor))) {
                    $todosVacios = false;
                    break;
                }
            }
            if ($todosVacios) {
                continue; // descartar esta fila
            }
            $datosNormalizados[] = $registroNormalizado;
        }

        return $datosNormalizados;
    }

    public function confirmarUsuarios(
        array $datos,
        array $localTipoUsuario = [],
        $globalTipoUsuario = '',
        array $localEmpresa = [],
        $globalEmpresa = '',
        $tipoOperacion,
        callable $errorCallback
    ): bool {
        try {
            DB::transaction(function () use (
                $datos,
                $localTipoUsuario,
                $globalTipoUsuario,
                $localEmpresa,
                $globalEmpresa,
                $tipoOperacion,
                $errorCallback
            ) {
                foreach ($datos as $index => $dato) {
                    // Si cuit está vacío, se asigna la empresa actual
                    if (empty($dato['cuit'])) {
                        $dato['cuit'] = IdHelper::idEmpresa();
                    }

                    // Valida y asigna el valor para tipo_usuario, rep_tecnico y cuit (empresa)
                    if (
                        !$this->validateAndAssign($dato, 'tipo_usuario', $localTipoUsuario, $globalTipoUsuario, $index, 'Tipo Usuario', $errorCallback) ||
                        !$this->validateAndAssign($dato, 'cuit', $localEmpresa, $globalEmpresa, $index, 'Empresa', $errorCallback)
                    ) {
                        throw new Exception("Error en la fila " . ($index + 1) . ": Falta un valor requerido.");
                    }

                    // Si la operación es Insertar, verificamos que no exista ya un usuario con el cuil o email
                    if ($tipoOperacion === 'Insertar') {
                        $user = \App\Models\User::where('cuil', $dato['cuil'] ?? '')
                            ->orWhere('email', $dato['email'])->first();

                        if ($user) {

                            // Verificar si el usuario ya está en UsuariosEmpresasModel
                            $usuarioEmpresaExistente = \App\Models\UsuariosEmpresasModel::where('id_usuario', $user->id)
                                ->where('cuit', IdHelper::idEmpresa())
                                ->first();


                            // Si el valor de usuarioEmpresa es null
                            if (!$usuarioEmpresaExistente) {
                                // Si el usuario pertenece a otra empresa, se considera Externo
                                $tipo_inter_exter = ($dato['cuit'] == IdHelper::idEmpresa()) ? 'Interno' : 'Externo';

                                $usuarioEmpresa = \App\Models\UsuariosEmpresasModel::create([
                                    'id_usuario' => $user->id,
                                    'cuit' => IdHelper::idEmpresa(),
                                    'cargo' => 'Empleado',
                                    'legajo' => $dato['legajo'] ?? null,
                                    'es_representante_tecnico' => 'No',
                                    'tipo_user' => $dato['tipo_usuario'] ?? null,
                                    'tipo_inter_exter' => $tipo_inter_exter,
                                    'estado' => 'Aceptado',
                                ]);

                                // Si es Externo, se registra en contrato_inter_prestadora
                                if ($tipo_inter_exter === 'Externo') {
                                    \App\Models\ContratoInterPrestadoraModel::create([
                                        'id_relacion' => $usuarioEmpresa->id_relacion,
                                        'id_usuario' => $user->id,
                                        'cuil_empresa' => IdHelper::idEmpresa(),
                                        'cuil_prestadora' => $dato['cuit'],
                                        'nmro_contrato' => isset($dato['nmro_contrato']) ? $dato['nmro_contrato'] : null,
                                    ]);
                                }
                            } else {

                                // dd($usuarioEmpresaExistente);
                                $tipo_inter_exter = ($dato['cuit'] == IdHelper::idEmpresa()) ? 'Interno' : 'Externo';

                                // Si ya existe la relación, actualizamos la información
                                $usuarioEmpresaExistente->update([
                                    'legajo' => $dato['legajo'] ?? $usuarioEmpresaExistente->legajo,
                                    'tipo_user' => $dato['tipo_usuario'] ?? $usuarioEmpresaExistente->tipo_user,
                                    'tipo_inter_exter' => $tipo_inter_exter,
                                    'estado' => 'Aceptado', // Ajusta el estado si es necesario
                                ]);

                                // Si es Externo, registrar o actualizar contrato_inter_prestadora
                                if ($usuarioEmpresaExistente->tipo_inter_exter === 'Externo') {
                                    \App\Models\ContratoInterPrestadoraModel::updateOrCreate(
                                        [
                                            'id_relacion' => $usuarioEmpresaExistente->id_relacion,
                                            'id_usuario' => $user->id,
                                        ],
                                        [
                                            'cuil_empresa' => IdHelper::idEmpresa(),
                                            'cuil_prestadora' => $dato['cuit'],
                                            'nmro_contrato' => isset($dato['nmro_contrato']) ? $dato['nmro_contrato'] : null,
                                        ]
                                    );
                                }
                            }
                        } else {

                            $tipo_panel = $this->obtenerTipoPanel($dato['cuit']);

                            // Si el usuario no existe, se crea en Users
                            $user = \App\Models\User::create([
                                'name' => $dato['name'] ?? null,
                                'cuil' => $dato['cuil'] ?? null,
                                'email' => $dato['email'] ?? null,
                                'password' => Hash::make($dato['password']) ?? null,
                                'tipo' => '2',
                                'estado' => $dato['estado'] ?? 1,
                                'panel_actual' => $tipo_panel,
                            ]);

                            if ($user->wasRecentlyCreated) {
                                $user->setShouldSendEmailVerification(true);
                                $user->sendEmailVerificationNotification();
                            }

                            $empresaActual = IdHelper::idEmpresa();

                            // Verificar si el usuario ya está asociado a una empresa
                            $usuarioEmpresa = \App\Models\UsuariosEmpresasModel::where('id_usuario', $user->id)
                                ->where('cuit', $empresaActual)->first();

                            // Insertar o actualizar la relación en UsuariosEmpresasModel.
                            if (!$usuarioEmpresa) {
                                // Si no está asociado, creamos la relación
                                $usuarioEmpresa = \App\Models\UsuariosEmpresasModel::create([
                                    'id_usuario' => $user->id,
                                    'cuit' => $empresaActual,
                                    'cargo' => 'Empleado',
                                    'legajo' => $dato['legajo'] ?? null,
                                    'es_representante_tecnico' => 'No',
                                    'tipo_user' => $dato['tipo_usuario'] ?? null,
                                    'tipo_inter_exter' => (!empty($dato['cuit']) && $dato['cuit'] == IdHelper::idEmpresa()) ? 'Interno' : 'Externo',
                                    'estado' => 'Aceptado',
                                ]);
                            } else {
                                // Si ya está asociado, verificamos si es la misma empresa
                                if ($usuarioEmpresa->cuit !== $dato['cuit']) {
                                    // Si está en otra empresa, crear una nueva relación con la empresa actual
                                    $usuarioEmpresa = \App\Models\UsuariosEmpresasModel::create([
                                        'id_usuario' => $user->id,
                                        'cuit' => IdHelper::idEmpresa(),
                                        'cargo' => 'Empleado',
                                        'legajo' => $dato['legajo'] ?? null,
                                        'es_representante_tecnico' => 'No',
                                        'tipo_user' => $dato['tipo_usuario'] ?? null,
                                        'tipo_inter_exter' => 'Externo',
                                        'estado' => 'Aceptado',
                                    ]);
                                }
                            }

                            // Si el usuario es externo, registrarlo en contrato_inter_prestadora
                            if ($usuarioEmpresa->tipo_inter_exter === 'Externo') {
                                \App\Models\ContratoInterPrestadoraModel::create([
                                    [
                                        'id_usuario' => $user->id,
                                        'cuil_empresa' => $empresaActual,
                                        'cuil_prestadora' => $dato['cuit'],
                                    ],
                                    [
                                        'id_relacion'   => $usuarioEmpresa->id,
                                        'nmro_contrato' => $dato['nmro_contrato'] ?? null,
                                    ]
                                ]);
                            }
                        }
                    } elseif ($tipoOperacion === 'Actualizar') {
                        $user = \App\Models\User::where('cuil', $dato['cuil'] ?? '')->orWhere('email', $dato['email'])->first();

                        if ($user) {
                            // Verificar si ya existe la relación en UsuariosEmpresasModel
                            $usuarioEmpresaExistente = \App\Models\UsuariosEmpresasModel::where('id_usuario', $user->id)
                                ->where('cuit', IdHelper::idEmpresa())
                                ->first();
                            // dd($usuarioEmpresaExistente);
                            if (!$usuarioEmpresaExistente) {
                                // Si no está asociado a ninguna empresa, mostrar un error
                                return response()->json(['error' => 'Verifique la lista de usuarios.']);
                            } else {
                                // Si ya existe la relación, actualizar los datos
                                $tipo_inter_exter = ($dato['cuit'] == IdHelper::idEmpresa()) ? 'Interno' : 'Externo';

                                $usuarioEmpresaExistente->update([
                                    'legajo' => $dato['legajo'] ?? $usuarioEmpresaExistente->legajo,
                                    'tipo_user' => $dato['tipo_usuario'] ?? $usuarioEmpresaExistente->tipo_user,
                                    'tipo_inter_exter' => $tipo_inter_exter,
                                    'estado' => 'Aceptado',
                                ]);

                                // Si es Externo, actualizar contrato
                                if ($usuarioEmpresaExistente->tipo_inter_exter === 'Externo') {
                                    \App\Models\ContratoInterPrestadoraModel::updateOrCreate(
                                        [
                                            'id_relacion' => $usuarioEmpresaExistente->id_relacion,
                                            'id_usuario' => $user->id,
                                        ],
                                        [
                                            'cuil_empresa' => IdHelper::idEmpresa(),
                                            'cuil_prestadora' => $dato['cuit'],
                                            'nmro_contrato' => $dato['nmro_contrato'] ?? null,
                                        ]
                                    );
                                }
                            }
                        }
                    }
                }
            });
            return true;
        } catch (Exception $e) {
            $errorCallback("Error al importar los datos: " . $e->getMessage(), null);
            return false;
        }
    }

    private function obtenerTipoPanel($cuit)
    {
        // Buscamos la empresa por cuit
        $empresa = EmpresasModel::find($cuit);
        if (!$empresa) {
            return null; // O algún valor por defecto
        }

        // Dependiendo del id_tipo_empresa, retornamos el panel correspondiente
        // Asumimos que $empresa->tipo contiene el id_tipo_empresa
        switch ($empresa->tipo) {
            case 2:
                return 'Prestadora';
            case 3:
                return 'Controladora';
            case 4:
                return 'Estado';
            default:
                return 'Usuario';
        }
    }
}
