<?php

namespace App\Traits\Importaciones;

use Exception;
use App\Services\FileImport\FileImportService;
use App\Models\EmpresasModel;
use App\Services\FileImport\FileUsuarios;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Helpers\IdHelper;
use App\Models\UsuariosEmpresasModel;

trait UsuariosTrait
{
    public array $nivelesEmpresa = [];

    public function descargarEjemploUsuarios()
    {
        $this->descargarEjemplo('UsuarioArchivoEjemplo.xlsx', 'UsuarioArchivoEjemplo.xlsx');
    }

    public function abrirDependencia(array $payload)
    {
        $this->dispatch('openModalEditarDependencia', $payload)
            ->to(\App\Livewire\Empresas\EmpresasUsuarios\EditarDependenciaModal::class);
    }

    private function hidratarSupervisorUsuarioParaVista(array &$rows): void
    {
        $empresaActual = IdHelper::idEmpresa();

        // 1) Tokens de la columna "supervisor_usuario" (puede venir id, cuil o nombre)
        $tokens = [];
        foreach ($rows as $r) {
            $raw = trim((string)($r['supervisor_usuario'] ?? ''));
            if ($raw !== '') $tokens[] = $raw;
        }
        if (empty($tokens)) {
            foreach ($rows as &$r) {
                $r['supervisor_usuario'] = $r['supervisor_usuario'] ?? null;
                $r['supervisor_usuario'] = is_string($r['supervisor_usuario'] ?? '') ? $r['supervisor_usuario'] : '';
            }
            unset($r);
            return;
        }

        // 2) Separar posibles ids/cuils y nombres
        $ids = $cuits = $names = [];
        foreach ($tokens as $t) {
            if (ctype_digit($t)) {
                $ids[] = (int)$t;
                $cuits[] = $t;
            } // puede ser id o cuil
            else {
                $names[] = $t;
            }
        }
        $ids   = array_values(array_unique($ids));
        $cuits = array_values(array_unique($cuits));
        $names = array_values(array_unique($names));

        // 3) Traer usuarios candidatos (por id, cuil o nombre)
        $users = User::query()
            ->when($ids,   fn($q) => $q->orWhereIn('id', $ids))
            ->when($cuits, fn($q) => $q->orWhereIn(DB::raw('CAST(cuil AS VARCHAR(20))'), $cuits))
            ->when($names, fn($q) => $q->orWhereIn('name', $names))
            ->get();

        $byId     = $users->keyBy('id');
        $byCuil   = $users->keyBy(fn($u) => preg_replace('/\D+/', '', (string)$u->cuil));
        $byLcName = $users->groupBy(fn($u) => mb_strtolower($u->name));

        // 4) Validar que además sean SUPERVISORES (supervisor=1) en la empresa actual
        $candidateIds = $users->pluck('id')->all();
        $valid = UsuariosEmpresasModel::where('cuit', $empresaActual)
            ->where('supervisor', 1)
            ->whereIn('id_usuario', $candidateIds)
            ->pluck('id_usuario')
            ->flip(); // para lookup rápido

        // 5) Resolver fila a fila
        foreach ($rows as &$row) {
            $raw = trim((string)($row['supervisor_usuario'] ?? ''));
            $uid = null;
            $name = '';

            if ($raw !== '') {
                if (ctype_digit($raw)) {
                    // ¿Coincide con un ID?
                    $n = (int)$raw;
                    if (isset($byId[$n]) && $valid->has($n)) {
                        $uid = $n;
                        $name = $byId[$n]->name;
                    } else {
                        // ¿Coincide con un CUIL?
                        $c = preg_replace('/\D+/', '', $raw);
                        if ($c !== '' && isset($byCuil[$c]) && $valid->has($byCuil[$c]->id)) {
                            $uid = $byCuil[$c]->id;
                            $name = $byCuil[$c]->name;
                        }
                    }
                } else {
                    // Coincidencia exacta por nombre (case-insensitive)
                    $lista = $byLcName[mb_strtolower($raw)] ?? collect();
                    $m = $lista->first(fn($u) => $valid->has($u->id));
                    if ($m) {
                        $uid = $m->id;
                        $name = $m->name;
                    }
                }
            }

            $row['supervisor_usuario'] = $uid;   // ID o null (para BD)
            $row['supervisor_usuario'] = $name;  // nombre o "" (para mostrar)
        }
        unset($row);
    }

    public function importarArchivoUsuario(FileImportService $importService)
    {
        if (!$this->archivo) {
            $this->addError('archivo', 'No se ha seleccionado ningún archivo.');
            $this->limpiarUsuarios();
            return;
        }
        try {
            $requiredKeys = ['Nombre', 'Apellido', 'Email', 'Password', 'CUIL'];

            $importService = new FileUsuarios();

            $this->vistaPreviaUsuario = $importService->importFileUsuario($this->archivo, $requiredKeys);

            $this->hidratarSupervisorUsuarioParaVista($this->vistaPreviaUsuario);

            if (empty($this->vistaPreviaUsuario)) {
                $this->addError('archivo', 'El archivo no contiene registros válidos o faltan claves requeridas.');
                return;
            }

            foreach ($this->vistaPreviaUsuario as $i => $row) {
                $this->localSupervisorUsuario[$i] = $row['supervisor_usuario'] ?? null;
            }

            // Obtener lista de CUIT únicos desde la vista previa
            $cuitList = collect($this->vistaPreviaUsuario)
                ->pluck('cuit_empresa')
                ->filter()
                ->unique()
                ->toArray();

            // Verificamos los cuits buscando las empresas
            $empresas = EmpresasModel::whereIn('cuit', $cuitList)->get()->keyBy('cuit');

            foreach ($this->vistaPreviaUsuario as $index => $row) {
                $cuitFromFile = $row['cuit_empresa'] ?? '';
                // Verificamos si existe la empresa con el cuit del documento
                if (!empty($cuitFromFile) && isset($empresas[$cuitFromFile])) {
                    $this->localEmpresa[$index] = $cuitFromFile;
                } else {
                    $this->localEmpresa[$index] = '';
                }
                $this->local_tipo_usario[$index] = '';
                $this->representante_tecnico_local[$index] = '';
            }

            foreach ($this->vistaPreviaUsuario as $index => $row) {
                $tipo = strtolower(trim($row['tipo_usuario'] ?? ''));

                if ($tipo === 'administrador') {
                    $this->local_tipo_usario[$index] = '2';
                } elseif ($tipo === 'común' || $tipo === 'comun') {
                    $this->local_tipo_usario[$index] = '1';
                } else {
                    $this->local_tipo_usario[$index] = ''; // Por si falta o viene mal
                }
            }

            $empresaActual = \App\Helpers\IdHelper::idEmpresa();

            // Mapa Id => Nombre que usará la vista
            $mapNiveles = \App\Models\OrganizacionUnidadesModel::where('CuitEmpresa', $empresaActual)
                ->pluck('Nombre', 'Id')   // ['Id' => 'Nombre']
                ->toArray();

            $this->nivelesEmpresa = $mapNiveles;      // <-- esto es lo que “llega” a la vista
            $idsValidos = array_map('intval', array_keys($mapNiveles)); // solo IDs para validar

            // Normalizar cada fila de la vista previa
            foreach ($this->vistaPreviaUsuario as &$row) {
                $id = isset($row['depende_de']) ? (int) $row['depende_de'] : null;
                $row['depende_de'] = ($id && in_array($id, $idsValidos, true)) ? $id : null;
            }
            unset($row);

            $firstRecord = reset($this->vistaPreviaUsuario);
            if (!array_key_exists('cuit empresa', $firstRecord)) {
                $this->missingCuit = true;
                $this->columnsForCuitSelection = array_keys($firstRecord);
                return;
            }
            $this->resetErrorBag('archivo');
        } catch (Exception $e) {
            $this->addError('archivo', $e->getMessage());
            $this->dispatch('errorInfo', [
                'title'   => 'Error de Importación',
                'message' => $e->getMessage()
            ]);
            $this->limpiarUsuarios();
        }
    }

    // Otros métodos de confirmación y descarga se mantienen...
    public function confirmarUsuarios(FileImportService $importService)
    {
        if (empty($this->vistaPreviaUsuario)) {
            $this->mostrarErrorImportacion('No hay datos para importar.');
            return;
        }

        $importService = new FileUsuarios();

        $resultado = $importService->confirmarUsuarios(
            $this->vistaPreviaUsuario,
            $this->tipoOperacion,
            function ($errorMessage, $index) {
                $this->mostrarErrorImportacion($errorMessage);
            },
            $this->localNombre = [],
            $this->globalNombre = '',
            $this->localApellido = [],
            $this->globalApellido = '',
            $this->localEmail = [],
            $this->globalEmail = '',
            $this->localCuil = [],
            $this->globalCuil = '',
            $this->localLegajo = [],
            $this->globalLegajo = '',
            $this->localEmpresa,
            $this->globalEmpresa,
            $this->localPassword = [],
            $this->globalPassword = '',
            $this->localNumContrato = [],
            $this->globalNumContrato = '',
            $this->local_tipo_usario,
            $this->Global_tipo_usario
        );

        if ($resultado === true) {
            $this->mostrarExitoImportacion();
            $this->closeCompleto();
        } elseif ($resultado === 'usuario_duplicado' || $resultado === 'error') {
            // Resetear todo solo si es ese caso
            $this->limpiarUsuarios();
        }
    }

    private function limpiarUsuarios()
    {
        $this->vistaPreviaUsuario = [];
        $this->localSupervisorUsuario = [];
        $this->globalSupervisorUsuario = null;
        $this->local_tipo_usario = [];
        $this->Global_tipo_usario = '';
    }
}
