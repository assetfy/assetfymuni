<?php

namespace App\Livewire\Activos;

use Livewire\Component;
use App\Helpers\IdHelper;
use Illuminate\Support\Facades\Cache;
use App\Models\ActivosModel;
use App\Services\ActivosReferenceService;
use Livewire\WithFileUploads;
use App\Models\AtributosModel;
use App\Models\UbicacionesModel;
use App\Models\ActivosAtributosModel;
use App\Models\ActivosCompartidosModel;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Str;
use App\Http\Requests\StoreActivoValidacion;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\AtributosSubcategoriaModel;
use App\Models\AtributosValoresModel;
use App\Models\EmpresasModel;
use App\Models\MarcasModel;
use App\Models\ModelosModel;
use App\Models\OrganizacionUnidadesModel;
use App\Models\PisosModel;
use App\Services\MiddlewareInvoker;
use App\Services\GeneradorDeGestoresAsignacion;
use App\Helpers\Funciones;
use App\Models\EstadoGeneralModel;

class CreateActivos extends Component
{
    use WithFileUploads;
    private ActivosReferenceService $refsService;
    public array $refsData = [];
    public $origen = 'default';
    // Propiedades del componente
    public $currentStep = 1;
    public $selectedAtributos = [], $datoAtributo = [], $campo_numerico = [], $campo = [];
    public $open = false;
    public $id_estado_sit_alta, $id_estado_sit_general, $id_tipo, $id_categoria, $id_subcategoria, $id_ubicacion,
        $categoria, $subcategoria, $comentarios_sit_alta, $estado_inventario, $empresa_titular, $usuario_titular, $ubicacion, $altas, $general, $campos,
        $etiqueta, $numero_serie, $nombre, $tipoAsociado, $tipos, $imagen, $propietario, $tipo, $categoriasTipos, $valorTipo, $nombreTipo, $ubicaciones,
        $gestionado_por, $asignado_a, $usuarioEmpresa, $empresas, $users, $user, $empleadosLista, $noEmpleadosEncontrados, $responsable, $responsable_id,
        $id_usuario, $selectedEmpleado, $fecha_asignacion, $asignado_a_id, $gestionado_por_id, $cuit_empresa, $cert_garantia, $empresaTitular;
    public $searchGestionado, $searchAsignado, $searchResponsable, $sinMarca, $categorias;
    public $fotos = []; // Todas las fotos (subidas y capturadas)
    public $nuevasFotos = []; // Nuevas fotos subidas
    public $capturedPhotos = []; // Fotos capturadas desde la cámara
    // Propiedades para los dropdowns personalizados
    public $selectedTipoNombre, $searchTipo, $tipoPrueba, $allNivelesPlano;
    public $selectedCategoriaNombre, $searchCategoria, $padreNombre;
    public $categorias2;
    public $selectedSubcategoriaNombre;
    public $searchSubcategoria = '';
    public $selectedUbicacionNombre, $searchUbicacion, $ubicacionesList;
    public $tipoInicial = false; // Indica si el tipo inicial proviene del servidor
    public $sinUbicacion = -1;
    public $activoId;
    public $fecha = [];
    public $fecha_compra;
    public $factura_compra;
    public $garantia_vigente = 'No'; // Valor por defecto
    public $vencimiento_garantia;
    public $atributoDefinid = [];
    public $AtributoMultiple = [];
    public $atributosValores = [];
    public $atributosSeleccionadosValoresCheckboxes = [];
    public $atributosSeleccionadosValoresSelects = [];
    public $ListaMarcas,  $selectedMarcaNombre, $searchMarca;
    public $ListaModelos, $selectedModeloNombre, $searchModelo, $selectedPiso, $nivelesPlano;
    public $id_marca, $padreId, $searchPadre;
    public $id_modelo;
    public $piso;
    public $baseMarcas, $marca;
    public $baseModelos;
    public $empresaActual;
    public $empresaUbicacionesDelegadas, $filteredEmpresas, $cuit, $empresaBusqueda, $searchEmpresas, $ubicacionesEmpresa, $subcategorias, $pisosOpcion;
    public $condiciones, $id_condicion, $subcategoriaBuscada;
    public ?int $selectedLevel = null;
    public $tiposById, $categoriasByTipo, $subsByCategoria, $subsById, $tiposDatosCompletos, $prestado;
    public $tercero_nombre, $tipo_tercero;

    public $searchUbicacionDelegada = '';

    public $noHayUbicacionesDelegadas = false;
    protected $fotoService;

    // Para manejo de archivos
    public $factura_compra_path;

    protected $listeners = ['crearActivo', 'createActivos', 'ubicacionCreada' => 'actualizarUbicaciones', 'setPadre'];

    public function updatedOpen()
    {
        if ($this->open) {
            $this->resetUbicaciones(); // Solo resetea si el modal se abre
        }
    }

    public function boot(ActivosReferenceService $service)
    {
        // Inyección automática del servicio
        $this->refsService = $service;
    }

    public function prepararPaso6()
    {
        $this->cargarNivelesPlano();
        $this->searchEmpleados();
    }

    public function cargarNivelesPlano()
    {
        $cacheKey = 'niveles_plano_' . $this->empresaActual;

        // Guarda en cache por 60 minutos
        $todos = Cache::remember($cacheKey, now()->addMinutes(60), function () {
            return OrganizacionUnidadesModel::where('CuitEmpresa', $this->empresaActual)
                ->orderBy('PadreId')
                ->get();
        });

        $this->allNivelesPlano = $todos->map(fn($item) => (object)[
            'Id' => $item->Id,
            'Nombre' => $item->Nombre,
        ])->toArray();

        $this->nivelesPlano = $this->allNivelesPlano;

        $datosPrueba = collect($todos)
            ->map(fn($item) => [
                'id'     => (string) $item->Id,
                'padre'  => $item->PadreId ? (string) $item->PadreId : null,
                'nombre' => $item->Nombre,
            ])
            ->values()
            ->toArray();

        $this->dispatch('init-jstree', ['data' => $datosPrueba]);
    }


    public function createActivos($origen = 'default')
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->origen = $origen;
            $this->id_ubicacion = null;
            $this->selectedUbicacionNombre = 'Sin Ubicación';
            $this->searchUbicacion = '';
            $this->open = true;
        }
    }

    public function resetUbicaciones()
    {
        $user = auth()->user();
        if ($user->panel_actual == 'Usuario') {
            $this->ubicacionesList = UbicacionesModel::where('cuil', $this->usuario_titular)->get();
        } else {
            $this->ubicacionesList = UbicacionesModel::where('cuit', $this->empresaActual)->get();
        }

        // Opcional: Restablece también la búsqueda y selección actual si es necesario
        $this->id_ubicacion = null;
        $this->selectedUbicacionNombre = 'Sin Ubicación';
        $this->searchUbicacion = '';
    }

    public function mount()
    {
        $this->user = IdHelper::usuarioActual();
        $this->empresaActual  = IdHelper::empresaActual()->cuit;
        // Configurar ubicaciones
        $this->resetUbicaciones();
        // Verificar si el tipo ya viene cargado
        if (session()->has('tipo')) {
            $this->id_tipo = session('tipo');
            $this->setTipo($this->id_tipo); // Cargar categorías relacionadas
            $this->tipoInicial = true; // Marcar como tipo inicial del servidor
        }
        $this->cargaCache();
    }

    private function cargaCache(): void
    {
        $this->refsService->clearCache();
        $this->refsData = $this->refsService->getReferenceData();
        //collect($arr) convierto el array en una collecion xq no pude hacer que funcione con array,->map(fn($i) => (object) $i) Recorre cada elemento $i y lo castea a stdClass ((object) $i).
        $Objetos = fn($arr) => collect($arr)->map(fn($i) => (object) $i)->values();
        // bases
        $this->subcategorias = $Objetos($this->refsData['subcategorias'] ?? []);
        $this->tipoPrueba    = $Objetos($this->refsData['tipoPrueba']    ?? []);
        $this->altas         = $Objetos($this->refsData['altas']         ?? []);
        $this->condiciones   = $Objetos($this->refsData['condiciones']   ?? []);
        $this->general       = $Objetos($this->refsData['general']       ?? []);
        $this->campos        = $Objetos($this->refsData['campos']        ?? []);
        $this->categorias2   = $Objetos($this->refsData['categorias']    ?? []);
        //Datos Completos para busqueda
        $this->tiposDatosCompletos =  $this->tipoPrueba;
        // índices
        $this->tiposById = collect($this->refsData['tipos_by_id'] ?? [])
            ->map(fn($i) => (object) $i);

        $this->categoriasByTipo = collect($this->refsData['categorias_by_tipo'] ?? [])
            ->map(fn($grp) => collect($grp)->map(fn($i) => (object) $i)->values());

        $this->subsByCategoria = collect($this->refsData['subs_by_categoria'] ?? [])
            ->map(fn($grp) => collect($grp)->map(fn($i) => (object) $i)->values());

        $this->subsById = collect($this->refsData['subs_by_id'] ?? [])
            ->map(fn($i) => (object) $i);
    }

    private function Ubicaciones()
    {
        // Configurar ubicaciones
        if ($this->user->panel_actual == 'Usuario') {
            $this->ubicacionesList = UbicacionesModel::where('cuil', $this->user->cuil)->get();
            $this->propietario = 'Propio';
        } else {
            $this->ubicacionesList = UbicacionesModel::where('cuit',  $this->empresaTitular)->get();
            $this->searchEmpleados();
            $this->isEmpleado();
            $this->updatedSearchEmpresas();
            $this->updatedSearchUbicacionDelegada();
            $this->empresa();
            if ($this->origen == 'bienes_aceptados') {
                $this->propietario = 'Cliente';
            }
        }
    }

    public function save()
    {
        // Iniciar la transacción
        DB::beginTransaction();
        try {
            // Crear el activo
            if ($this->id_ubicacion) {
                $this->CargarPiso();
            }
            $activo = $this->createActivo();

            $this->dispatch('activoCreado', $activo->id_activo, $activo->nombre);

            // Guardar fotos subidas y capturadas
            $fotoService = app(\App\Services\FotoActivoService::class);

            $metadatos = [
                'id_tipo' => $this->id_tipo,
                'id_categoria' => $this->id_categoria,
                'id_subcategoria' => $this->id_subcategoria,
            ];

            $fotoService->guardarFotosSubidas($this->fotos, $activo->id_activo, $metadatos);
            $fotoService->guardarFotosCapturadas($this->capturedPhotos, $activo->id_activo, $metadatos);

            // Crear atributos asociados
            $this->crearAtributos($activo);

            if (!$this->isUser() && !$this->isEmpleado()) {
                $this->createGestores($activo);

                // Verificar que sea desde la vista de Bienes Clientes
                if ($this->origen == 'bienes_aceptados') {
                    $this->createCompartidos($activo);
                }
            }
            // Confirmar la transacción si todo fue exitoso
            DB::commit();
            // Emitir eventos necesarios
            $this->dispatch('lucky');
            $this->dispatch('render');

            if ($this->origen == 'bienes_aceptados') {
                $this->dispatch('refreshBienesAceptados');
            }

            $this->dispatch('refreshBienes');

            // Cerrar el modal y resetear las propiedades
            $this->close();
        } catch (\Exception $e) {
            // En caso de cualquier error, hacer rollback de la transacción
            DB::rollBack();
            // Manejar el error y mostrar un mensaje al usuario
            $this->dispatch('errorInfo', [
                'title'   => 'Error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function setPadre(int $id)
    {
        $this->padreId = $id;
        $u = OrganizacionUnidadesModel::find($id);
        $this->padreNombre = $u?->Nombre;
    }

    private function cargarPiso()
    {
        if ($this->selectedPiso) {
            $nuevoPiso = PisosModel::create([
                'id_ubicacion' => $this->id_ubicacion,
                'nombre'       => $this->selectedPiso,
            ]);
            $this->piso  = $nuevoPiso;
        }
    }
    // Establece el Tipo seleccionado y carga las Categorías asociadas
    public function setTipo(int $id): void
    {
        // 1) Tipo desde el índice
        $tipo = $this->tiposById->get($id);
        if (!$tipo) {
            // opcional: limpiar selección si llegó un id inválido
            $this->id_tipo = null;
            $this->selectedTipoNombre = null;
            $this->categorias = collect();
            return;
        }
        $this->id_tipo            = $id;
        $this->selectedTipoNombre = $tipo->nombre ?? '';
        $this->searchTipo         = '';
        // 2) Categorías del tipo (ya agrupadas en el índice)
        $this->categorias = $this->categoriasByTipo->get($id, collect())->values();
        // 3) Reset en cascada
        $this->id_categoria               = null;
        $this->selectedCategoriaNombre    = null;
        $this->subcategoria               = collect();
        $this->id_subcategoria            = null;
        $this->selectedSubcategoriaNombre = null;
    }

    public function setCategoria(int $id): void
    {
        // 1) La que está actualmente en el dropdown filtrado
        $cat = $this->categorias->firstWhere('id_categoria', $id);
        // 2) Fallback al “maestro” (categorias2 o índice)
        if (!$cat) {
            // mejor usar un keyBy global:
            $cat = $this->categorias2->firstWhere('id_categoria', $id);
            // o $this->categoriasById->get($id) si lo creaste.
        }

        if (!$cat) {
            // id inválido, limpia y sal
            $this->id_categoria            = null;
            $this->selectedCategoriaNombre = null;
            return;
        }

        $this->id_categoria            = $id;
        $this->selectedCategoriaNombre = $cat->nombre ?? '';
        $this->searchCategoria         = '';
        // 3) Cargar subcategorías del índice
        $this->cargaSubcategoria($id);
    }

    public function setSubcategoria(int $id): void
    {
        // 1) Del subset filtrado actual
        $sub = $this->subcategoria->firstWhere('id_subcategoria', $id);

        // 2) Fallback al índice global
        if (!$sub && isset($this->subsById)) {
            $sub = $this->subsById->get($id);
        }

        if (!$sub) {
            $this->id_subcategoria            = null;
            $this->selectedSubcategoriaNombre = null;
            return;
        }

        $this->id_subcategoria            = $id;
        $this->selectedSubcategoriaNombre = $sub->nombre ?? '';

        $this->cargaAtributos();
    }

    public function updatedSearchTipo(string $value): void
    {
        // Colección completa desde memoria
        $all = collect($this->tipoPrueba);
        if (trim($value) === '') {
            $this->tipoPrueba =  $this->tiposDatosCompletos;  // vuelve a Collection
        } else {
            $needle = Str::lower($value);
            $this->tipoPrueba = $all->filter(
                fn($t) => Str::contains(Str::lower(data_get($t, 'nombre', '')), $needle)
            )->values();
        }
        // Si el tipo seleccionado ya no está en la lista filtrada, reset cascada
        if ($this->id_tipo && ! $this->tipoPrueba->contains('id_tipo', $this->id_tipo)) {
            $this->id_tipo                    = null;
            $this->selectedTipoNombre         = null;

            $this->categorias                 = collect();
            $this->id_categoria               = null;
            $this->selectedCategoriaNombre    = null;

            $this->subcategoria               = collect();
            $this->id_subcategoria            = null;
            $this->selectedSubcategoriaNombre = null;
        }
    }

    public function updatedSearchCategoria(string $valor): void
    {
        // Subset base: todas las categorías del tipo actual
        $base = isset($this->categoriasByTipo)
            ? $this->categoriasByTipo->get($this->id_tipo, collect())->values()
            : collect($this->refsData['categorias'] ?? [])
            ->where('id_tipo', $this->id_tipo)
            ->values();

        if (trim($valor) === '') {
            $this->categorias = $base;
        } else {
            $filtro = Str::lower($valor);
            $this->categorias = $base->filter(
                fn($c) => Str::contains(Str::lower($c->nombre), $filtro)
            )->values();
        }
        // Reset si la categoría elegida ya no está
        if ($this->id_categoria && ! $this->categorias->contains('id_categoria', $this->id_categoria)) {
            $this->id_categoria               = null;
            $this->selectedCategoriaNombre    = null;

            $this->subcategoria               = collect();
            $this->id_subcategoria            = null;
            $this->selectedSubcategoriaNombre = null;
        }
    }

    public function updatedSearchMarca(string $value)
    {
        if (trim($value) === '') {
            // campo vacío → restaurar toda la base
            $this->ListaMarcas = $this->baseMarcas;
        } else {
            $this->ListaMarcas = $this->baseMarcas
                ->filter(fn($m) => stripos($m->nombre, $value) !== false)
                ->values();
        }
    }

    public function updatedSearchModelo(string $value)
    {
        if (trim($value) === '') {
            $this->ListaModelos = $this->baseModelos;
        } else {
            $this->ListaModelos = $this->baseModelos
                ->filter(fn($m) => stripos($m->nombre, $value) !== false)
                ->values();
        }
    }

    public function modelos()
    {
        $query = ModelosModel::where('id_subcategoria', $this->id_subcategoria)
            ->where('id_categoria', $this->id_categoria)
            ->where('id_tipo', $this->id_tipo);
        if ($this->id_marca) {
            $query->where('id_marca', $this->id_marca);
        }
        $col = $query->get();
        $this->baseModelos  = $col;
        $this->ListaModelos = $col;
        // y recarga las marcas para actualizar baseMarcas
        $this->marcas();
    }

    public function setModelo(int $id)
    {
        $modelo = ModelosModel::find($id);

        $this->id_modelo              = $id;
        $this->selectedModeloNombre   = $modelo->nombre;
        $this->searchModelo           = '';                // limpia el input de búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'modelo']);
    }

    public function setMarca(int $id)
    {
        $marca = MarcasModel::find($id);

        $this->id_marca             = $id;
        $this->selectedMarcaNombre  = $marca->nombre;
        $this->searchMarca          = '';                // limpia el input de búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'marca']);
        // Si quieres recargar los modelos en base a la marca seleccionada:
        $this->modelos();
    }

    private function marcas()
    {
        $col = MarcasModel::whereIn('id_marca', $this->ListaModelos->pluck('id_marca'))->get();
        $this->baseMarcas  = $col;
        $this->ListaMarcas = $col;
    }
    // Actualiza la búsqueda de Subcategorías
    public function updatedSearchSubcategoria(string $valor): void
    {
        // 1) Subset base (todas las subs de la categoría/tipo actual)
        $base = isset($this->subsByCategoria)
            ? $this->subsByCategoria
            ->get($this->id_categoria, collect())
            ->where('id_tipo', $this->id_tipo)
            ->values()
            : collect($this->subcategorias)
            ->where('id_categoria', $this->id_categoria)
            ->where('id_tipo', $this->id_tipo)
            ->values();

        // 2) Filtro por texto
        if (trim($valor) === '') {
            $this->subcategoria = $base;
        } else {
            $needle = Str::lower($valor);
            $this->subcategoria = $base->filter(
                fn($s) => Str::contains(Str::lower($s->nombre), $needle)
            )->values();
        }
        // 3) Si la seleccionada ya no está en la lista filtrada, la limpio
        if (
            $this->id_subcategoria &&
            ! $this->subcategoria->contains('id_subcategoria', $this->id_subcategoria)
        ) {
            $this->id_subcategoria            = null;
            $this->selectedSubcategoriaNombre = null;
        }
    }

    public function updatedSearchUbicacion()
    {
        $user = auth()->user();

        // Identificar el panel actual y definir la búsqueda
        $query = UbicacionesModel::query();
        if ($user->panel_actual == 'Usuario') {
            $query->where('cuil', $this->usuario_titular);
        } else {
            $query->where('cuit', $this->empresaActual);
        }
        // Aplicar filtro de búsqueda si existe
        if ($this->searchUbicacion) {
            $query->where('nombre', 'like', '%' . $this->searchUbicacion . '%');
        }
        // Obtener los resultados
        $this->ubicacionesList = $query->get();
        // Manejar caso de lista vacía y selección no válida
        if ($this->ubicacionesList->isEmpty()) {
            // Mostrar mensaje de "Sin resultados"
            $this->id_ubicacion = null;
            $this->selectedUbicacionNombre = null;
        } elseif (!$this->ubicacionesList->contains('id_ubicacion', $this->id_ubicacion)) {
            // Si la búsqueda no coincide con la ubicación seleccionada
            $this->id_ubicacion = null;
            $this->selectedUbicacionNombre = null;
        }
    }
    // Define una constante para "Sin Ubicación"
    const SIN_UBICACION = -1;

    public function setUbicacion($id)
    {
        if ($id === self::SIN_UBICACION) {
            $this->id_ubicacion = null; // Representa "Sin Ubicación".
            $this->selectedUbicacionNombre = 'Sin Ubicación';
        } else {
            $ubicacion = UbicacionesModel::find($id);
            if ($ubicacion) {
                $this->id_ubicacion = $ubicacion->id_ubicacion;
                $this->selectedUbicacionNombre = $ubicacion->nombre;
                $this->pisosDisponibles($ubicacion);
            } else {
                $this->id_ubicacion = null;
                $this->selectedUbicacionNombre = null;
            }
        }
        $this->searchUbicacion = '';
        $this->dispatch('closeDropdown', ['dropdown' => 'ubicacion']);
    }

    private function pisosDisponibles($ubicacion)
    {
        if (! $ubicacion->multipisos) {
            $this->pisosOpcion  = [];
            return;
        }

        $max = 1 + (int) $ubicacion->piso;  // p. ej. 4 si hay PB + 3 pisos
        $options = [];

        // Generamos manualmente cada valor
        for ($i = 0; $i < $max; $i++) {
            if ($i === 0) {
                $options[] = 'Planta Baja';      // Planta baja
            } else {
                $options[] = 'Piso ' . $i;   // "1", "2", "3", ...
            }
        }

        $this->pisosOpcion = $options;
    }

    public function actualizarUbicaciones($id, $nombre)
    {
        // Recargar la lista de ubicaciones desde la base de datos
        $this->resetUbicaciones();

        // Seleccionar automáticamente la nueva ubicación
        $this->id_ubicacion = $id;
        $this->selectedUbicacionNombre = $nombre;
        $this->pisosDisponibles(UbicacionesModel::find($id));
    }

    private function maxSteps(): int
    {
        // Si para no-inmueble tenés 6, poné 6 acá.
        // En tu Blade actual hay 5, así que dejo 5/5.
        return $this->isInmueble() ? 5 : 5; // ó 5 : 6 si tu flujo normal tiene 6
    }
    /**
     * Valida sólo las reglas del paso actual
     */
    protected function validateStep(int $step): bool
    {
        if ($step === 2 && $this->isInmueble()) {
            // El Paso 2 no corre para Inmueble, no validar nada acá
            return true;
        }

        $rules = StoreActivoValidacion::rulesForStep($step);
        if (count($rules) === 0) return true;

        $this->validate($rules);
        return true;
    }
    public function nextStep()
    {
        $max = $this->maxSteps();

        if (!$this->validateStep($this->currentStep)) {
            return;
        }

        // Paso 1 -> si es inmueble, saltar al 3 (atributos); si no, ir al 2
        if ($this->currentStep === 1) {
            $this->currentStep = $this->isInmueble() ? 3 : 2;
            return;
        }

        // Paso 2 (solo no-inmueble): si no hay atributos, saltar al 4 como ya hacías
        if ($this->currentStep === 2 && !$this->isInmueble()) {
            // por las dudas, refresco atributos
            $this->cargaAtributos();
            if (empty($this->datoAtributo)) {
                $this->currentStep = 4;
                return;
            }
        }

        if ($this->currentStep < $max) {
            $this->currentStep++;
        }
    }

    // Retrocede al paso anterior
    public function previousStep()
    {
        if ($this->currentStep <= 1) return;

        // Si estoy en 3 y es inmueble, al volver voy a 1 (porque el 2 no existe en este flujo)
        if ($this->currentStep === 3 && $this->isInmueble()) {
            $this->currentStep = 1;
            return;
        }

        // Si estoy en 4 y no-inmueble, pero se saltó 2 por no tener atributos, volver a 2
        if ($this->currentStep === 4 && !$this->isInmueble() && empty($this->datoAtributo)) {
            $this->currentStep = 2;
            return;
        }

        $this->currentStep--;
    }

    public function prepararPaso2()
    {
        $this->modelos();
        $this->cargaAtributos();
    }

    public function updatedFechaCompra($value)
    {
        $this->dispatch('fechaCompraUpdated', $value);
    }

    // Cierra el modal y resetea las propiedades
    public function close()
    {
        $this->reset([
            'id_tipo',
            'selectedTipoNombre',
            'searchTipo',
            'id_categoria',
            'selectedCategoriaNombre',
            'searchCategoria',
            'categorias2',
            'id_subcategoria',
            'selectedSubcategoriaNombre',
            'nombre',
            'id_estado_sit_alta',
            'comentarios_sit_alta',
            'id_ubicacion',
            'id_estado_sit_general',
            'propietario',
            'currentStep',
            'nuevasFotos',
            'capturedPhotos',
            'fotos',
            'selectedAtributos',
            'datoAtributo',
            'campo',
            'campo_numerico',
            'fecha_compra',
            'factura_compra',
            'garantia_vigente',
            'vencimiento_garantia',
            'atributosSeleccionadosValoresCheckboxes',
            'atributosSeleccionadosValoresSelects',
            'gestionado_por',
            'asignado_a',
            'fecha_asignacion',
            'empresaBusqueda',
            'cuit',
            'empresaUbicacionesDelegadas',
            'searchEmpresas',
            'cert_garantia',
            'numero_serie',
            'id_modelo',
            'id_marca',
            'selectedMarcaNombre',
            'pisosOpcion',
            'id_condicion',
            'prestado'
        ]);
        // Re-asignar colecciones y arrays para evitar null
        $this->subcategoria = collect();
        $this->categorias2 = $this->refsData['categorias'];
        $this->fotos = [];
        $this->capturedPhotos = [];
        $this->open = false;
    }
    // Crear el activo
    protected function createActivo()
    {
        if ($this->factura_compra) {
            $facturaPath = $this->factura_compra->store('StorageMvp/facturas', 's3');
        }

        if ($this->cert_garantia) {
            $certificadoPath = $this->cert_garantia->store('StorageMvp/facturas', 's3');
        }

        if ($this->origen == 'bienes_aceptados') {
            $empresaFinal = $this->empresa_titular !== $this->empresaActual ? $this->empresa_titular : $this->empresaActual;
            $this->propietario = 'Cliente';
        } else {
            $empresaFinal = $this->empresaActual;
        }

        $this->estado_inventario = $this->id_estado_sit_general == Funciones::activoBaja()
            ? 'Baja'
            : 'Activo';

        return ActivosModel::create([
            'nombre' => $this->nombre,
            'id_estado_sit_alta' => $this->id_estado_sit_alta ?? null,
            'comentarios_sit_alta' => $this->comentarios_sit_alta ?? null,
            'estado_inventario' => $this->estado_inventario,
            'id_estado_sit_general' => $this->id_estado_sit_general ?? null,
            'id_categoria' => $this->id_categoria,
            'id_tipo' => $this->id_tipo,
            'id_subcategoria' => $this->id_subcategoria,
            'usuario_titular' => $this->usuario_titular,
            'empresa_titular' => $empresaFinal,
            'propietario' => $this->propietario ?? 'Propio',
            'id_ubicacion' => $this->id_ubicacion,
            'fecha_compra' => $this->fecha_compra,
            'factura_compra' => $facturaPath ?? null,
            'garantia_vigente' => $this->garantia_vigente,
            'vencimiento_garantia' => $this->garantia_vigente === 'Si' ? $this->vencimiento_garantia : null,
            'cert_garantia' => $certificadoPath ?? null,
            'id_modelo' => $this->id_modelo  ?? null,
            'numero_serie' => $this->numero_serie  ?? null,
            'id_piso'   =>   $this->piso->id_piso ?? null,
            'id_Nivel_Organizacion' => $this->padreId ?? null,
            'prestado' => $this->prestado ?? 'No',
        ]);
    }

    public function updatedTipoTercero($value)
    {
        $this->general = EstadoGeneralModel::where('id_estado_sit_general', '!=', 5)
            ->where('id_estado_sit_general', '!=', 4)
            ->get(['id_estado_sit_general', 'nombre']);
    }


    protected function crearAtributos($activo)
    {
        $servicio = new \App\Services\GeneradorDeAtributosDeActivo(
            $this->selectedAtributos,
            $this->atributosSeleccionadosValoresCheckboxes,
            $this->atributosSeleccionadosValoresSelects,
            $this->campo,
            $this->campo_numerico,
            $this->fecha,
            $this->id_subcategoria,
            $this->id_categoria,
            $this->id_tipo,
        );

        $filas = $servicio->handle($activo->id_activo);

        // Inserción de un bloque
        foreach ($filas as $data) {
            ActivosAtributosModel::create($data);
        }
    }

    public function cargaSubcategoria(int $categoriaId): void
    {
        $subset = $this->subsByCategoria->get($categoriaId, collect());
        // Filtra por tipo actual (por si una categoría se comparte)
        $this->subcategoria = $subset
            ->where('id_tipo', $this->id_tipo)
            ->values();

        // Reset de dependientes
        $this->searchSubcategoria          = '';
        $this->id_subcategoria             = null;
        $this->selectedSubcategoriaNombre  = null;
    }

    protected function updateIdSubcategoria()
    {
        $this->updatedIdSubcategoria($this->id_subcategoria);
    }

    protected function clearDatoAtributo()
    {
        $this->datoAtributo = [];
    }

    public function cargaAtributos()
    {
        $this->tipoAsociado = AtributosSubcategoriaModel::where('id_subcategoria', $this->id_subcategoria)
            ->pluck('id_atributo')
            ->toArray();
        // Obtener la colección y convertirla a array
        $this->datoAtributo = AtributosModel::whereIn('id_atributo', $this->tipoAsociado)->get()->all();

        foreach ($this->datoAtributo as $datos) {
            if ($datos->predefinido == 'Si') {
                // Almacenar valores por id_atributo
                $this->atributoDefinid[$datos->id_atributo] = $datos->predefinido;
                $this->AtributoMultiple[$datos->id_atributo] = $datos->SelectM;
                $this->atributosValores[$datos->id_atributo] = AtributosValoresModel::where('id_atributo', $datos->id_atributo)->get();
            }
        }
    }

    public function updatedNuevasFotos()
    {
        foreach ($this->nuevasFotos as $newFoto) {
            $this->fotos[] = $newFoto; // Añadir cada nueva foto al array $fotos
        }

        $this->reset('nuevasFotos'); // Limpiar $nuevasFotos después de procesarlas
    }

    public function saveCapturedPhoto($imageData)
    {
        if ((count($this->fotos) + count($this->capturedPhotos)) < 10) {
            $this->capturedPhotos[] = $imageData; // Añadir la imagen capturada al array $capturedPhotos
        }
    }

    public function removeFoto($index)
    {
        if (isset($this->fotos[$index])) {
            unset($this->fotos[$index]);
            $this->fotos = array_values($this->fotos); // Reindexar el array
        }
    }

    public function removeCapturedPhoto($index)
    {
        if (isset($this->capturedPhotos[$index])) {
            unset($this->capturedPhotos[$index]);
            $this->capturedPhotos = array_values($this->capturedPhotos); // Reindexar el array
        }
    }

    public function updatedSearchGestionado()
    {
        $this->searchEmpleados();
    }

    public function updatedSearchAsignado()
    {
        $this->searchEmpleados();
    }

    //Busqueda de los empleados para ser asignados
    public function setAsignadoA($id)
    {
        $empleado = User::find($id);
        $this->asignado_a = $empleado ? $empleado->name : null;
        $this->asignado_a_id = $empleado->id;
        $this->searchAsignado = ''; // Limpiar búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'asignado']); // Cerrar el dropdown
    }

    //Busqueda de los empleados para ser responsables
    public function setResponsable($id)
    {
        $empleado = User::find($id);
        $this->responsable = $empleado ? $empleado->name : null;
        $this->responsable_id = $empleado->id;
        $this->searchResponsable = ''; // Limpiar búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'responsable']); // Cerrar el dropdown
    }

    // Busca los empleados de dicha empresa para ser mostrados
    private function searchEmpleados()
    {
        // ids permitidos…
        $usuariosIds = UsuariosEmpresasModel::where('cuit', $this->empresaActual)
            ->where('cargo', '!=', 'Apoderado')
            ->where('estado', 'Aceptado')
            ->where('tipo_user', '!=', 2)
            ->pluck('id_usuario');

        if ($usuariosIds->isEmpty()) {
            $this->empleadosLista = collect();
            return;
        }

        // aquí unificas los tres posibles search…
        $nombreBuscado = $this->searchResponsable
            ?: $this->searchAsignado
            ?: $this->searchGestionado
            ?: '';

        $this->empleadosLista = User::whereIn('id', $usuariosIds)
            ->where('name', 'like', "%{$nombreBuscado}%")
            ->get();
    }


    protected function createGestores($activo)
    {
        (new GeneradorDeGestoresAsignacion(
            $activo,
            $this->asignado_a_id,
            $this->responsable_id,
            $this->fecha_asignacion,
            $this->user->id,
            $this->empresaActual
        ))->handle();
    }

    public function updatedSearchResponsable(string $value)
    {
        // actualizamos el término de búsqueda
        $this->searchResponsable = $value;
        // y recargamos tu lista de empleados
        $this->searchEmpleados();
    }


    protected function createCompartidos($activo)
    {
        $empresaFinal = $this->empresa_titular !== $this->empresaActual ? $this->empresa_titular : $this->empresaActual;

        $data =
            [
                'id_activo' => $activo->id_activo,
                'id_subcat' => $activo->id_subcategoria,
                'id_cat' => $activo->id_categoria,
                'id_tipo' => $activo->id_tipo,
                'empresa_titular' => $empresaFinal,
                'empresa_proveedora' => $this->empresaActual,
                'estado_asignacion' => 'Aceptado',
            ];

        ActivosCompartidosModel::create($data);
    }
    // Usuario
    public function isUser()
    {
        return $this->user->panel_actual === 'Usuario';
    }
    // Si el usuario es empleado
    public function isEmpleado()
    {
        return UsuariosEmpresasModel::where('id_usuario', $this->user->id)
            ->where('cuit', $this->empresaActual)
            ->where('cargo', '=', 'Empleado')
            ->where('estado', 'Aceptado')
            ->exists();
    }
    // Para busqueda de ubicaciones delegadas
    public function updatedSearchEmpresas()
    {
        $this->cargarEmpresasUbicaciones();
    }

    public function updatedSearchUbicacionDelegada()
    {
        $this->cargarUbicacionesEmpresa(); // Llama a cargar las ubicaciones filtradas
    }

    private function cargarEmpresasUbicaciones()
    {
        $apoderado = UsuariosEmpresasModel::where('id_usuario', $this->user->id)
            ->where('cuit', $this->empresaActual)
            ->where('estado', 'Aceptado')
            ->exists();

        if ($apoderado) {
            $this->empresaUbicacionesDelegadas = UbicacionesModel::where('cuit_empresa', $this->empresaActual)
                ->pluck('cuit')
                ->unique();

            // Obtener los nombres de las empresas utilizando los CUIT obtenidos y aplicar filtro por búsqueda
            $this->filteredEmpresas = EmpresasModel::whereIn('cuit', $this->empresaUbicacionesDelegadas)
                ->where(function ($query) {
                    $query->where('razon_social', 'like', '%' . $this->searchEmpresas . '%');
                })
                ->get(['cuit', 'razon_social']);  // Obtenemos los nombres y CUIT
        } else {
            $this->empresaUbicacionesDelegadas = collect(); // Asegura que sea una colección vacía si no hay resultados
            $this->filteredEmpresas = collect(); // Asegura que también esté vacío si no hay empresas
        }
    }

    public function cargarUbicacionesEmpresa()
    {
        $this->empresaUbicacionesDelegadas = UbicacionesModel::where('cuit_empresa', $this->empresaActual)
            ->pluck('cuit')
            ->unique();

        // Verificar si no se han delegado ubicaciones
        if ($this->empresaUbicacionesDelegadas->isEmpty()) {
            // Aquí puedes agregar un mensaje o una bandera para indicar que no hay ubicaciones delegadas
            $this->noHayUbicacionesDelegadas = true;
            $this->ubicacionesEmpresa = collect(); // Asegurar que la colección no tenga datos incorrectos
        } else {
            $searchTerm = !empty($this->searchUbicacion) ? '%' . $this->searchUbicacion . '%' : '%';

            // Obtener las ubicaciones de la empresa seleccionada filtradas por el nombre
            $this->ubicacionesEmpresa = UbicacionesModel::where('cuit', $this->empresaUbicacionesDelegadas->toArray())
                ->where('cuit_empresa', $this->empresaActual)
                ->where('nombre', 'like', $searchTerm)
                ->get();

            // Si no hay ubicaciones, asegurarse de que la colección esté vacía
            if ($this->ubicacionesEmpresa->isEmpty()) {
                $this->ubicacionesEmpresa = collect();
            }
        }
    }

    public function setCuitEmpresa($cuit)
    {
        $this->cuit = $cuit;
        $this->empresaBusqueda = EmpresasModel::where('cuit', $cuit)->first();
        $this->empresa_titular = $this->cuit; // Asignar el valor seleccionado a empresa_titular

        // Obtener las ubicaciones de la empresa seleccionada
        $this->ubicacionesEmpresa = UbicacionesModel::where('cuit', $cuit)
            ->where('cuit_empresa', $this->empresaActual)
            ->where(function ($query) {
                $query->where('nombre', 'like', '%' . $this->searchUbicacionDelegada . '%');
            })
            ->get();

        $this->searchEmpresas = '';

        // Filtrar las empresas que coinciden con los cuit obtenidos
        $this->filteredEmpresas = EmpresasModel::whereIn('cuit', $this->empresaUbicacionesDelegadas)
            ->get(['cuit', 'razon_social']);  // Obtenemos los nombres y CUIT

        $this->dispatch('closeDropdown', ['dropdown' => 'empresa']); // Cerrar el dropdown
    }

    public function setUbicacionDelegada($idUbicacion)
    {
        $this->id_ubicacion = $idUbicacion;

        // Buscar la ubicación seleccionada
        $ubicacion = UbicacionesModel::where('id_ubicacion', $this->id_ubicacion)->first();

        if ($ubicacion) {
            $this->selectedUbicacionNombre = $ubicacion->nombre;

            // ✅ Cargar los pisos disponibles si tiene multipisos
            $this->pisosDisponibles($ubicacion);
        }

        // Cerrar el dropdown
        $this->dispatch('closeDropdown', ['dropdown' => 'ubicacion']);
    }

    // Crear bienes
    public function crearUbicaciones()
    {
        $this->dispatch('crearUbicacion');
    }

    private function empresa()
    {
        return EmpresasModel::where('cuit', $this->empresaActual)->pluck('tipo')->first();
    }

    private function isInmueble(): bool
    {
        return \App\Helpers\Funciones::isInmueble($this->selectedTipoNombre ?? '');
    }

    public function render()
    {
        return view(
            'livewire.activos.create-activos',
            [
                'esUsuario' => $this->isUser(),
                'empleado' => $this->isEmpleado(),
                'filteredEmpresas' => $this->filteredEmpresas,
                'empresaPrestadora' => $this->empresa(),
                'inmueble'         => $this->isInmueble(),
                'maxSteps'         => $this->maxSteps(),
            ]
        );
    }
}
