<?php

namespace App\Livewire;

// Laravel base
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

// Livewire
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

// Helpers y Models
use App\Helpers\IdHelper;
use App\Models\User;
use App\Models\EmpresasModel;
use App\Models\CategoriaModel;
use App\Models\CondicionModel;
use App\Models\SubcategoriaModel;
use App\Models\TiposModel;
use App\Models\TiposUbicacionesModel;
use App\Models\UbicacionesModel;
use App\Models\EstadoGeneralModel;
use App\Models\EstadosAltasModel;
use App\Models\MarcasModel;
use App\Models\ModelosModel;
use App\Models\UsuariosEmpresasModel;

// Traits
use App\Traits\SortableTrait;
use App\Traits\Importaciones\UbicacionesPropiasTrait;
use App\Traits\Importaciones\ProveedoresTrait;
use App\Traits\Importaciones\ClientesTrait;
use App\Traits\Importaciones\UsuariosTrait;
use App\Traits\Importaciones\BienesTrait;
use Illuminate\Support\Str;

class Importaciones extends Component
{
    use SortableTrait;
    use WithFileUploads;
    use UbicacionesPropiasTrait;
    use ProveedoresTrait;
    use ClientesTrait;
    use UsuariosTrait;
    use BienesTrait;

    use WithPagination {
        setPage as protected livewireSetPage;
    }
    // === Previsualización de datos ===
    public $previewData = [];
    public $vistaPreviaUsuario;
    public $vistaPreviaActivo;
    public $previewDataActivo;
    public $previewDataUbicacion;
    public $previewDataProveedores;
    public $previewDataClientes;

    // === Datos de selección global/local ===
    public $globalTipoUbicacion = '';
    public $localTipoUbicacion = [];
    public $globalGestor, $globalGestores, $globalAsignado, $globalResponsable;
    public $localGestor = [], $localGestores = [], $localAsignado = [], $localResponsable = [];
    public $globalSubcategoria = [], $localSubcategoria = [];
    public $globalCategoria = [], $localCategoria = [];
    public $globalTipo = [], $localTipo = [];
    public $globalSitAlta = [], $localSitAlta = [];
    public $globalSitGeneral = [], $localSitGeneral = [];
    public $globalUbicacion = [], $localUbicacion = [];
    public $globalCondicion = [], $localCondicion = [];
    public $globalMarca = [], $localMarca = [];
    public $globalModelo = [], $localModelo = [];
    public $globalGestorActivo = [], $localGestorActivo = [];
    public $globalPropietario = '', $localPropietario;
    public $tipoUbicacionGlobal = '';
    public $propietarioGlobal = '';
    public $globalEmpresa = '', $localEmpresa = [];
    public array $atributos = [];
    public array $atributosDisponibles = [];
    public array $atributosNombres = [];
    public $localAtributos = [];
    public ?int $globalSupervisorUsuario = null;
    public array $localSupervisorUsuario = [];
    public $supervisores;

    // === Empresa y usuarios ===
    public $empresas, $empresasClientes;
    public $selectedEmpresaNombre, $cuit, $searchEmpresa, $id;

    // === Importación de archivos ===
    public $archivo;
    public $tipoDatos = 1;
    public $tipoOperacion = 1;

    // === Datos para activos ===
    public $selectedTipoNombre, $searchTipo, $id_tipo, $tipoPrueba;
    public $selectedCategoriaNombre, $searchCategoria, $id_categoria;
    public $selectedSubcategoriaNombre, $id_subcategoria, $searchSubcategoria;
    public $selectedMarcaNombre, $id_marca, $searchMarca, $marca = [];
    public $selectedUbicacionNombre, $id_ubicacion, $searchUbicacion;
    public $categorias, $subcategorias, $ubicaciones = [], $tiposActivos, $tiposs, $tipos;
    public $estadosSitAlta, $estadosSitGeneral = [];
    public $subcategoria, $categoria, $categorias2, $ubicacionesList, $ubicacionesGeneral;
    public $tiposUbicaciones, $gestores, $condiciones, $marcas, $modelos;

    // === Geolocalización (ubicaciones) ===
    public $selectedCuil, $cuilGestor, $searchCuil;
    public $pais, $provincia, $ciudad, $calle, $altura;
    public $codigo_postal, $lat, $long;

    // === Panel UI y búsqueda ===
    public $panel_actual;
    public $searchUbicaciones = '';
    public $searchActivos = '';
    public $searchProveedores = '';
    public $searchClientes = '';
    public $searchUsuarios = '';
    public $searchGestor;

    // === Paginación ===
    public $pageUsuarios = 1;
    public $pageClientes = 1;
    public $pageUbicaciones = 1;
    public $pageActivos = 1;
    public $pageProveedores = 1;
    protected $paginationTheme = 'tailwind';
    protected array $tiposCache = [];
    protected array $estadosCache = [];

    public int $perPageActivos = 10;
    public $filaEditandoAtributos = null;
    public $origen;

    protected $queryString = [
        'pageUbicaciones' => ['except' => 1],
        'searchUbicaciones' => ['except' => ''],
        'pageActivos' => ['except' => 1],
        'searchActivos' => ['except' => ''],
        'pageProveedores' => ['except' => 1],
        'searchProveedores' => ['except' => ''],
        'pageUsuarios' => ['except' => 1],
        'searchUsuarios' => ['except' => ''],
    ];

    // === Tipo Usuario y Representante Técnico ===
    public $Global_tipo_usario = '';
    public $local_tipo_usario = [];
    public $representante_tecnico_global = '';
    public $representante_tecnico_local = [];
    public $local_multiples = [];
    public $global_multiples = '';
    public $localPiso = [];
    public $localSubsuelo = [];

    // === Importación: manejo de columnas faltantes ===
    public $missingCuit = false;
    public $columnsForCuitSelection = [];
    public $selectedCuitColumn = '';
    private array $cacheMarcasPorCombo = []; // key: "tipo|cat|sub" => array de marcas
    private array $cacheModelosPorCombo = []; // key: "tipo|cat|sub|marca" => array de modelos
    private array $cacheAtributosMeta = []; // [id_atributo => ['nombre'=>..., 'tipo'=>..., 'predefinido'=>..., 'multiple'=>..., 'valores'=>[], 'ids'=>[]]]

    // public array $filasEditadasManualmente = []; // índice => true

    // === Listeners ===
    protected $listeners = [
        'updateAllLocalTipoUsuario',
        'updateAllLocalRepTecnico',
        'updatedGlobalEmpresa',
        'updateAllLocalMultiples',
        'updatedLocalMultiples',
        'updatedLocalTipo',
        'dependenciaSeleccionada' => 'setDependenciaSeleccion',
    ];

    const SIN_UBICACION = -1;

    // === Métodos del ciclo de vida ===

    public array $atributoVisible = [];

    public function mount()
    {
        $this->Global_tipo_usario = '';
        $this->representante_tecnico_global = '';
        $this->globalEmpresa = '';
        $this->tipos = $this->Tipos();
        $this->tiposs = $this->catalogoTiposActivos();   // << antes: TiposModel::all()
    }

    private function cargarDatosIniciales()
    {
        match ($this->tipoDatos) {
            'Usuarios'     => [$this->Empresas(), $this->Tipos(), $this->Supervisores(), $this->dispatch('openModal', ['empresas.empresas-usuarios.editar-dependencia-modal'])],
            'UbicacionesUsuarios'  => [$this->Empresas(), $this->EmpresasClientes()],
            'UbicacionesPropias'  => $this->Ubicaciones(),
            'Activos'      => [$this->Activos(), $this->dispatch('openModal', ['empresas.empresas-usuarios.editar-dependencia-modal'])],
            'Bienes'      => [$this->Activos(), $this->EmpresasClientes(), $this->dispatch('openModal', ['empresas.empresas-usuarios.editar-dependencia-modal'])],
            default        => null,
        };
    }

    protected function descargarEjemplo(string $nombreArchivo, string $nombreDescarga)
    {
        $filePath = public_path("storage/EjemploDescarga/{$nombreArchivo}");
        if (!file_exists($filePath)) {
            $this->dispatch('error', ['message' => 'El archivo de ejemplo no existe.']);
            return;
        }

        $url = asset("storage/EjemploDescarga/{$nombreArchivo}");
        $this->dispatch('download-template', [
            'url' => $url,
            'filename' => $nombreDescarga
        ]);
    }

    private function mostrarErrorImportacion(string $mensaje = 'Error durante la importación.'): void
    {
        $this->dispatch('errorInfo', [
            'title' => 'Error de Importación',
            'message' => $mensaje,
        ]);
    }

    private function mostrarExitoImportacion(string $mensaje = 'Los registros fueron importados exitosamente.'): void
    {
        $this->dispatch('Exito', [
            'title' => 'Importación Exitosa',
            'message' => $mensaje,
        ]);
    }

    public function setPage($page, $pageName = 'page')
    {
        if ($pageName === 'pageUbicaciones') {
            $this->pageUbicaciones = $page;
        } elseif ($pageName === 'pageActivos') {
            $this->pageActivos = $page;
        } elseif ($pageName === 'pageProveedores') {
            $this->pageProveedores = $page;
        } elseif ($pageName === 'pageUsuarios') {
            $this->pageUsuarios = $page;
        }
    }

    // === Carga de datos ===

    private function Empresas()
    {
        return $this->empresas = Cache::remember('act.empresas', 3600, function () {
            return EmpresasModel::select('cuit', 'razon_social', 'tipo')->get();
        });
    }

    private function Supervisores()
    {
        $empresa = IdHelper::idEmpresa();

        $ids = UsuariosEmpresasModel::where('cuit', $empresa)
            ->where('supervisor', 1)
            ->pluck('id_usuario');

        // solo id, name, cuil (útil para búsqueda)
        return $this->supervisores = User::whereIn('id', $ids)
            ->orderBy('name')
            ->get(['id', 'name', 'cuil']);
    }

    private function Tipos()
    {
        // <- línea que te marca el log (#184)
        return $this->tipos = Cache::remember('act.tipos_ubicaciones', 3600, function () {
            return TiposUbicacionesModel::select('id_tipo', 'nombre')->get();
        });
    }

    private function catalogoTiposActivos()
    {
        // 1 hora de caché (ajusta a gusto)
        return Cache::remember('act.tipos_activos_all', 3600, function () {
            return TiposModel::select('id_tipo', 'nombre')
                ->orderBy('nombre')
                ->get();
        });
    }

    private function EmpresasClientes()
    {
        return $this->empresasClientes = DB::table('act.clientes_empresa as ce')
            ->join('act.empresas_o_particulares as ep', 'ep.cuit', '=', 'ce.cliente_cuit')
            ->where('ce.empresa_cuit', IdHelper::idEmpresa())
            ->select('ep.*')
            ->get();
    }

    private function Ubicaciones()
    {
        $this->ubicaciones = $this->ubicacionesPropias();
        $this->gestores = $this->UsuariosEmpresas();
    }

    private function Activos()
    {
        $this->categorias = CategoriaModel::all();
        $this->subcategorias = SubcategoriaModel::all();
        $this->id = SubcategoriaModel::pluck('id_tipo');
        $this->tiposActivos = TiposModel::whereIn('id_tipo', $this->id)->get();
        $this->estadosSitAlta = EstadosAltasModel::all();
        $this->estadosSitGeneral = EstadoGeneralModel::all();
        $this->ubicaciones = $this->ubicacionesPropias();
        $this->condiciones = CondicionModel::all();
        $this->marcas = MarcasModel::all();
        $this->modelos = ModelosModel::all();
        $this->gestores = $this->UsuariosEmpresas();
    }

    protected function extraerClavesYValores(string $atributosStr): array
    {
        $resultado = [];

        // Normalizamos espacios y saltos de línea en una sola línea
        $atributosStr = trim(preg_replace('/\s*\n\s*/', ', ', $atributosStr));

        // Separamos por coma
        $pares = explode(',', $atributosStr);

        foreach ($pares as $par) {
            $par = trim($par);
            if (empty($par)) {
                continue;
            }

            // Separar por dos puntos, máximo 2 partes
            $partes = explode(':', $par, 2);

            if (count($partes) == 2) {
                $clave = trim($partes[0]);
                $valor = trim($partes[1]);
                $resultado[$clave] = $valor;
            }
        }

        return $resultado;
    }
    public $atributosCache = [], $openModalAtributo = false;

    private function cargarMetaAtributos(array $ids): void
    {
        $faltantes = array_values(array_diff($ids, array_keys($this->cacheAtributosMeta)));
        if (empty($faltantes)) return;

        $tiposMap = $this->tiposCampoMap(); // evita pegar a tipos_campo

        $atributos = \App\Models\AtributosModel::query()
            ->whereIn('id_atributo', $faltantes)
            ->get(['id_atributo', 'nombre', 'tipo_campo', 'predefinido', 'SelectM']);

        foreach ($atributos as $attr) {
            $tipoRaw = $tiposMap[$attr->id_tipo_campo] ?? 'texto';
            if (in_array($tipoRaw, ['numerico', 'número', 'numero'])) {
                $tipoCampo = 'Numerico';
            } elseif (in_array($tipoRaw, ['fecha', 'date', 'datetime'])) {
                $tipoCampo = 'Fecha';
            } else {
                $tipoCampo = 'Texto';
            }

            // valores predefinidos desde cache (si los hay)
            $vals = $this->valoresDeAtributo((int)$attr->id_atributo);
            $valores = array_column($vals, 'valor');
            $idsVals = array_column($vals, 'id_valor');

            $this->cacheAtributosMeta[$attr->id_atributo] = [
                'nombre'      => $attr->nombre,
                'tipo'        => $tipoCampo,
                'predefinido' => strtolower((string)$attr->predefinido) === 'si',
                'multiple'    => strtolower((string)$attr->SelectM) === 'si',
                'valores'     => $valores,
                'ids'         => $idsVals,
            ];
        }
    }

    public function abrirModalAtributos(int $index)
    {
        $this->filaEditandoAtributos = $index;
        $this->cargarAtributosSiCorresponde($index);
        $this->openModalAtributo = true;
    }

    public function cerrarModalAtributos(): void
    {
        $this->openModalAtributo = false;
    }

    public function cargarAtributosSiCorresponde($index)
    {
        $tipo        = $this->localTipo[$index]        ?? $this->globalTipo        ?? null;
        $categoria   = $this->localCategoria[$index]   ?? $this->globalCategoria   ?? null;
        $subcategoria = $this->localSubcategoria[$index] ?? $this->globalSubcategoria ?? null;

        if (!$tipo || !$categoria || !$subcategoria) return;

        $key = "$tipo|$categoria|$subcategoria";

        if (!isset($this->atributosCache[$key])) {
            // Obtenés las filas AtributosSubcategoria en una sola consulta
            $rows = \App\Models\AtributosSubcategoriaModel::where([
                ['id_tipo', $tipo],
                ['id_categoria', $categoria],
                ['id_subcategoria', $subcategoria],
            ])->get(['id_atributo']);

            $ids = $rows->pluck('id_atributo')->unique()->values()->all();

            // 🚀 Cargar meta de esos atributos (solo los que falten)
            $this->cargarMetaAtributos($ids);

            // Armar la definición usando el cache global por id_atributo
            $def = [];
            foreach ($ids as $idAtrib) {
                $meta = $this->cacheAtributosMeta[$idAtrib] ?? null;
                if ($meta) $def[$idAtrib] = $meta;
            }
            $this->atributosCache[$key] = $def;
        }

        // nombre => id_atributo para mapear lo que vino del archivo
        $nombreToId = [];
        foreach ($this->atributosCache[$key] as $idAtributo => $data) {
            $nombreToId[$data['nombre']] = $idAtributo;
        }

        // Aplicar a TODAS las filas con esa combinación
        foreach ($this->previewDataActivo as $i => $row) {
            if (
                ($this->localTipo[$i] ?? null)        === $tipo &&
                ($this->localCategoria[$i] ?? null)   === $categoria &&
                ($this->localSubcategoria[$i] ?? null) === $subcategoria
            ) {
                // 1) exponer definiciones a la fila
                $this->atributosDisponibles[$i] = $this->atributosCache[$key];

                // 2) preseleccionar valores si ya venían del archivo
                $crudos = $this->atributos[$i] ?? []; // aquí venían como [nombre => valor]

                foreach ($crudos as $nombreClave => $valor) {
                    if (!isset($nombreToId[$nombreClave])) continue;

                    $idAtributo = $nombreToId[$nombreClave];
                    $meta       = $this->atributosDisponibles[$i][$idAtributo];

                    if ($meta['predefinido'] && $meta['multiple']) {
                        // si el archivo trae "a | b" o "a, b" —> array normalizado
                        $items = preg_split('/\s*[|,]\s*/', (string)$valor, -1, PREG_SPLIT_NO_EMPTY);
                        $this->atributos[$i][$idAtributo] = array_values(array_unique(array_map('trim', $items)));
                    } else {
                        // texto, numérico, fecha o predefinido simple
                        $this->atributos[$i][$idAtributo] = is_string($valor) ? trim($valor) : $valor;
                    }
                }
            }
        }
    }

    private function ubicacionesPropias()
    {
        $cuit = IdHelper::idEmpresa();

        return Cache::remember("act.ubicaciones.$cuit", 600, function () use ($cuit) {
            return UbicacionesModel::where('cuit', $cuit)
                ->get(['id_ubicacion', 'cuit', 'nombre', 'tipo', 'cuit_empresa', 'multipisos', 'subsuelo']);
        });
    }

    // === Reacciones a cambios ===

    public function updatingSearchUbicaciones()
    {
        $this->pageUbicaciones = 1;
    }
    public function updatingSearchProveedores()
    {
        $this->pageProveedores = 1;
    }
    public function updatingSearchActivos()
    {
        $this->pageActivos = 1;
    }

    public function updatedArchivo()
    {
        // Resetear cualquier cosa relacionada a vista previa
        $this->errores = [];
        $this->vistaPreviaUsuario = [];
        $this->previewDataUbicacion = [];
        $this->previewDataActivo = [];
    }

    public function updatedTipoDatos($value)
    {
        $this->reset([
            'previewDataUbicacion',
            'previewDataActivo',
            'previewDataClientes',
            'previewDataProveedores',
            'vistaPreviaUsuario',
            'archivo',
            'tipoOperacion',
            'representante_tecnico_global',
            'Global_tipo_usario',
            'globalEmpresa',
        ]);

        $this->cargarDatosIniciales();
    }

    // === Lógica de paginación genérica con búsqueda ===

    // Cambia la firma para incluir $pageName
    private function paginarConBusqueda($data, $page, $perPage, $campoBusqueda, $termino, string $pageName)
    {
        $collection = collect($data);

        if ($termino) {
            $t = mb_strtolower(trim($termino));
            $collection = $collection->filter(function ($item) use ($campoBusqueda, $t) {
                $val = mb_strtolower((string)($item[$campoBusqueda] ?? ''));
                return $t === '' ? true : str_contains($val, $t);
            });
        }

        $total = $collection->count();
        $items = $collection
            ->slice(max(0, ($page - 1) * $perPage), $perPage) // página actual
            ->values(); // ← reindexamos 0..n SOLO para la iteración

        return new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $total,
            $perPage,
            $page,
            [
                'pageName' => $pageName,
                'path'     => request()->url(),
            ]
        );
    }

    // === Render ===
    public function render()
    {
        $perPage = 10;

        // --- Para Ubicaciones ---
        $dataUbicaciones = collect($this->previewDataUbicacion);
        if ($this->searchUbicaciones) {
            $dataUbicaciones = $dataUbicaciones->filter(function ($item) {
                return stripos($item['nombre'] ?? '', $this->searchUbicaciones) !== false;
            });
        }
        $paginatedUbicaciones = new \Illuminate\Pagination\LengthAwarePaginator(
            $dataUbicaciones->forPage($this->pageUbicaciones, $perPage),
            $dataUbicaciones->count(),
            $perPage,
            $this->pageUbicaciones,
            [
                'pageName' => 'pageUbicaciones',
                'path' => url()->current(),
            ]
        );

        // --- Para Activos ---
        $dataActivos = collect($this->previewDataActivo);
        if ($this->searchActivos) {
            $dataActivos = $dataActivos->filter(function ($item) {
                return stripos($item['nombre'] ?? '', $this->searchActivos) !== false;
            });
        }
        $paginatedActivos = new \Illuminate\Pagination\LengthAwarePaginator(
            $dataActivos->forPage($this->pageActivos, $perPage),
            $dataActivos->count(),
            $perPage,
            $this->pageActivos,
            [
                'pageName' => 'pageActivos',
                'path' => url()->current(),
            ]
        );

        // --- Para Proveedores ---
        $dataProveedores = collect($this->previewDataProveedores);
        if ($this->searchProveedores) {
            // Se filtra usando la clave que contiene el nombre (en este ejemplo usamos 'razon_social')
            $dataProveedores = $dataProveedores->filter(function ($item) {
                return stripos($item['razon_social'] ?? '', $this->searchProveedores) !== false;
            });
        }
        $paginatedProveedores = new \Illuminate\Pagination\LengthAwarePaginator(
            $dataProveedores->forPage($this->pageProveedores, $perPage),
            $dataProveedores->count(),
            $perPage,
            $this->pageProveedores,
            [
                'pageName' => 'pageProveedores',
                'path' => url()->current(),
            ]
        );

        // --- Para Clientes (NUEVO) ---
        $dataClientes = collect($this->previewDataClientes);
        if ($this->searchClientes) {
            // Se filtra por el campo que desees (ej. 'name')
            $dataClientes = $dataClientes->filter(function ($item) {
                return stripos($item['name'] ?? '', $this->searchClientes) !== false;
            });
        }

        // Creamos el paginador
        $paginatedClientes = new \Illuminate\Pagination\LengthAwarePaginator(
            $dataClientes->forPage($this->pageClientes, $perPage),
            $dataClientes->count(),
            $perPage,
            $this->pageClientes,
            [
                'pageName' => 'pageClientes',
                'path' => url()->current(),
            ]
        );

        // --- Para Usuarios (NUEVO) ---
        $dataUsuarios = collect($this->vistaPreviaUsuario);
        if ($this->searchUsuarios) {
            $dataUsuarios = $dataUsuarios->filter(function ($item) {
                return stripos($item['razon_social'] ?? '', $this->searchUsuarios) !== false;
            });
        }
        $paginatedUsuarios = new \Illuminate\Pagination\LengthAwarePaginator(
            $dataUsuarios->forPage($this->pageUsuarios, $perPage),
            $dataUsuarios->count(),
            $perPage,
            $this->pageUsuarios,
            [
                'pageName' => 'pageUsuarios',
                'path' => url()->current(),
            ]
        );

        return view('livewire.importaciones.importaciones', [
            'paginatedUbicaciones' => $paginatedUbicaciones,
            'paginatedActivos'     => $paginatedActivos,
            'paginatedProveedores' => $paginatedProveedores,
            'paginatedClientes'    => $paginatedClientes,
            'paginatedUsuarios' => $paginatedUsuarios,
        ]);
    }



    // Indice para determinar si una fila ha sido editada manualmente
    public array $filasEditadasManualmente = [
        [
            'tipo' => true,
            'categoria' => true,
            'subcategoria' => true,
            'marca' => true,
            'modelo' => true,
        ]
    ];

    // Función genérica para aplicar valor global
    private function actualizarGlobal(string $atributoLocal, array $previewDataActivo, $value): void
    {
        $mapAtributoAFilaFlag = [
            'localTipo' => 'tipo',
            'localCategoria' => 'categoria',
            'localSubcategoria' => 'subcategoria',
            'localMarca' => 'marca',
            'localModelo' => 'modelo',
        ];

        $atributoFlag = $mapAtributoAFilaFlag[$atributoLocal] ?? null;

        foreach ($previewDataActivo as $index => $row) {
            // Primero verificar si fue editado manualmente
            if (
                $atributoFlag !== null &&
                isset($this->filasEditadasManualmente[$index][$atributoFlag]) &&
                $this->filasEditadasManualmente[$index][$atributoFlag] === true
            ) {
                continue;
            }

            // Aplicar valor global
            $this->{$atributoLocal}[$index] = $value;

            // Recién acá, si no fue manual, podés resetear banderas
            $this->filasEditadasManualmente[$index] = [
                'tipo' => false,
                'categoria' => false,
                'subcategoria' => false,
                'marca' => false,
                'modelo' => false,
                'estado_general' => false,
                'sit_alta' => false,
                'ubicacion' => false,
                'condicion' => false,
            ];
        }
    }

    // Función auxiliar para asignar si el valor es numérico
    private function actualizarLocalSiEsNumerico(string $atributo, string $columna, array $row, int $index): void
    {
        $this->{$atributo}[$index] = (!empty($row[$columna]) && is_numeric($row[$columna])) ? (int) $row[$columna] : null;
    }

    // === ACTUALIZACIÓN LOCAL DE ACTIVOS (REFORMADO) ===
    public function actualizarLocalesActivos()
    {
        if (!empty($this->previewDataActivo)) {
            foreach ($this->previewDataActivo as $index => $row) {
                $this->actualizarLocalSiEsNumerico('localTipo', 'tipo', $row, $index);
                $this->actualizarLocalSiEsNumerico('localCategoria', 'categoria', $row, $index);
                $this->actualizarLocalSiEsNumerico('localSubcategoria', 'subcategoria', $row, $index);
                $this->actualizarLocalSiEsNumerico('localSitGeneral', 'estado_general', $row, $index);
                $this->actualizarLocalSiEsNumerico('localSitAlta', 'estado_alta', $row, $index);
                $this->actualizarLocalSiEsNumerico('localUbicacion', 'ubicación', $row, $index);
                $this->actualizarLocalSiEsNumerico('localCondicion', 'condicion', $row, $index);
                $this->actualizarLocalSiEsNumerico('localMarca', 'marca', $row, $index);
                $this->actualizarLocalSiEsNumerico('localModelo', 'modelo', $row, $index);
                $this->actualizarLocalSiEsNumerico('localGestores', 'responsable_inventario', $row, $index);
                $this->actualizarLocalSiEsNumerico('localResponsable', 'usuario_titular', $row, $index);
                $this->actualizarLocalSiEsNumerico('localEmpresa', 'cuit_propietario', $row, $index);

                $this->cargarMarcasPorFila($index);
                $this->prepararFilaAtributos($index);
                $this->cargarModelosPorFila($index);
            }
        }
    }

    public function prepararFilaAtributos($index)
    {
        // 1. Extraer el array de atributos y valores desde el string importado
        $atributosStr = $this->previewDataActivo[$index]['atributos'] ?? '';
        $atributosValores = $this->extraerClavesYValores($atributosStr);

        // 2. Guardar en $this->atributos[$index]
        $this->atributos[$index] = $atributosValores;

        // 3. Cargar atributos posibles desde DB
        $this->cargarAtributosSiCorresponde($index);
    }

    // === ACTUALIZACIONES GLOBALES (UNIFICADAS) ===
    public function updatedGlobalSubcategoria($value)
    {
        $this->resetCachesCombo();

        $this->globalMarca = null;
        $this->actualizarGlobal('localSubcategoria', $this->previewDataActivo, $value);
        $this->actualizarGlobal('localMarca', $this->previewDataActivo, null);


        // NUEVO: cargar marcas relacionadas con esa subcategoría
        $marcaIds = ModelosModel::where('id_tipo', $this->globalTipo)
            ->where('id_categoria', $this->globalCategoria)
            ->where('id_subcategoria', $value)
            ->pluck('id_marca')
            ->unique();

        $this->marca = MarcasModel::whereIn('id_marca', $marcaIds)->get()->toArray();

        foreach ($this->previewDataActivo as $index => $row) {
            $this->cargarMarcasPorFila($index);
            $this->cargarAtributosSiCorresponde($index);
        }
    }

    public function updatedGlobalCategoria($value)
    {
        $this->resetCachesCombo();

        // Resetear subcategoría
        $this->globalSubcategoria = null;
        $this->globalMarca = null;

        $this->actualizarGlobal('localCategoria', $this->previewDataActivo, $value);
        $this->actualizarGlobal('localSubcategoria', $this->previewDataActivo, null);
        $this->actualizarGlobal('localMarca', $this->previewDataActivo, null);

        foreach ($this->previewDataActivo as $index => $row) {
            $this->cargarMarcasPorFila($index);
        }
    }

    public function updatedGlobalTipo($value)
    {
        $this->resetCachesCombo();

        // Resetear categoría y subcategoría
        $this->globalCategoria = null;
        $this->globalSubcategoria = null;
        $this->globalMarca = null;

        // Actualizar todos los registros visibles
        $this->actualizarGlobal('localTipo', $this->previewDataActivo, $value);
        $this->actualizarGlobal('localCategoria', $this->previewDataActivo, null);
        $this->actualizarGlobal('localSubcategoria', $this->previewDataActivo, null);
        $this->actualizarGlobal('localMarca', $this->previewDataActivo, null);

        foreach ($this->previewDataActivo as $index => $row) {
            $this->cargarMarcasPorFila($index);
        }
    }

    public function updatedGlobalEstadoGeneral($value)
    {
        $this->actualizarGlobal('localSitGeneral', $this->previewDataActivo, $value);
    }

    public function updatedGlobalSitAlta($value)
    {
        $this->actualizarGlobal('localSitAlta', $this->previewDataActivo, $value);
    }

    public function updatedGlobalUbicacion($value)
    {
        $this->actualizarGlobal('localUbicacion', $this->previewDataActivo, $value);
    }

    public function updatedGlobalGestor($value)
    {
        $this->actualizarGlobal('localGestor', $this->previewDataUbicacion, $value);
    }

    public function updatedGlobalGestores($value)
    {
        $this->actualizarGlobal('localGestores', $this->previewDataActivo, $value);
    }

    public function updatedGlobalResponsable($value)
    {
        $this->actualizarGlobal('localResponsable', $this->previewDataActivo, $value);
    }

    public function updatedGlobalCondicion($value)
    {
        $this->actualizarGlobal('localCondicion', $this->previewDataActivo, $value);
    }

    public function updatedGlobalMarca($value)
    {
        $this->cacheModelosPorCombo = [];

        // Resetear modelo
        $this->globalModelo = null;

        $this->actualizarGlobal('localMarca', $this->previewDataActivo, $value);
        $this->actualizarGlobal('localModelo', $this->previewDataActivo, null);

        foreach ($this->previewDataActivo as $index => $row) {
            $this->cargarModelosPorFila($index); // 🆕 importante
        }
    }

    public function updatedGlobalModelo($value)
    {
        $this->actualizarGlobal('localModelo', $this->previewDataActivo, $value);
    }

    // // Actualiza la búsqueda de Subcategorías
    public function updatedSearchSubcategoria()
    {
        if ($this->id_categoria) {
            $query = SubcategoriaModel::where('id_categoria', $this->id_categoria)
                ->where('id_tipo', $this->id_tipo);

            if ($this->searchSubcategoria) {
                $query->where('nombre', 'like', '%' . $this->searchSubcategoria . '%');
            }

            $this->subcategoria = $query->get();
        } else {
            $this->subcategoria = collect();
        }

        // Resetear selección si la búsqueda no coincide con la subcategoría seleccionada
        if ($this->subcategoria->isEmpty() || !$this->subcategoria->contains('id_subcategoria', $this->id_subcategoria)) {
            $this->id_subcategoria = null;
            $this->selectedSubcategoriaNombre = null;
        }
    }

    // Actualiza la búsqueda de Categorías
    public function updatedSearchCategoria()
    {
        if ($this->id_tipo) {
            $query = CategoriaModel::where('id_tipo', $this->id_tipo);

            if ($this->searchCategoria) {
                $query->where('nombre', 'like', '%' . $this->searchCategoria . '%');
            }

            $this->categorias2 = $query->get();
        } else {
            $this->categorias2 = collect();
        }

        // Resetear selección si la búsqueda no coincide con la categoría seleccionada
        if ($this->categorias2->isEmpty() || !$this->categorias2->contains('id_categoria', $this->id_categoria)) {
            $this->id_categoria = null;
            $this->selectedCategoriaNombre = null;
            $this->subcategoria = collect();
            $this->id_subcategoria = null;
        }
    }

    // Actualiza la búsqueda de Tipos
    public function updatedSearchTipo()
    {
        if ($this->searchTipo) {
            $this->tipoPrueba = TiposModel::where('nombre', 'like', '%' . $this->searchTipo . '%')->get();
        } else {
            $this->tipoPrueba = TiposModel::all();
        }

        // Resetear selección si la búsqueda no coincide con el tipo seleccionado
        if ($this->tipoPrueba->isEmpty() || !$this->tipoPrueba->contains('id_tipo', $this->id_tipo)) {
            $this->id_tipo = null;
            $this->selectedTipoNombre = null;
            $this->categorias2 = collect();
            $this->id_categoria = null;
            $this->selectedCategoriaNombre = null;
            $this->subcategoria = collect();
            $this->id_subcategoria = null;
        }
    }

    public function onLocalTipoSeleccionado($index, $valor)
    {
        $this->localTipo[$index] = $valor;

        // Resetear campos dependientes
        $this->localCategoria[$index] = null;
        $this->localSubcategoria[$index] = null;
        $this->localMarca[$index] = null;
        $this->localModelo[$index] = null;

        $this->filasEditadasManualmente[$index]['tipo'] = true;
        // Marcar como editadas manualmente también las dependencias,
        // para evitar que se sobreescriban por valores globales incompatibles.
        $this->filasEditadasManualmente[$index]['categoria'] = true;
        $this->filasEditadasManualmente[$index]['subcategoria'] = true;
        $this->filasEditadasManualmente[$index]['marca'] = true;
        $this->filasEditadasManualmente[$index]['modelo'] = true;
    }

    public function onLocalCategoriaSeleccionada($index, $valor)
    {
        $this->localCategoria[$index] = $valor;
        $this->localSubcategoria[$index] = null;
        $this->localMarca[$index] = null;
        $this->localModelo[$index] = null;

        $this->filasEditadasManualmente[$index]['categoria'] = true;
        $this->filasEditadasManualmente[$index]['subcategoria'] = false;
        $this->filasEditadasManualmente[$index]['marca'] = false;
        $this->filasEditadasManualmente[$index]['modelo'] = false;
    }

    public function onLocalSubcategoriaSeleccionada($index, $valor)
    {
        $this->localSubcategoria[$index] = $valor;
        $this->localMarca[$index] = null;
        $this->localModelo[$index] = null;

        $this->filasEditadasManualmente[$index]['subcategoria'] = true;
        $this->filasEditadasManualmente[$index]['marca'] = false;
        $this->filasEditadasManualmente[$index]['modelo'] = false;

        $this->cargarMarcasPorFila($index);
        $this->cargarAtributosSiCorresponde($index);
    }

    public array $localMarcas = [], $localModelos = [];

    public function cargarMarcasPorFila($index)
    {
        $tipo        = $this->localTipo[$index]        ?? $this->globalTipo        ?? null;
        $categoria   = $this->localCategoria[$index]   ?? $this->globalCategoria   ?? null;
        $subcategoria = $this->localSubcategoria[$index] ?? $this->globalSubcategoria ?? null;

        if (!$tipo || !$categoria || !$subcategoria) {
            $this->localMarcas[$index] = [];
            return;
        }

        $key = "{$tipo}|{$categoria}|{$subcategoria}";
        if (!array_key_exists($key, $this->cacheMarcasPorCombo)) {
            $marcaIds = ModelosModel::query()
                ->where('id_tipo', $tipo)
                ->where('id_categoria', $categoria)
                ->where('id_subcategoria', $subcategoria)
                ->whereNotNull('id_marca')
                ->distinct()
                ->pluck('id_marca');

            $this->cacheMarcasPorCombo[$key] = $marcaIds->isEmpty()
                ? []
                : MarcasModel::whereIn('id_marca', $marcaIds)->orderBy('nombre')->get()->toArray();
        }

        $this->localMarcas[$index] = $this->cacheMarcasPorCombo[$key];
    }

    public function updatedLocalMarca($value, $index)
    {
        $this->localModelo[$index] = null;
        $this->cargarModelosPorFila($index);
    }

    public function cargarModelosPorFila($index)
    {
        $tipo        = $this->localTipo[$index]        ?? $this->globalTipo        ?? null;
        $categoria   = $this->localCategoria[$index]   ?? $this->globalCategoria   ?? null;
        $subcategoria = $this->localSubcategoria[$index] ?? $this->globalSubcategoria ?? null;
        $marca       = $this->localMarca[$index]       ?? $this->globalMarca       ?? null;

        if (!$tipo || !$categoria || !$subcategoria || !$marca) {
            $this->localModelos[$index] = [];
            return;
        }

        $key = "{$tipo}|{$categoria}|{$subcategoria}|{$marca}";
        if (!array_key_exists($key, $this->cacheModelosPorCombo)) {
            $modelosIds = ModelosModel::query()
                ->where('id_tipo', $tipo)
                ->where('id_categoria', $categoria)
                ->where('id_subcategoria', $subcategoria)
                ->where('id_marca', $marca)
                ->pluck('id_modelo');

            $this->cacheModelosPorCombo[$key] = $modelosIds->isEmpty()
                ? []
                : ModelosModel::whereIn('id_modelo', $modelosIds)->orderBy('nombre')->get()->toArray();
        }

        $this->localModelos[$index] = $this->cacheModelosPorCombo[$key];
    }

    public function setDependenciaSeleccion($data)
    {
        $i      = $data['rowIndex'] ?? null;
        $id     = $data['id'] ?? null;
        $nombre = $data['nombre'] ?? null;
        $origen = $data['origen'] ?? null;

        if ($i === null) return;

        if ($origen === 'bienes_masiva') {
            // Si es null, inicializo como array
            $bag = $this->previewDataActivo ?? [];

            // Escribo sin array_key_exists (crea si no existe)
            $bag[$i] = array_merge($bag[$i] ?? [], [
                'depende_de'        => $id,
                'depende_de_nombre' => $nombre,
            ]);

            $this->previewDataActivo = $bag;
            return;
        }

        if ($origen === 'usuarios_masiva') {
            $bag = $this->vistaPreviaUsuario ?? [];

            $bag[$i] = array_merge($bag[$i] ?? [], [
                'depende_de'        => $id,
                'depende_de_nombre' => $nombre,
            ]);

            $this->vistaPreviaUsuario = $bag;
            return;
        }
    }

    public function updateAllLocalMultiples($value)
    {
        if (!empty($this->previewDataUbicacion)) {
            foreach ($this->previewDataUbicacion as $index => $row) {
                $this->local_multiples[$index] = $value;

                // Si se habilita "múltiples pisos", inicializamos si no hay valores
                if ($value == '1') {
                    $this->localPiso[$index] = $this->localPiso[$index] ?? '';
                    $this->localSubsuelo[$index] = $this->localSubsuelo[$index] ?? '';
                } elseif ($value == '0') {
                    // Si se desactiva, limpiamos piso y subsuelo
                    $this->localPiso[$index] = '';
                    $this->localSubsuelo[$index] = '';
                }
            }
        }
    }

    // Actualiza todos los locales de Tipo Usuario con un valor global
    public function updateAllLocalTipoUsuario($value)
    {
        if (!empty($this->vistaPreviaUsuario)) {
            foreach ($this->vistaPreviaUsuario as $index => $row) {
                $this->local_tipo_usario[$index] = $value;
            }
        }
    }

    // Actualiza el valor global de empresa solo donde no haya local asignado
    public function updatedGlobalEmpresa($value)
    {
        if (!empty($this->vistaPreviaUsuario)) {
            foreach ($this->vistaPreviaUsuario as $index => $row) {
                if (empty($this->localEmpresa[$index])) {
                    $this->localEmpresa[$index] = $value;
                }
            }
        } elseif (!empty($this->vistaPreviaActivo)) {
            foreach ($this->vistaPreviaActivo as $index => $row) {
                if (empty($this->localEmpresa[$index])) {
                    $this->localEmpresa[$index] = $value;
                }
            }
        }
    }

    public function updatedGlobalTipoUbicacion($value)
    {
        if (empty($value)) return;
        if (empty($this->previewDataUbicacion)) return;

        // Aplicar a todas las filas visibles en la previsualización de Ubicaciones
        foreach ($this->previewDataUbicacion as $i => $row) {
            $this->localTipoUbicacion[$i] = $value;
        }
    }

    public function aplicarEmpresaGlobal($nuevoCuit, $forzar = false)
    {
        $this->globalEmpresa = $nuevoCuit;

        // Detectar si estamos trabajando con usuarios o activos
        $lista = !empty($this->vistaPreviaUsuario)
            ? $this->vistaPreviaUsuario
            : $this->vistaPreviaActivo;

        foreach ($lista as $index => $row) {
            if ($forzar || empty($this->localEmpresa[$index])) {
                $this->localEmpresa[$index] = $nuevoCuit;
            }
        }
    }

    public function aplicarEmpresaGlobales($nuevoCuit, $forzar = false)
    {
        $this->globalEmpresa = $nuevoCuit;

        foreach ($this->previewDataUbicacion as $index => $row) {
            if ($forzar || empty($this->localEmpresa[$index])) {
                $this->localEmpresa[$index] = $nuevoCuit;
            }
        }

        foreach ($this->previewDataActivo as $index => $row) {
            if ($forzar || empty($this->localEmpresa[$index])) {
                $this->localEmpresa[$index] = $nuevoCuit;
            }
        }
    }

    public function updatingSearchUsuarios()
    {
        $this->pageUsuarios = 1;
    }

    public function handleGeolocation($lat, $long)
    {
        $this->lat = $lat;
        $this->long = $long;
        $this->setAddress($lat, $long);
    }

    private function resetCachesCombo(): void
    {
        $this->cacheMarcasPorCombo  = [];
        $this->cacheModelosPorCombo = [];
    }

    private function tiposCampoMap(): array
    {
        return Cache::rememberForever('act.tipos_campo.map', function () {
            return \App\Models\TiposCamposModel::pluck('nombre', 'id_tipo_campo')
                ->map(fn($n) => mb_strtolower($n))
                ->toArray();
        });
    }

    private function valoresDeAtributo(int $idAtributo): array
    {
        $key = "act.atributo_valores.$idAtributo";
        return Cache::remember($key, 3600, function () use ($idAtributo) {
            return \App\Models\AtributosValoresModel::where('id_atributo', $idAtributo)
                ->orderBy('id_valor')
                ->get(['id_valor', 'valor'])
                ->map(fn($r) => ['id_valor' => (int)$r->id_valor, 'valor' => $r->valor])
                ->toArray();
        });
    }

    private function estadosGeneralAll()
    {
        return Cache::rememberForever(
            'act.estados_general',
            fn() =>
            EstadoGeneralModel::orderBy('nombre')->get(['id_estado_sit_general', 'nombre'])
        );
    }

    private function estadosAltaAll()
    {
        return Cache::rememberForever(
            'act.estados_alta',
            fn() =>
            EstadosAltasModel::orderBy('nombre')->get(['id_estado_sit_alta', 'nombre'])
        );
    }

    private function condicionesAll()
    {
        return Cache::rememberForever(
            'act.condiciones',
            fn() =>
            CondicionModel::orderBy('nombre')->get(['id_condicion', 'nombre'])
        );
    }

    private function normalizeName(string $s): string
    {
        // sin acentos, lower, trim
        return trim(mb_strtolower(Str::ascii($s)));
    }

    private function nameToIdMap(string $table, string $idCol, string $nameCol, array $scoped = [], ?string $cacheKey = null): array
    {
        $key = $cacheKey ?? sprintf(
            'dict.%s.%s.%s.%s',
            $table,
            $idCol,
            $nameCol,
            md5(json_encode($scoped))
        );

        return Cache::remember($key, 3600, function () use ($table, $idCol, $nameCol, $scoped) {
            $q = DB::table($table)->select($idCol, $nameCol);
            foreach ($scoped as $col => $val) $q->where($col, $val);
            return $q->get()->reduce(function ($carry, $r) use ($idCol, $nameCol) {
                $carry[$this->normalizeName((string)$r->$nameCol)] = (int)$r->$idCol;
                return $carry;
            }, []);
        });
    }

    public function aplicarSupervisorGlobal($id = null, $aplicarATodas = true)
    {
        $this->globalSupervisorUsuario = is_numeric($id) ? (int)$id : null;

        $name = '';
        if ($this->globalSupervisorUsuario) {
            $u = collect($this->supervisores)->firstWhere('id', $this->globalSupervisorUsuario);
            $name = is_array($u) ? ($u['name'] ?? '') : ($u->name ?? '');
        }

        if ($aplicarATodas && !empty($this->vistaPreviaUsuario)) {
            foreach ($this->vistaPreviaUsuario as $i => &$r) {
                $r['supervisor_usuario'] = $this->globalSupervisorUsuario; // <- ID o null
                $r['supervisor_usuario'] = $name;                          // <- nombre para mostrar
                $this->localSupervisorUsuario[$i] = $this->globalSupervisorUsuario;
            }
            unset($r);
        }
    }

    public function setSupervisorLocal(int $absIndex, $id = null)
    {
        $id = is_numeric($id) ? (int)$id : null;
        $this->localSupervisorUsuario[$absIndex] = $id;

        $name = '';
        if ($id) {
            $u = collect($this->supervisores)->firstWhere('id', $id);
            $name = is_array($u) ? ($u['name'] ?? '') : ($u->name ?? '');
        }

        if (isset($this->vistaPreviaUsuario[$absIndex])) {
            $this->vistaPreviaUsuario[$absIndex]['supervisor_usuario'] = $id;  // ID o null
            $this->vistaPreviaUsuario[$absIndex]['supervisor_usuario']  = $name; // nombre o ""
        }
    }


    public function setAddress($lat, $long)
    {
        try {
            $apiKey = config('services.google_maps.api_key');
            // Construir la URL para la geocodificación inversa
            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$long}&key={$apiKey}";
            // Realizar la consulta a Google Maps
            $response = Http::withOptions(['verify' => false])->get($url);

            if ($response->successful()) {
                $results = $response->json('results');
                if (!empty($results)) {
                    // Tomamos el primer resultado
                    $result = $results[0];
                    $components = $result['address_components'];
                    // Inicializamos un arreglo para almacenar los valores
                    $address = [];
                    // Recorremos los componentes para extraer la información
                    foreach ($components as $component) {
                        if (in_array('country', $component['types'])) {
                            $address['country'] = $component['long_name'];
                        }
                        if (in_array('administrative_area_level_1', $component['types'])) {
                            $address['state'] = $component['long_name'];
                        }
                        if (in_array('locality', $component['types'])) {
                            $address['city'] = $component['long_name'];
                        }
                        // Si no se encontró 'locality', se puede usar 'sublocality'
                        if (!isset($address['city']) && in_array('sublocality', $component['types'])) {
                            $address['town'] = $component['long_name'];
                        }
                        if (in_array('route', $component['types'])) {
                            $address['road'] = $component['long_name'];
                        }
                        if (in_array('street_number', $component['types'])) {
                            $address['house_number'] = $component['long_name'];
                        }
                        if (in_array('postal_code', $component['types'])) {
                            $address['postcode'] = $component['long_name'];
                        }
                    }
                    // Asignar los valores a las variables usando la misma nomenclatura que usas
                    $this->pais = $address['country'] ?? '';
                    $this->provincia = $address['state'] ?? '';
                    $this->ciudad = $address['city'] ?? ($address['town'] ?? '');
                    $this->calle = $address['road'] ?? '';
                    $this->altura = $address['house_number'] ?? '';
                    $this->codigo_postal = $address['postcode'] ?? '';

                    $this->dispatch('addressUpdated');
                } else {
                    // No se encontraron resultados
                    $this->dispatch('error', ['message' => 'No se encontró ninguna dirección para las coordenadas proporcionadas.']);
                }
            }
        } catch (\Exception $e) {
            logger()->error('Exception occurred in setAddress', ['exception' => $e->getMessage()]);
        }
    }

    private function UsuariosEmpresas()
    {
        $usuario = UsuariosEmpresasModel::where('cuit', IdHelper::idEmpresa())
            ->where('estado', 'Aceptado')->pluck('id_usuario');

        return User::whereIn('id', $usuario)->get();
    }

    public function close()
    {
        $this->reset([
            'previewData',
            'localTipoUbicacion',
            'globalTipoUbicacion',
            'previewDataUbicacion',
            'vistaPreviaUsuario',
            'localGestor',
            'globalGestor',
            'localGestores',
            'globalGestores',
            'localAsignado',
            'globalAsignado',
            'localResponsable',
            'globalResponsable',
            'vistaPreviaUsuario',
        ]);
    }

    public function closeCompleto()
    {
        $this->reset([
            'archivo',
            'tipoDatos',
            'tipoOperacion',
            'previewData',
            'previewDataUbicacion',
            'previewDataActivo',
            'previewDataClientes',
            'previewDataProveedores',
            'localTipoUbicacion',
            'globalTipoUbicacion',
            'localGestor',
            'globalGestor',
            'localGestores',
            'globalGestores',
            'localAsignado',
            'globalAsignado',
            'localResponsable',
            'globalResponsable',
            'vistaPreviaUsuario',
        ]);
    }
}
