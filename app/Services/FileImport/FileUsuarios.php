<?php

namespace App\Services\FileImport;

use Exception;
use App\Services\FileImport\ProcessFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\IdHelper;
use App\Models\UsuariosEmpresasModel;

class FileUsuarios extends ProcessFile
{
    public $dependeCrudo;
    protected array $empresaTipoMap = [];
    protected array $contratosMap = []; // key: "rel|user|prestadora" => row

    // Método para importar archivo de usuarios, delega a importFile de la clase padre
    public function importFileUsuario($file, array $requiredKeys = []): array
    {
        return $this->importFile($file, $requiredKeys);
    }

    private function preloadEmpresaTipos(array $datos): void
    {
        // Tomo los CUIT prestadora declarados en el archivo + la empresa actual
        $cuitSet = collect($datos)
            ->pluck('cuit_empresa')
            ->filter()
            ->unique()
            ->push(IdHelper::idEmpresa())
            ->unique()
            ->values()
            ->all();

        if (empty($cuitSet)) return;

        // 1 sola consulta
        $this->empresaTipoMap = \App\Models\EmpresasModel::query()
            ->whereIn('cuit', $cuitSet)
            ->pluck('tipo', 'cuit')
            ->toArray();
    }

    private function obtenerTipoPanelFast($cuit): ?string
    {
        if (!$cuit) return null;
        $tipo = $this->empresaTipoMap[$cuit] ?? null;

        return match ($tipo) {
            '2' => 'Prestadora',
            '3' => 'Controladora',
            '4' => 'Estado',
            default => 'Usuario',
        };
    }

    private function preloadContratos($relacionesExistentes, array $usuarioIds): void
    {
        // normalizo a Collection
        $relaciones = collect($relacionesExistentes);

        $relIds = $relaciones->pluck('id_relacion')->values();

        if ($relIds->isEmpty() || empty($usuarioIds)) {
            $this->contratosMap = [];
            return;
        }

        $rows = \App\Models\ContratoInterPrestadoraModel::query()
            ->whereIn('id_relacion', $relIds)
            ->whereIn('id_usuario', $usuarioIds)
            ->get(['id_contrato', 'id_relacion', 'id_usuario', 'cuil_empresa', 'cuil_prestadora', 'nmro_contrato']);

        // clave única para lookup rápido y evitar consultas repetidas luego
        $this->contratosMap = $rows->keyBy(function ($r) {
            return $r->id_relacion . '|' . $r->id_usuario . '|' . $r->cuil_prestadora;
        })->all();
    }

    // File: app/Services/FileImport/FileUsuarios.php

    private function resolveSupervisores(array &$datos): void
    {
        $empresaActual = IdHelper::idEmpresa();

        // helper para obtener el texto que venga en la columna de supervisor
        $getRaw = static function (array $row): string {
            foreach (['supervisor usuario', 'supervisor_usuario', 'Supervisor Usuario', 'Supervisor_Usuario'] as $k) {
                if (array_key_exists($k, $row) && $row[$k] !== null && $row[$k] !== '') {
                    return trim((string)$row[$k]);
                }
            }
            return '';
        };

        // 1) Tokens (id/cuil/nombre) detectando la clave que exista
        $tokens = [];
        foreach ($datos as $r) {
            $raw = $getRaw($r);
            if ($raw !== '') $tokens[] = $raw;
        }

        // Si no hay nada, inicializo columnas y salgo
        if (empty($tokens)) {
            foreach ($datos as &$r) {
                // id para BD
                $r['supervisor_usuario'] = $r['supervisor_usuario'] ?? null;
                // label para mostrar (si vino con alguna variante, la copio)
                $r['supervisor usuario'] = $getRaw($r);
            }
            unset($r);
            return;
        }

        // 2) separar ids/cuils y nombres
        $ids = $cuits = $names = [];
        foreach ($tokens as $t) {
            if (ctype_digit($t)) {
                $ids[] = (int)$t;
                $cuits[] = $t;
            } else {
                $names[] = $t;
            }
        }
        $ids   = array_values(array_unique($ids));
        $cuits = array_values(array_unique($cuits));
        $names = array_values(array_unique($names));

        // 3) candidatos (por id, cuil o nombre)
        $users = User::query()
            ->when($ids,   fn($q) => $q->orWhereIn('id', $ids))
            ->when($cuits, fn($q) => $q->orWhereIn(DB::raw('CAST(cuil AS VARCHAR(20))'), $cuits))
            ->when($names, fn($q) => $q->orWhereIn('name', $names))
            ->get();

        $byId     = $users->keyBy('id');
        $byCuil   = $users->keyBy(fn($u) => preg_replace('/\D+/', '', (string)$u->cuil));
        $byLcName = $users->groupBy(fn($u) => mb_strtolower($u->name));

        // 4) validar que además sean supervisores en la empresa actual
        $candidateIds = $users->pluck('id')->all();
        $valid = UsuariosEmpresasModel::where('cuit', $empresaActual)
            ->where('supervisor', 1)
            ->whereIn('id_usuario', $candidateIds)
            ->pluck('id_usuario')
            ->flip();

        // 5) resolver por fila
        foreach ($datos as &$row) {
            $raw  = $getRaw($row);
            $uid  = null;
            $name = '';

            if ($raw !== '') {
                if (ctype_digit($raw)) {
                    $n = (int)$raw; // ¿id?
                    if (isset($byId[$n]) && $valid->has($n)) {
                        $uid = $n;
                        $name = $byId[$n]->name;
                    } else {
                        // ¿cuil?
                        $c = preg_replace('/\D+/', '', $raw);
                        if ($c !== '' && isset($byCuil[$c]) && $valid->has($byCuil[$c]->id)) {
                            $uid  = $byCuil[$c]->id;
                            $name = $byCuil[$c]->name;
                        }
                    }
                } else {
                    $lista = $byLcName[mb_strtolower($raw)] ?? collect();
                    $m = $lista->first(fn($u) => $valid->has($u->id));
                    if ($m) {
                        $uid = $m->id;
                        $name = $m->name;
                    }
                }
            }

            // INT para BD, string para mostrar
            $row['supervisor_usuario'] = $uid;   // id o null
            $row['supervisor usuario'] = $name;  // nombre o ""
        }
        unset($row);
    }

    /**
     * Útil si querés correr la resolución ANTES de confirmar, para que en la vista
     * ya aparezca el nombre (se puede llamar desde el componente Livewire).
     */
    public function precalcularSupervisoresParaVista(array &$rows): void
    {
        $this->resolveSupervisores($rows);
    }

    // Método principal para confirmar e insertar o actualizar usuarios a partir de datos importados
    public function confirmarUsuarios(
        array $datos,
        $tipoOperacion,
        callable $errorCallback,
        array $localNombre = [],
        string $globalNombre = '',
        array $localApellido = [],
        string $globalApellido = '',
        array $localEmail = [],
        string $globalEmail = '',
        array $localCuil = [],
        string $globalCuil = '',
        array $localLegajo = [],
        string $globalLegajo = '',
        array $localEmpresa = [],
        string $globalEmpresa = '',
        array $localPassword = [],
        string $globalPassword = '',
        array $localNumContrato = [],
        string $globalNumContrato = '',
        array $local_tipo_usario = [],
        string $Global_tipo_usario = ''
    ): mixed {

        $errores = [];
        $filasYaRelacionados = [];

        // Obtener lista de CUIL y Email únicos para evitar duplicados
        $cuilList = collect($datos)->pluck('cuil')->filter()->unique()->toArray();
        $emailList = collect($datos)->pluck('email')->filter()->unique()->toArray();

        // Obtener usuarios existentes por CUIL y Email
        $usuarios = \App\Models\User::whereIn('cuil', $cuilList)
            ->orWhereIn('email', $emailList)
            ->get();

        // Agrupar usuarios por CUIL y Email para facilitar la búsqueda
        $usuariosPorCuil = $usuarios->filter(fn($u) => $u->cuil)->keyBy('cuil');
        $usuariosPorEmail = $usuarios->filter(fn($u) => $u->email)->keyBy('email');

        // Obtener ID de los usuarios
        $usuarioIds = $usuarios->pluck('id')->toArray();

        // Obtener relaciones existentes de usuarios con la empresa actual
        $relacionesExistentes = \App\Models\UsuariosEmpresasModel::whereIn('id_usuario', $usuarioIds)
            ->where('cuit', IdHelper::idEmpresa())
            ->get()
            ->keyBy('id_usuario');

        // Validación previa de cada fila y asignación de valores corregidos o globales
        foreach ($datos as $index => &$dato) {
            // Si el campo CUIT EMPRESA está vacío, se asigna la empresa actual
            $cuitEmpresa = !empty($localEmpresa[$index]) ? $localEmpresa[$index]
                : (!empty($globalEmpresa) ? $globalEmpresa
                    : (!empty($dato['cuit_empresa']) ? $dato['cuit_empresa']
                        : IdHelper::idEmpresa()));

            $dato['cuit_empresa'] = $cuitEmpresa;

            // Obtener tipo usuario desde los valores corregidos (formulario) o global o dato original
            $tipoUser = $local_tipo_usario[$index] ?? $Global_tipo_usario ?? $dato['tipo_usuario'] ?? null;

            // Guardamos el tipo de usuario para usarlo en inserción/actualización
            $dato['tipo_user'] = $tipoUser;

            // Validar duplicados solo si la operación es insertar
            if ($tipoOperacion == 'Insertar') {
                $user = $usuariosPorCuil[$dato['cuil']] ?? $usuariosPorEmail[$dato['email']] ?? null;

                if ($user && isset($relacionesExistentes[$user->id])) {
                    $msg = "El usuario con Email '{$dato['email']}' o CUIL '{$dato['cuil']}' ya está registrado para esta empresa. Use 'Actualizar' para modificar sus datos.";
                    $errorCallback($msg, $index);
                    $errores[] = 'usuario_duplicado';
                    continue;
                }
            }

            // Validaciones específicas para campos obligatorios y su asignación con posibles correcciones
            $valido = $this->validateAndAssign($dato, 'nombre', $localNombre, $globalNombre, $index, 'Nombre', $errorCallback);

            if (!$valido) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Nombre' es inválido o está vacío.";
                continue;
            }

            $valido = $this->validateAndAssign($dato, 'apellido', $localApellido, $globalApellido, $index, 'Apellido', $errorCallback);

            if (!$valido) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Apellido' es inválido o está vacío.";
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

            $validoCuil = $this->validateAndAssign($dato, 'cuil', $localCuil, $globalCuil, $index, 'CUIL', $errorCallback);

            if (!$validoCuil) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'CUIL' es inválido o está vacío.";
                continue;
            }
        }

        // Si hay errores detectados, devolverlos o el código específico para duplicados y no insertar nada
        if (!empty($errores)) {
            // Verificamos si alguno es por duplicado para informarlo al componente
            if (in_array('usuario_duplicado', $errores)) {
                return 'usuario_duplicado';
            }
            return false;
        }

        // 👇 NORMALIZACIÓN TEMPRANA
        foreach ($datos as &$d) {
            $d = $this->normalizeUsuariosBooleanos($d);
        }
        unset($d);

        try {
            $this->resolveSupervisores($datos);

            $this->preloadEmpresaTipos($datos);
            $this->preloadContratos($relacionesExistentes, $usuarios->pluck('id')->all());

            // Se ejecuta una transacción para asegurar integridad de la operación completa
            DB::transaction(function () use (
                $datos,
                $tipoOperacion,
                $errorCallback,
                &$errores,
                $usuariosPorCuil,
                $usuariosPorEmail,
                $relacionesExistentes,
            ) {

                $empresaActual = IdHelper::idEmpresa();
                $contador = 0;

                foreach ($datos as $dato) {
                    $contador++;

                    // Buscar usuario existente por cuil o email
                    $user = $usuariosPorCuil[$dato['cuil']] ?? $usuariosPorEmail[$dato['email']] ?? null;

                    // Lógica para insertar un nuevo usuario
                    if ($tipoOperacion === 'Insertar') {

                        if ($user) {

                            // Verificar si usuario ya está relacionado con la empresa actual
                            $usuarioEmpresaExistente = $relacionesExistentes[$user->id] ?? null;

                            // Determinar si el usuario es interno o externo según empresa relacionada
                            $tipo_inter_exter = ($dato['cuit_empresa'] == $empresaActual) ? 'Interno' : 'Externo';

                            // Si no hay relación, crearla
                            if (!$usuarioEmpresaExistente) {

                                $usuarioEmpresa = \App\Models\UsuariosEmpresasModel::create([
                                    'id_usuario' => $user->id,
                                    'cuit' => IdHelper::idEmpresa(),
                                    'cargo' => 'Empleado',
                                    'legajo' => $dato['legajo'] ?? null,
                                    'es_representante_tecnico' => 'No',
                                    'tipo_user' => $dato['tipo usuario'] ?? 1,
                                    'tipo_inter_exter' => $tipo_inter_exter,
                                    'estado' => 'Aceptado',
                                    'supervisor' => (($dato['supervisor'] ?? 'No') === 'Si') ? 1 : 0,
                                    'id_Nivel_Organizacion' => $dato['depende_de'] ?? null,
                                    'supervisor_usuario' => (isset($dato['supervisor_usuario']) && is_numeric($dato['supervisor_usuario']))
                                        ? (int)$dato['supervisor_usuario']
                                        : null,
                                ]);

                                // Si el usuario es externo, crear contrato en tabla contrato_inter_prestadora
                                if ($tipo_inter_exter === 'Externo') {
                                    \App\Models\ContratoInterPrestadoraModel::create([
                                        'id_relacion' => $usuarioEmpresa->id_relacion,
                                        'id_usuario' => $user->id,
                                        'cuil_empresa' => $empresaActual,
                                        'cuil_prestadora' => $dato['cuit_empresa'],
                                        'nmro_contrato' => isset($dato['numcontrato']) ? $dato['numcontrato'] : null,
                                    ]);
                                }
                            }
                        } else {

                            // Si no existe el usuario, crear uno nuevo
                            $tipo_panel = $this->obtenerTipoPanelFast($dato['cuit_empresa']);
                            $name = trim($dato['nombre'] . ' ' . $dato['apellido']);

                            $user = \App\Models\User::create([
                                'name' => $name,
                                'cuil' => $dato['cuil'],
                                'email' => $dato['email'],
                                'password' => Hash::make($dato['password']),
                                'tipo' => '2',
                                'estado' => $dato['estado'] ?? 1,
                                'panel_actual' => $tipo_panel,
                            ]);

                            if ($dato['enviar_invitación'] === 'Si') {
                                DB::afterCommit(function () use ($user) {
                                    $user->sendEmailVerificationNotification();
                                });
                            }

                            // Determinar si el usuario es interno o externo según empresa
                            $tipo_inter_exter = (!empty($dato['cuit_empresa']) && $dato['cuit_empresa'] == $empresaActual) ? 'Interno' : 'Externo';

                            // Si no está asociado, creamos la relación
                            $usuarioEmpresa = \App\Models\UsuariosEmpresasModel::create([
                                'id_usuario' => $user->id,
                                'cuit' => $empresaActual,
                                'cargo' => 'Empleado',
                                'legajo' => $dato['legajo'] ?? null,
                                'es_representante_tecnico' => 'No',
                                'tipo_user' => $dato['tipo usuario'] ?? 1,
                                'tipo_inter_exter' => $tipo_inter_exter,
                                'estado' => 'Aceptado',
                                'supervisor' => (($dato['supervisor'] ?? 'No') === 'Si') ? 1 : 0,
                                'id_Nivel_Organizacion' => $dato['depende_de'] ?? null,
                                'supervisor_usuario' => (isset($dato['supervisor_usuario']) && is_numeric($dato['supervisor_usuario']))
                                    ? (int)$dato['supervisor_usuario']
                                    : null,
                            ]);

                            // Si cuit_empresa está vacío, asignar empresa actual
                            if (empty($dato['cuit_empresa'])) {
                                $dato['cuit_empresa'] = IdHelper::idEmpresa();
                            }

                            // Si el usuario es externo, registrarlo en contrato_inter_prestadora
                            if ($usuarioEmpresa->tipo_inter_exter === 'Externo') {
                                $datos = [
                                    'id_usuario' => $user->id,
                                    'cuil_empresa' => $empresaActual,
                                    'cuil_prestadora' => $dato['cuit_empresa'],
                                    'id_relacion'   => $usuarioEmpresa->id_relacion,
                                    'nmro_contrato' => $dato['numcontrato'] ?? null,
                                ];

                                \App\Models\ContratoInterPrestadoraModel::create($datos);
                            }
                        }
                    } elseif ($tipoOperacion === 'Actualizar') {

                        // Operación para actualizar usuarios existentes
                        $user = $usuariosPorCuil[$dato['cuil']] ?? $usuariosPorEmail[$dato['email']] ?? null;

                        if (!$user) {
                            // Si no se encuentra el usuario a actualizar, marcar error
                            $msg = "Error en la fila " . $contador . ": Usuario no asociado a la Empresa.";
                            $errorCallback($msg, $contador);
                            $errores[] = 'error';
                            continue;
                        }

                        if ($user) {
                            // Buscar relación usuario-empresa actual
                            $usuarioEmpresaExistente = $relacionesExistentes[$user->id] ?? null;

                            if ($usuarioEmpresaExistente) {

                                // Actualizar datos de relación y usuario
                                $tipo_inter_exter = ($dato['cuit_empresa'] == IdHelper::idEmpresa()) ? 'Interno' : 'Externo';

                                $tipo_panel = $this->obtenerTipoPanelFast($dato['cuit_empresa']);

                                // Actualizar datos del usuario en tabla users
                                \App\Models\User::where('cuil', $dato['cuil'])->update([
                                    'email' => $dato['email'],
                                    'tipo' => '2',
                                    'panel_actual' => $tipo_panel,
                                ]);

                                // Actualizar relación usuario-empresa
                                $usuarioEmpresaExistente->update([
                                    'legajo' => $dato['legajo'] ?? $usuarioEmpresaExistente->legajo,
                                    'tipo_user' => $dato['tipo_user'] ?? $usuarioEmpresaExistente->tipo_user,
                                    'tipo_inter_exter' => $tipo_inter_exter,
                                    'estado' => 'Aceptado',
                                    'supervisor' => (($dato['supervisor'] ?? 'No') === 'Si') ? 1 : 0,
                                    'id_Nivel_Organizacion' => $dato['depende_de'] ?? null,
                                    'supervisor_usuario' => (isset($dato['supervisor_usuario']) && is_numeric($dato['supervisor_usuario']))
                                        ? (int)$dato['supervisor_usuario']
                                        : null,
                                ]);

                                // Si es externo, actualizar o crear contrato, si no, eliminar contratos existentes
                                if ($usuarioEmpresaExistente->tipo_inter_exter === 'Externo') {

                                    \App\Models\ContratoInterPrestadoraModel::where('id_relacion', $usuarioEmpresaExistente->id_relacion)
                                        ->where('id_usuario', $user->id)
                                        ->delete();

                                    \App\Models\ContratoInterPrestadoraModel::updateOrCreate(
                                        [
                                            'id_relacion' => $usuarioEmpresaExistente->id_relacion,
                                            'id_usuario' => $user->id,
                                            'cuil_empresa' => IdHelper::idEmpresa(),
                                            'cuil_prestadora' => $dato['cuit_empresa'],
                                            'nmro_contrato' => $dato['numcontrato'] ?? null,
                                        ]
                                    );
                                } else {
                                    \App\Models\ContratoInterPrestadoraModel::where('id_relacion', $usuarioEmpresaExistente->id_relacion)
                                        ->where('id_usuario', $user->id)
                                        ->delete();
                                }
                            }
                        }
                    }
                }
            });

            // Si tras la transacción hay errores, devolver código correspondiente
            if (!empty($errores)) {
                if (in_array('error', $errores)) {
                    return 'error';
                }
                return false;
            }

            return true;
        } catch (Exception $e) {
            $errorCallback("Error al importar los datos: " . $e->getMessage(), null);
            return false;
        }
    }

    /** Normaliza Enviar Invitación, Supervisor y Supervisor Usuario */
    private function normalizeUsuariosBooleanos(array $row): array
    {
        // helper para leer variantes de nombres de columnas
        $get = function (array $row, array $keys): string {
            foreach ($keys as $k) {
                if (array_key_exists($k, $row) && $row[$k] !== null) {
                    return trim((string)$row[$k]);
                }
                $alt = strtolower(str_replace([' ', '"', '-'], ['_', '', '_'], $k));
                if (array_key_exists($alt, $row) && $row[$alt] !== null) {
                    return trim((string)$row[$alt]);
                }
            }
            return '';
        };

        // normaliza Si/No (vacío => No)
        $siNo = function (string $v): string {
            $v = mb_strtolower(trim($v));
            if ($v === '' || $v === 'no' || $v === '0' || $v === 'false' || $v === 'n') return 'No';
            if ($v === 'si' || $v === 'sí' || $v === '1' || $v === 'true' || $v === 's') return 'Si';
            return 'No'; // fallback defensivo
        };

        // Enviar Invitación
        $rawInv = $get($row, ['Enviar Invitación', 'enviar invitación', 'enviar_invitación']);
        $row['enviar_invitación'] = $siNo($rawInv);             // 'Si' | 'No'
        $row['enviar_invitacion'] = $row['enviar_invitación'];   // alias por si en otro lado se usa sin tilde

        // Supervisor (flag de la relación)
        $rawSup = $get($row, ['Supervisor', 'supervisor']);
        $row['supervisor'] = $siNo($rawSup);                    // 'Si' | 'No'

        return $row;
    }
}
