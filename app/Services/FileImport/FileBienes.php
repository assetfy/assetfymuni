<?php

namespace App\Services\FileImport;

use Exception;
use App\Services\FileImport\ProcessFile;
use App\Helpers\Funciones;
use App\Helpers\IdHelper;
use App\Models\AtributosSubcategoriaModel;
use Illuminate\Support\Facades\DB;
use App\Models\MarcasModel;
use App\Models\ModelosModel;
use App\Models\OrganizacionUnidadesModel;
use App\Models\UbicacionesModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class FileBienes extends ProcessFile
{
    protected function normalizeName(?string $s): string
    {
        $s = preg_replace('/\s+/u', ' ', trim((string)$s)); // colapsa espacios múltiples
        return mb_strtolower(\Illuminate\Support\Str::ascii($s));
    }

    protected ?int $idEstadoBaja = null;
    protected array $attrBundleCache = []; // key: "t|c|s" => ['attrDef'=>[], 'valNameToId'=>[], 'valIdSet'=>[]];

    // caches por ejecución
    protected array $dict_tipos = [];
    protected array $dict_categorias = [];     // key: "$tipoId"
    protected array $dict_subcategorias = [];  // key: "$tipoId|$catId"
    protected array $dict_est_alta = [];
    protected array $dict_est_gral = [];
    protected array $dict_condicion = [];
    protected array $dict_ubicaciones = [];    // key: "$cuit"
    protected array $dict_users = [];          // key: "$cuit"

    public $fecha_asignacion;
    protected array $tipos = [];
    protected array $categorias = [];
    protected array $subcategorias = [];
    protected array $estadosCache = [];
    protected array $modelos = [];
    protected array $marcas = [];
    protected array $estadosGenerales = [];
    protected array $estadosSitAlta = [];
    protected array $condiciones = [];
    protected array $ubicaciones = [];
    protected array $atributos = [];
    public $dependeCrudo;
    public $tipoDatos;

    public function importFileActivo($file, array $requiredKeys = []): array
    {
        return $this->importFile($file, $requiredKeys, function ($registro) {
            $cuitEmpresa = IdHelper::idEmpresa();

            $this->buildDictionaries($cuitEmpresa);

            // Validar y normalizar 
            $tipoCrudo = $registro['tipo'] ?? '';
            $categoriaCrudo = $registro['categoria'] ?? '';
            $subcategoriaCrudo = $registro['subcategoria'] ?? '';
            $modeloCrudo = $registro['modelo'] ?? '';
            $marcaCrudo = $registro['marca'] ?? '';
            $estadoGeneralCrudo = $registro['estado_general'] ?? '';
            $estadoAltaCrudo = $registro['estado_alta'] ?? '';
            $condicionCrudo = $registro['condicion'] ?? '';
            $ubicacionCrudo = $registro['ubicación'] ?? '';
            $usuarioTitularCrudo = $registro['usuario_titular'] ?? '';
            $responsableInvCrudo = $registro['responsable_inventario'] ?? '';
            $dependeCrudo = $registro['depende_de'];

            $tipoValido = $this->obtenerTipoValido($tipoCrudo);
            $tipoId = $tipoValido['id_tipo'] ?? null;

            $categoriaValido = $this->obtenerCategoriaValida($categoriaCrudo, $tipoId);
            $categoriaId = $categoriaValido['id_categoria'] ?? null;

            $subcategoriaValido = $this->obtenerSubcategoriaValida($subcategoriaCrudo, $tipoId, $categoriaId);
            $subcategoriaId = $subcategoriaValido['id_subcategoria'] ?? null;

            $marcaValida = $this->obtenerMarcaValida($marcaCrudo);
            $marcaId = $marcaValida['id_marca'] ?? null;

            $modeloValido = $this->obtenerModeloValido($modeloCrudo, $marcaId, $tipoId, $categoriaId, $subcategoriaId);
            $estadoGeneralValido = $this->obtenerEstadoGeneralValido($estadoGeneralCrudo);
            $estadoSitAltaValido = $this->obtenerEstadoSitAltaValido($estadoAltaCrudo);
            $condicionValida = $this->obtenerCondicionValida($condicionCrudo);
            $ubicacionValida = $this->obtenerUbicacionValida($ubicacionCrudo);
            $usuarioTitularValido = $this->obtenerUsuarioValido($usuarioTitularCrudo, $cuitEmpresa);
            $responsableInvValido = $this->obtenerUsuarioValido($responsableInvCrudo, $cuitEmpresa);
            $dependeValido = $this->obtenerOrganizacionValido($dependeCrudo, $cuitEmpresa);

            $registro['estado_general'] = $estadoGeneralValido['id_estado_sit_general'] ?? null;
            $registro['estado_alta'] = $estadoSitAltaValido['id_estado_sit_alta'] ?? null;
            $registro['condicion'] = $condicionValida['id_condicion'] ?? null;
            $registro['ubicación'] = $ubicacionValida['id_ubicacion'] ?? null;
            $registro['usuario_titular'] = $usuarioTitularValido['id'] ?? null;
            $registro['responsable_inventario'] = $responsableInvValido['id'] ?? null;
            $registro['tipo'] = $tipoValido['id_tipo'] ?? null;
            $registro['categoria'] = $categoriaValido['id_categoria'] ?? null;
            $registro['subcategoria'] = $subcategoriaValido['id_subcategoria'] ?? null;
            $registro['modelo'] = $modeloValido['id_modelo'] ?? null;
            $registro['marca'] = $marcaValida['id_marca'] ?? null;
            $registro['depende_de'] = $dependeValido ? $dependeValido->Id : null;

            return $registro;
        });
    }

    // construir diccionarios una sola vez (por import)
    protected function buildDictionaries(string $cuitEmpresa): void
    {
        if (!$this->dict_tipos) {
            $this->dict_tipos = \App\Models\TiposModel::query()
                ->get(['id_tipo', 'nombre'])
                ->reduce(fn($acc, $r) => ($acc[$this->normalizeName($r->nombre)] = (int)$r->id_tipo) ? $acc : $acc, []);
        }

        if (!$this->dict_est_alta) {
            $this->dict_est_alta = \App\Models\EstadosAltasModel::query()
                ->get(['id_estado_sit_alta', 'nombre'])
                ->reduce(fn($a, $r) => ($a[$this->normalizeName($r->nombre)] = (int)$r->id_estado_sit_alta) ? $a : $a, []);
        }

        if (!$this->dict_est_gral) {
            $this->dict_est_gral = \App\Models\EstadoGeneralModel::query()
                ->get(['id_estado_sit_general', 'nombre'])
                ->reduce(fn($a, $r) => ($a[$this->normalizeName($r->nombre)] = (int)$r->id_estado_sit_general) ? $a : $a, []);
        }

        if (!$this->dict_condicion) {
            $this->dict_condicion = \App\Models\CondicionModel::query()
                ->get(['id_condicion', 'nombre'])
                ->reduce(fn($a, $r) => ($a[$this->normalizeName($r->nombre)] = (int)$r->id_condicion) ? $a : $a, []);
        }

        if (!isset($this->dict_ubicaciones[$cuitEmpresa])) {
            $this->dict_ubicaciones[$cuitEmpresa] = \App\Models\UbicacionesModel::query()
                ->where('cuit_empresa', $cuitEmpresa) // **scoped por empresa**
                ->get(['id_ubicacion', 'nombre'])
                ->reduce(fn($a, $r) => ($a[$this->normalizeName($r->nombre)] = (int)$r->id_ubicacion) ? $a : $a, []);
        }

        if (!isset($this->dict_users[$cuitEmpresa])) {
            $this->dict_users[$cuitEmpresa] = \App\Models\User::query()
                ->join('act.usuarios_empresas as ue', 'ue.id_usuario', '=', 'users.id')
                ->where('ue.cuit', $cuitEmpresa)->where('ue.estado', 'Aceptado')
                ->get(['users.id', 'users.name', 'users.cuil'])
                ->reduce(function ($a, $r) {
                    $a['by_name'][$this->normalizeName($r->name)] = ['id' => (int)$r->id, 'name' => $r->name, 'cuil' => $r->cuil];
                    $a['by_id'][(int)$r->id] = ['id' => (int)$r->id, 'name' => $r->name, 'cuil' => $r->cuil];
                    return $a;
                }, ['by_name' => [], 'by_id' => []]);
        }
    }

    protected function idEstadoBaja(): int
    {
        if ($this->idEstadoBaja === null) {
            $this->idEstadoBaja = (int) Funciones::activoBaja();
        }
        return $this->idEstadoBaja;
    }

    protected function getAttrBundle(int $tipoId, int $catId, int $subcatId): array
    {
        $memKey = "{$tipoId}|{$catId}|{$subcatId}";
        if (isset($this->attrBundleCache[$memKey])) {
            return $this->attrBundleCache[$memKey];
        }

        $cacheKey = "act.attrBundle.$memKey";

        $bundle = Cache::remember($cacheKey, 86400, function () use ($tipoId, $catId, $subcatId) {
            // Traer SOLO lo necesario
            $rows = AtributosSubcategoriaModel::query()
                ->with([
                    'atributo:id_atributo,tipo_campo,predefinido,SelectM',
                    'valores:id_valor,id_atributo,valor'
                ])
                ->where('id_tipo', $tipoId)
                ->where('id_categoria', $catId)
                ->where('id_subcategoria', $subcatId)
                ->get(['id_atributo', 'id_tipo', 'id_categoria', 'id_subcategoria']);

            $attrDef     = []; // id_atributo => def (por si lo usás)
            $valNameToId = []; // id_atributo => [valor_normalizado => id_valor]
            $valIdSet    = []; // id_atributo => [id_valor,...]
            $attrMeta    = []; // id_atributo => ['tipo_campo'=>'1|2|3','predefinido'=>bool,'multiple'=>bool]

            foreach ($rows as $def) {
                $idA = (int) $def->id_atributo;
                $attrDef[$idA] = $def; // queda por compatibilidad

                $predef = strtolower((string)($def->atributo->predefinido ?? 'no')) === 'si';
                $multi  = strtolower((string)($def->atributo->SelectM ?? 'no')) === 'si';
                $tipoC  = (string)($def->atributo->tipo_campo ?? '1'); // 1=Texto, 2=Numérico, 3=Fecha

                $attrMeta[$idA] = [
                    'tipo_campo'  => $tipoC,
                    'predefinido' => $predef,
                    'multiple'    => $multi,
                ];

                foreach (($def->valores ?? []) as $v) {
                    $txt = mb_strtolower(trim((string)$v->valor));
                    $idv = (int) $v->id_valor;
                    $valNameToId[$idA][$txt] = $idv;
                    $valIdSet[$idA][] = $idv;
                }
            }

            return [
                'attrDef'     => $attrDef,
                'valNameToId' => $valNameToId,
                'valIdSet'    => $valIdSet,
                'meta'        => $attrMeta,
            ];
        });

        return $this->attrBundleCache[$memKey] = $bundle;
    }

    // helpers dependientes (categorías/subcategorías por ámbito)
    protected function categoriasDictForTipo(int $tipoId): array
    {
        if (!isset($this->dict_categorias[$tipoId])) {
            $this->dict_categorias[$tipoId] = \App\Models\CategoriaModel::query()
                ->where('id_tipo', $tipoId)
                ->get(['id_categoria', 'nombre'])
                ->reduce(fn($a, $r) => ($a[$this->normalizeName($r->nombre)] = (int)$r->id_categoria) ? $a : $a, []);
        }
        return $this->dict_categorias[$tipoId];
    }

    protected function subcategoriasDictFor(int $tipoId, int $catId): array
    {
        $k = "$tipoId|$catId";
        if (!isset($this->dict_subcategorias[$k])) {
            $this->dict_subcategorias[$k] = \App\Models\SubcategoriaModel::query()
                ->where('id_tipo', $tipoId)->where('id_categoria', $catId)
                ->get(['id_subcategoria', 'nombre'])
                ->reduce(fn($a, $r) => ($a[$this->normalizeName($r->nombre)] = (int)$r->id_subcategoria) ? $a : $a, []);
        }
        return $this->dict_subcategorias[$k];
    }

    private function obtenerOrganizacionValido($id, $cuitEmpresa = null)
    {
        if (empty($id)) {  // o $id === null o $id === ''
            return null; // no consultar si id es vacío o nulo
        }

        return OrganizacionUnidadesModel::where('id', $id)
            ->where('CuitEmpresa', $cuitEmpresa)
            ->first();
    }
    protected array $usuariosCache = [];

    protected function obtenerUsuarioValido(?string $nombre, string $cuitEmpresa): ?array
    {
        $nombre = $this->normalizeName($nombre ?? '');
        if ($nombre === '') return null;

        $hit = $this->dict_users[$cuitEmpresa]['by_name'][$nombre] ?? null;
        return $hit ?: null;
    }

    protected function obtenerUbicacionValida(string $v): ?array
    {
        $v = trim((string)$v);
        if ($v === '') return null;

        // numérico directo
        if (is_numeric($v)) {
            $row = \App\Models\UbicacionesModel::find((int)$v, ['id_ubicacion', 'nombre']);
            return $row ? $row->toArray() : null;
        }

        // nombre normalizado para lookup en dict
        $vn = $this->normalizeName($v);
        $cuit = IdHelper::idEmpresa(); // 🔧 CAMBIA AQUÍ si querés resolver por otra empresa
        $id = $this->dict_ubicaciones[$cuit][$vn] ?? null;
        return $id ? ['id_ubicacion' => $id, 'nombre' => null] : null;
    }

    protected function obtenerCondicionValida(string $v): ?array
    {
        $v = $this->normalizeName($v);
        if ($v === '') return null;
        $id = $this->dict_condicion[$v] ?? null;
        return $id ? ['id_condicion' => $id, 'nombre' => null, 'descripcion' => null] : null;
    }

    protected function obtenerEstadoSitAltaValido(string $v): ?array
    {
        $v = $this->normalizeName($v);
        if ($v === '') return null;
        $id = $this->dict_est_alta[$v] ?? null;
        return $id ? ['id_estado_sit_alta' => $id, 'nombre' => null, 'descripcion' => null] : null;
    }

    protected function obtenerEstadoGeneralValido(string $v): ?array
    {
        $v = $this->normalizeName($v);
        if ($v === '') return null;
        $id = $this->dict_est_gral[$v] ?? null;
        return $id ? ['id_estado_sit_general' => $id, 'nombre' => null] : null;
    }

    // Permite verificar y asignar un valor de tipo de ubicación si es correcto y válido
    protected function obtenerTipoValido(string $valor): ?array
    {
        $valor = $this->normalizeName($valor);
        if ($valor === '') return null;

        // numérico directo
        if (is_numeric($valor)) {
            $row = \App\Models\TiposModel::find((int)$valor, ['id_tipo', 'nombre']);
            return $row ? $row->toArray() : null;
        }

        $id = $this->dict_tipos[$valor] ?? null;
        return $id ? ['id_tipo' => $id, 'nombre' => null] : null;
    }

    protected function obtenerCategoriaValida(string $valor, ?int $tipoId = null): ?array
    {
        $valor = $this->normalizeName($valor);
        if ($valor === '') return null;

        if (is_numeric($valor)) {
            $row = \App\Models\CategoriaModel::find((int)$valor, ['id_categoria', 'nombre', 'id_tipo']);
            return $row ? $row->toArray() : null;
        }
        if (!$tipoId) return null;

        $id = $this->categoriasDictForTipo($tipoId)[$valor] ?? null;
        return $id ? ['id_categoria' => $id, 'nombre' => null, 'id_tipo' => $tipoId] : null;
    }

    protected function obtenerSubcategoriaValida(string $valor, ?int $tipoId = null, ?int $categoriaId = null): ?array
    {
        $valor = $this->normalizeName($valor);
        if ($valor === '') return null;

        if (is_numeric($valor)) {
            $row = \App\Models\SubcategoriaModel::find((int)$valor, ['id_subcategoria', 'nombre', 'id_categoria', 'id_tipo']);
            return $row ? $row->toArray() : null;
        }
        if (!$tipoId || !$categoriaId) return null;

        $id = $this->subcategoriasDictFor($tipoId, $categoriaId)[$valor] ?? null;
        return $id ? ['id_subcategoria' => $id, 'nombre' => null, 'id_categoria' => $categoriaId, 'id_tipo' => $tipoId] : null;
    }

    // Permite verificar y asignar un valor de tipo de ubicación si es correcto y válido
    protected function obtenerModeloValido(
        string $valor,
        ?int $marcaId,
        ?int $tipoId,
        ?int $categoriaId,
        ?int $subcategoriaId
    ): ?array {
        if ($marcaId === null) {
            return null; // No se puede validar modelo sin marca
        }

        $valor = $this->normalizeName($valor);

        if ($valor === '') {
            return null; // no consultar si vacio
        }

        $cacheKey = "$marcaId|$tipoId|$categoriaId|$subcategoriaId|$valor";
        if (isset($this->modelos[$cacheKey])) {
            return $this->modelos[$cacheKey];
        }

        $query = ModelosModel::query();

        if (is_numeric($valor)) {
            $query->where('id_modelo', $valor);
        } else {
            $query->whereRaw("nombre COLLATE Latin1_General_CI_AI = ?", [$valor]);
        }

        // Filtros obligatorios porque el modelo pertenece a una marca, tipo, etc.
        $query->where('id_marca', $marcaId)
            ->where('id_tipo', $tipoId)
            ->where('id_categoria', $categoriaId)
            ->where('id_subcategoria', $subcategoriaId);

        $resultado = $query->first([
            'id_modelo',
            'nombre',
            'id_marca',
            'id_categoria',
            'id_tipo',
            'id_subcategoria'
        ])?->toArray();

        $this->modelos[$cacheKey] = $resultado ?: null;

        return $resultado ?: null;
    }

    protected function obtenerMarcaValida(string $valor): ?array
    {
        $valor = $this->normalizeName($valor);

        if ($valor === '') {
            return null; // no consultar si vacio
        }

        if (isset($this->marcas[$valor])) {
            return $this->marcas[$valor];
        }

        $query = MarcasModel::query();

        if (is_numeric($valor)) {
            $query->where('id_marca', $valor);
        } else {
            $query->whereRaw("nombre COLLATE Latin1_General_CI_AI = ?", [$valor]);
        }

        $resultado = $query->first(['id_marca', 'nombre'])?->toArray();

        $this->marcas[$valor] = $resultado;

        return $resultado;
    }

    public function __construct($tipoDatos = 'Activos')
    {
        $this->tipoDatos = $tipoDatos;
    }

    public $tipo, $categoria, $subcategoria;
    public function confirmImportActivo(
        array $datos,
        $propiedad,
        array $localSubcategoria = [],
        $globalSubcategoria = '',
        array $localCategoria = [],
        $globalCategoria = '',
        array $localTipo = [],
        $globalTipo = '',
        array $localSitAlta = [],
        $globalSitAlta = '',
        array $localSitGeneral = [],
        $globalSitGeneral = '',
        array $localCondicion = [],
        $globalCondicion = '',
        array $localMarca = [],
        $globalMarca = '',
        array $localModelo = [],
        $globalModelo = '',
        array $localAtributos = [],
        array $localUbicacion = [],
        $globalUbicacion = '',
        array $localGestores = [],
        $globalGestores = '',
        array $localResponsable = [],
        $globalResponsable = '',
        array $localEmpresa = [],
        string $globalEmpresa = '',
        array $localPropietario = [],
        string $globalPropietario = '',
        $tipoOperacion,
        callable $errorCallback
    ): mixed {
        $errores = [];
        $propiedad = $this->tipoDatos === 'Activos' ? 'Propio' : 'Cliente';

        foreach ($datos as $index => &$dato) {

            $tipo = $localTipo[$index] ?? $globalTipo ?? $dato['tipo'];
            $categoria = $localCategoria[$index] ?? $globalCategoria ?? $dato['categoria'];
            $subcategoria = $localSubcategoria[$index] ?? $globalSubcategoria ?? $dato['subcategoria'];
            $estado_general = $localSitGeneral[$index] ?? $globalSitGeneral ?? $dato['estado_general'];
            $estado_alta = $localSitAlta[$index] ?? $globalSitAlta ?? $dato['estado_alta'];
            $condicion = $localCondicion[$index] ?? $globalCondicion ?? $dato['condicion'] ?? null;
            $modelo = $localModelo[$index] ?? $globalModelo ?? $dato['modelo'] ?? null;
            $ubicación = $localUbicacion[$index] ?? $globalUbicacion ?? $dato['ubicación'] ?? null;
            $responsable_inventario = $localGestores[$index] ?? $globalGestores ?? $dato['responsable_inventario'] ?? null;
            $usuario_titular = $localResponsable[$index] ?? $globalResponsable ?? $dato['usuario_titular'] ?? null;
            $atributos = $localAtributos[$index] ?? [];

            $toScalar = function ($v) {
                if (is_array($v)) {
                    // tomar el primer valor si existe, o null
                    return array_key_exists(0, $v) ? $v[0] : (count($v) ? reset($v) : null);
                }
                if ($v === '' || $v === false) return null;
                return is_scalar($v) ? $v : (string) $v; // por si viniera un Stringable
            };

            $dato['tipo'] = $tipo;
            $dato['categoria'] = $categoria;
            $dato['subcategoria'] = $subcategoria;
            $dato['estado_general'] = $estado_general;
            $dato['estado_alta'] = $estado_alta;
            $dato['condicion'] = $condicion ?? null;
            $dato['modelo'] = $modelo ?? null;
            $dato['ubicación'] = $ubicación ?? null;
            $dato['responsable_inventario'] = $responsable_inventario;
            $dato['usuario_titular'] = $usuario_titular;
            $dato['atributos'] = $atributos;

            $dato['id_externo']      = $toScalar($dato['id_externo'] ?? null);
            $dato['ubicación']       = $toScalar($dato['ubicación'] ?? null);
            $dato['condicion']       = $toScalar($dato['condicion'] ?? null);
            $dato['identificador']   = $toScalar($dato['identificador'] ?? null);
            $dato['depende_de']      = $toScalar($dato['depende_de'] ?? null);
            $dato['modelo']          = $toScalar($dato['modelo'] ?? null);

            $valido = $this->validateAndAssign($dato, 'categoria', $localCategoria, $globalCategoria, $index, 'Categoría', $errorCallback);

            if (!$valido) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Categoría' es inválido o está vacío.";
                continue;
            }

            $valido = $this->validateAndAssign($dato, 'subcategoria', $localSubcategoria, $globalSubcategoria, $index, 'Subcategoría', $errorCallback);

            if (!$valido) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Subcategoría' es inválido o está vacío.";
                continue;
            }

            $valido = $this->validateAndAssign($dato, 'tipo', $localTipo, $globalTipo, $index, 'Tipo', $errorCallback);

            if (!$valido) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Tipo' es inválido o está vacío.";
                continue;
            }

            $valido = $this->validateAndAssign($dato, 'estado_general', $localSitGeneral, $globalSitGeneral, $index, 'Estado General', $errorCallback);

            if (!$valido) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Estado General' es inválido o está vacío.";
                continue;
            }

            $valido = $this->validateAndAssign($dato, 'estado_alta', $localSitAlta, $globalSitAlta, $index, 'Estado Alta', $errorCallback);

            if (!$valido) {
                $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'Estado Alta' es inválido o está vacío.";
                continue;
            }

            $cuit_empresa = null;
            if ($this->tipoDatos === 'Activos') {
                $propiedad = 'Propio';
                $cuit_empresa = IdHelper::idEmpresa(); 
            } else {
                $valido = $this->validateAndAssign($dato, 'cuit_propietario', $localPropietario, $globalPropietario, $index, 'CUIT Propietario', $errorCallback);

                if (!$valido) {
                    $errores[$index] = "Error en la fila " . ($index + 1) . ": El campo 'CUIT Propietario' es inválido o está vacío.";
                    continue;
                }

                $cuitEmpresa = !empty($localEmpresa[$index]) ? $localEmpresa[$index]
                    : (!empty($globalEmpresa) ? $globalEmpresa
                        : (!empty($dato['cuit_propietario']) ? $dato['cuit_propietario'] : null));

                $cuit_empresa = $cuitEmpresa; // Empresa dueña del bien
                $propiedad = 'Cliente'; // Empresa propietaria del bien
            }

            $dato['cuit_propietario'] = $cuit_empresa;
            $dato['propiedad'] = $propiedad;
        }
        if (!empty($errores)) {
            foreach ($errores as $idx => $msg) {
                $errorCallback($msg, $idx);
            }
            return false;
        }

        $this->fecha_asignacion = now()->format('Y-m-d H:i:s'); // 🔧 CAMBIA AQUÍ si querés forzar otra fecha/hora

        try {
            DB::transaction(function () use ($datos) {

                $bulkAsignaciones = [];
                $toUpsert = [];
                $combos = [];

                foreach ($datos as $d) {
                    if (!empty($d['tipo']) && !empty($d['categoria']) && !empty($d['subcategoria'])) {
                        $combos[$d['tipo'] . '|' . $d['categoria'] . '|' . $d['subcategoria']] = [$d['tipo'], $d['categoria'], $d['subcategoria']];
                    }
                }
                foreach ($combos as [$t, $c, $s]) {
                    $this->getAttrBundle($t, $c, $s); // esto llena cache y evita la 1ª latencia dentro del loop
                }
                
                foreach ($datos as $i => &$dato) {
                    $estado_inventario = ((int)$dato['estado_general'] === $this->idEstadoBaja())
                        ? 'Baja' : 'Activo'; // 🔧 CAMBIA AQUÍ si querés mapear distinto

                    if ($this->tipoDatos === 'Bienes') {
                        // Para bienes de terceros: solo se permite ubicación si existe en la empresa logueada.
                        $existeUbicacion = UbicacionesModel::where('cuit_empresa', IdHelper::idEmpresa())
                            ->where('id_ubicacion', $dato['ubicación'])
                            ->exists();
                        $ubicacion = $existeUbicacion ? $dato['ubicación'] : null;
                    } else {
                        $ubicacion = $dato['ubicación'] ?? null; // 🔧 CAMBIA AQUÍ si querés validar también en Activos propios
                    }

                    foreach (['ubicación', 'condicion', 'modelo', 'identificador', 'depende_de'] as $campo) {
                        if (is_array($dato[$campo] ?? null)) {
                            $dato[$campo] = $dato[$campo][0] ?? null;
                        }
                    }

                    // normalizar '' -> null en campos nullable usados en create()
                    $nullable = ['id_externo', 'numero_serie', 'id_Nivel_Organizacion', 'id_modelo'];
                    foreach ($nullable as $f) {
                        if (($dato[$f] ?? '') === '') $dato[$f] = null;
                    }

                    // ✅ create(): devuelve un modelo con id_activo
                    $activo = \App\Models\ActivosModel::create([
                        'nombre'                => $dato['nombre'],
                        'id_externo'            => $dato['id_externo'] ?? null,
                        'id_estado_sit_alta'    => $dato['estado_alta'],
                        'estado_inventario'     => $estado_inventario,
                        'id_ubicacion'          => $ubicacion,
                        'id_subcategoria'       => $dato['subcategoria'],
                        'id_categoria'          => $dato['categoria'],
                        'id_tipo'               => $dato['tipo'],
                        'id_estado_sit_general' => $dato['estado_general'],
                        'empresa_titular'       => $dato['cuit_propietario'], // CUIT de la empresa titular
                        'propietario'           => $dato['propiedad'],        // 'Propio' o 'Cliente'
                        'id_condicion'          => $dato['condicion'] ?? null,
                        'numero_serie'          => $dato['identificador'] ?? null,
                        'id_Nivel_Organizacion' => $dato['depende_de'] ?? null,
                        'id_modelo'             => $dato['modelo'] ?? null,
                        'garantia_vigente'      => 'No', // 🔧 CAMBIA AQUÍ si querés default 'Si' o condicionar por fecha
                    ]);

                    if (!$activo || !$activo->id_activo) {
                        throw new \RuntimeException('No se pudo crear el activo para la fila ' . $i);
                    }

                    // ---- atributos combinados (Excel + UI) ----
                    $atributosArray = [];
                    if (!empty($dato['atributos']) && is_array($dato['atributos'])) {
                        foreach ($dato['atributos'] as $idAtributo => $valorAtributo) {
                            $atributosArray[(int)$idAtributo] = $valorAtributo;
                        }
                    }
                    // usar $i, no $index
                    if (isset($this->atributos[$i]) && is_array($this->atributos[$i])) {
                        foreach ($this->atributos[$i] as $idAtributo => $valorSeleccionado) {
                            if ($valorSeleccionado !== null && $valorSeleccionado !== '' && $valorSeleccionado !== []) {
                                $atributosArray[(int)$idAtributo] = $valorSeleccionado;
                            }
                        }
                    }

                    // ---- definiciones de la subcategoría (NO borrar luego) ----
                    [$attrDef, $valNameToId, $valIdSet] = (function () use ($dato) {
                        $b = $this->getAttrBundle($dato['tipo'], $dato['categoria'], $dato['subcategoria']);
                        return [$b['attrDef'], $b['valNameToId'], $b['valIdSet']];
                    })();

                    $selectedAtributos = [];
                    $valoresCheckbox = [];
                    $valoresSelect   = [];
                    $camposTexto     = [];
                    $camposNumericos = [];
                    $camposFechas    = [];

                    $enumMemo = [];
                    $resolveEnumId = function (int $idAttr, $value) use (&$enumMemo, $valNameToId, $valIdSet) {
                        if ($value === '' || $value === null) return null;
                        $key = $idAttr . '|' . mb_strtolower(trim((string)$value));
                        if (array_key_exists($key, $enumMemo)) return $enumMemo[$key];

                        if (is_numeric($value) && in_array((int)$value, $valIdSet[$idAttr] ?? [], true)) {
                            return $enumMemo[$key] = (int)$value;
                        }
                        $k = mb_strtolower(trim((string)$value));
                        $kAlt = str_replace(',', '.', $k);
                        return $enumMemo[$key] = ($valNameToId[$idAttr][$k] ?? $valNameToId[$idAttr][$kAlt] ?? null);
                    };

                    foreach ($atributosArray as $idAtributo => $valor) {
                        if (!isset($attrDef[$idAtributo])) continue;

                        $selectedAtributos[$idAtributo] = true;

                        $def  = $attrDef[$idAtributo];
                        $attr = $def->atributo;
                        $hasLista = ($def->valores && $def->valores->count() > 0);
                        $isEnum   = (strtolower((string)($attr->predefinido ?? 'No')) === 'si') || $hasLista;
                        $isMul    = strtolower((string)($attr->SelectM ?? 'No')) === 'si';

                        if ($isEnum) {
                            if ($isMul) {
                                $seleccion = is_array($valor)
                                    ? $valor
                                    : (($valor === '' || $valor === null) ? [] : preg_split('/\s*[|,]\s*/', (string)$valor, -1, PREG_SPLIT_NO_EMPTY));
                                $ids = [];
                                foreach ($seleccion as $v) {
                                    $idV = $resolveEnumId($idAtributo, $v);
                                    if ($idV !== null) $ids[] = $idV;
                                }
                                $ids = array_values(array_unique($ids));
                                $valoresCheckbox[$idAtributo] = !empty($ids) ? array_fill_keys($ids, true) : [];
                            } else {
                                $v   = is_array($valor) ? reset($valor) : $valor;
                                $idV = $resolveEnumId($idAtributo, $v);
                                $valoresSelect[$idAtributo] = $idV;
                            }
                        } else {
                            $v = is_array($valor) ? (reset($valor) ?: null) : $valor;
                            $tipoCampo = (string)($attr->tipo_campo ?? '1'); // 1=Texto, 2=Numérico, 3=Fecha
                            switch ($tipoCampo) {
                                case '2':
                                    if (is_string($v)) $v = str_replace(',', '.', $v);
                                    $camposNumericos[$idAtributo] = ($v === '' ? null : (is_numeric($v) ? 0 + $v : null));
                                    break;
                                case '3':
                                    $camposFechas[$idAtributo] = $v ? \Carbon\Carbon::parse($v)->format('Y-m-d') : null;
                                    break;
                                default:
                                    $camposTexto[$idAtributo] = ($v === '' ? null : (string)$v);
                                    break;
                            }
                        }
                    }

                    $servicio = new \App\Services\GeneradorDeAtributosDeActivo(
                        $selectedAtributos,
                        $valoresCheckbox,
                        $valoresSelect,
                        $camposTexto,
                        $camposNumericos,
                        $camposFechas,
                        $dato['subcategoria'],
                        $dato['categoria'],
                        $dato['tipo'],
                    );

                    // genera filas con el id del activo recién creado
                    $filas = $servicio->handle($activo->id_activo);

                    foreach ($filas as $data) {
                        $toUpsert[] = [
                            'id_atributo'            => $data['id_atributo'],
                            'id_activo'              => $data['id_activo'],
                            'id_subcategoria_activo' => $data['id_subcategoria_activo'],
                            'id_categoria_activo'    => $data['id_categoria_activo'],
                            'id_tipo_activo'         => $data['id_tipo_activo'],
                            'campo'                  => $data['campo'] ?? null,
                            'campo_numerico'         => $data['campo_numerico'] ?? null,
                            'fecha'                  => $data['fecha'] ?? null,
                            'campo_enum'             => $data['campo_enum'] ?? null,
                            'campo_enum_id'          => $data['campo_enum_id'] ?? null,
                            'campo_enum_list'        => $data['campo_enum_list'] ?? null,
                        ];
                    }

                    // 🔧 CAMBIA AQUÍ: lógica de quién figura como gestor y responsable por defecto
                    $gestor      = $dato['usuario_titular']        ?? auth()->user()->id;
                    $responsable = $dato['responsable_inventario'] ?? auth()->user()->id;

                    // 🔧 CAMBIA AQUÍ: origen de empresa_empleados en la asignación
                    // Actualmente se registra la empresa logueada (proveedora/operadora), no la titular.
                    $empresaEmpleados = IdHelper::idEmpresa();

                    $bulkAsignaciones[] = [
                        'id_activo'         => $activo->id_activo,
                        'id_tipo'           => $dato['tipo'],
                        'id_categoria'      => $dato['categoria'],
                        'id_subcategoria'   => $dato['subcategoria'],
                        'asignado_a'        => null,
                        'gestionado_por'    => $gestor,
                        'fecha_asignacion'  => $this->fecha_asignacion,
                        'responsable'       => $responsable,
                        'empresa_empleados' => $empresaEmpleados,
                        'estado_asignacion' => 'Aceptado',
                    ];

                    if ($this->tipoDatos === 'Bienes') {
                        // Relación de activo compartido entre titular y proveedora/operadora
                        \App\Models\ActivosCompartidosModel::updateOrCreate([
                            'id_activo'          => $activo->id_activo,
                            'id_subcat'          => $dato['subcategoria'] ?? null,
                            'id_cat'             => $dato['categoria'] ?? null,
                            'id_tipo'            => $dato['tipo'] ?? null,
                            'empresa_titular'    => $dato['cuit_propietario'],
                            'empresa_proveedora' => IdHelper::idEmpresa(), // 🔧 CAMBIA AQUÍ si la proveedora no es la logueada
                            'estado_asignacion'  => 'Aceptado'
                        ]);
                    }
                }

                // Upsert de atributos en chunks
                foreach (array_chunk($toUpsert, 800) as $chunk) {
                    \App\Models\ActivosAtributosModel::upsert(
                        $chunk,
                        ['id_atributo', 'id_activo'],
                        [
                            'campo',
                            'campo_numerico',
                            'fecha',
                            'campo_enum',
                            'campo_enum_id',
                            'campo_enum_list',
                            'id_subcategoria_activo',
                            'id_categoria_activo',
                            'id_tipo_activo'
                        ]
                    );
                }

                if ($bulkAsignaciones) {
                    \App\Models\ActivosAsignacionModel::insert($bulkAsignaciones);
                }
            });

            return true;
        } catch (\Exception $e) {
            $errorCallback("Error al importar los datos: " . $e->getMessage(), null);
            return false;
        }
    }
}
