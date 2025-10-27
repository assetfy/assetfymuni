<?php

namespace App\Livewire\Servicios\OrdenesProveedores;

use App\Helpers\IdHelper;
use App\Models\ActivosModel;
use App\Models\CategoriaModel;
use App\Models\ContratoBienesModel;
use App\Models\ContratoModel;
use App\Models\ContratoUbicacionesModel;
use App\Models\EmpresasModel;
use App\Models\MisProveedoresModel;
use App\Models\OrdenesAdjuntoModel;
use App\Models\ordenesBienesModel;
use App\Models\OrdenesModel;
use App\Models\OrdenesPorgramacionModel;
use App\Models\OrdenSlaModel;
use App\Models\provedoresContratosModel;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;

class OrdenesProveedores extends Component
{
    // --- PROPIEDADES PARA CLIENTE, ACTIVOS Y CATEGORÍA ---
    public $empresas, $servicios, $empresa, $actividadEconomica, $misServicios, $tecnicos;
    public $activos, $activoBusqueda, $id_activo, $searchActivo, $filteredActivos, $categoriasActivos, $subcategoriasActivos;
    public $id_cliente, $filteredEmpresas, $empresaBusqueda, $searchEmpresa, $tiposServicio, $orden, $cuit, $contrato, $conContrato, $id_contrato, $filteredContratos, $contratosBienes, $contratosUbicaciones;
    public $diasSeleccionados = [];
    public $imagenesTrabajo = [];
    public $newImages = [];
    public $searchContrato = '';
    public $contratoSeleccionado = null;
    public array $seleccionActivos = [];          // ids marcados desde los checkboxes
    public $activosSeleccionados;

    // Para Categoría
    public $categorias, $searchCategoria, $filteredCategorias, $id_categoria, $categoriaBusqueda, $contratosEmpresa;

    // --- PROPIEDADES PARA SERVICIOS DE LA EMPRESA ---
    public $serviciosEmpresa, $searchServicio, $filteredServiciosEmpresa, $id_servicioEmpresa, $servicioEmpresaBusqueda, $contratosActivos;

    public $selectedTipoServicio; // 1 = Correctivo/Reparación, 2 = Preventivo

    public bool $tieneContrato = false;

    // Otras propiedades
    public $descripcion;
    public $id_servicio;

    // SLA para Preventivo
    public $sla_4hs = false;
    public $sla_8hs = false;
    public $sla_24hs = false;
    public $sla_12hs = false;

    // SLA para Correctivo
    public $slaTipo;
    public $fechaProgramada;
    public $periodicidad;
    public $fechaInicio;
    public $fechaFin;
    use WithFileUploads;

    public function mount()
    {
        $this->empresas();
        $this->filteredEmpresas = $this->empresas ?? collect();

        $this->categorias        = CategoriaModel::all() ?? collect();
        $this->filteredCategorias = $this->categorias;

        $this->contratosEmpresa  = collect();
        $this->filteredContratos = collect();

        $this->seleccionActivos = [];
        $this->activosSeleccionados = collect();

        $this->activos           = collect();
        $this->filteredActivos   = collect();
    }

    public function updatedSearchEmpresa($value)
    {
        $this->updatedSearchEmpresaBusqueda($value); // Llama a cargar las ubicaciones filtradas
    }

    public function setCuitEmpresa($cuit)
    {
        $this->limpiarSeleccion();
        $this->cuit = $cuit;
        // contratos de ESA prestadora con MI empresa
        $this->contratosEmpresa = ContratoModel::where('prestadora', $cuit)
            ->where('cuit_cliente', IdHelper::idEmpresa())
            ->orderBy('fecha_inicio', 'desc')
            ->get();

        $this->tieneContrato = $this->contratosEmpresa->isNotEmpty();

        // restricciones (unión de todos los contratos de la prestadora)
        if ($this->tieneContrato) {
            $ids = $this->contratosEmpresa->pluck('id_contrato');
            $this->contratosActivos     = ContratoBienesModel::whereIn('id_contrato', $ids)->get();
            $this->contratosUbicaciones = ContratoUbicacionesModel::whereIn('id_contrato', $ids)->get();
        } else {
            $this->contratosActivos     = collect();
            $this->contratosUbicaciones = collect();
        }

        // dropdown contratos
        $this->filteredContratos = $this->contratosEmpresa->values();
        $this->id_contrato = null;
        $this->contratoSeleccionado = null;
        $this->searchContrato = '';

        // resto
        $this->empresaBusqueda  = EmpresasModel::where('cuit', $cuit)->first();
        $this->searchEmpresa    = '';
        $this->filteredEmpresas = $this->empresas;

        // recalcular listas
        $this->filterCategorias();
        $this->id_categoria ? $this->filterActivos() : $this->activos = $this->filteredActivos = collect();

        $this->dispatch('closeDropdown', ['dropdown' => 'empresa']);
    }

    public function updatedSearchActivo($value)
    {
        if (empty($value)) {
            $this->filteredActivos = $this->activos;
            return;
        }
        $this->filteredActivos = $this->activos->filter(
            fn($a) =>
            stripos($a->nombre, $value) !== false
        );
    }

    public function updatedSeleccionActivos($value): void
    {
        $this->refreshActivosSeleccionados();
    }

    private function refreshActivosSeleccionados(): void
    {
        $ids = collect($this->seleccionActivos)
            ->map(fn($v) => (int)$v)    // por si vienen como string
            ->unique()
            ->values()
            ->all();

        $this->activosSeleccionados = empty($ids)
            ? collect()
            : ActivosModel::query()
            ->whereIn('id_activo', $ids)
            ->with(['ubicacion', 'estadoGeneral', 'asignaciones']) // los que uses
            ->get()
            ->keyBy('id_activo');
    }

    public function toggleActivo(int $id): void
    {
        if (in_array($id, $this->seleccionActivos, true)) {
            $this->seleccionActivos = array_values(array_filter(
                $this->seleccionActivos,
                fn($x) => (int)$x !== $id
            ));
        } else {
            $this->seleccionActivos[] = $id;
        }
        $this->refreshActivosSeleccionados();
    }

    public function limpiarSeleccion(): void
    {
        $this->seleccionActivos = [];
        $this->refreshActivosSeleccionados();
    }

    public function getHayActivosSeleccionadosProperty(): bool
    {
        return is_array($this->seleccionActivos) && count($this->seleccionActivos) > 0;
    }

    public function getCategoriasSeleccionadasProperty()
    {
        // $activosSeleccionados está keyBy('id_activo'); pluck sobre valores:
        return collect($this->activosSeleccionados ?? [])
            ->pluck('id_categoria')
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function seleccionarTodos(): void
    {
        $src = ($this->filteredActivos ?? collect());
        $this->seleccionActivos = $src->pluck('id_activo')->unique()->values()->all();
        $this->refreshActivosSeleccionados();
    }

    public function removeActivo(int $id): void
    {
        $this->seleccionActivos = array_values(array_filter(
            $this->seleccionActivos,
            fn($x) => (int)$x !== (int)$id
        ));
        $this->refreshActivosSeleccionados();
    }
    // filtra por texto el listado de contratos mostrado en el dropdown

    public function updatedSearchContrato($value)
    {
        $base = $this->contratosEmpresa ?? collect();

        if (!strlen($value)) {
            $this->filteredContratos = $base->values();
            return;
        }

        $v = mb_strtolower($value);
        $this->filteredContratos = $base->filter(function ($c) use ($v) {
            return str_contains(mb_strtolower((string) $c->nro_contrato), $v)
                || str_contains(mb_strtolower((string) $c->nombre), $v);
        })->values();
    }
    // setea el contrato elegido
    public function setContrato($id)
    {
        $this->limpiarSeleccion();
        $this->id_contrato = $id;
        $this->contratoSeleccionado = ($this->contratosEmpresa ?? collect())
            ->firstWhere('id_contrato', $id);

        $this->dispatch('closeDropdown', ['dropdown' => 'contrato']);
    }

    private function updatedSearchEmpresaBusqueda($value)
    {
        $this->filteredEmpresas = $this->empresas->filter(function ($empresa) use ($value) {
            return stripos($empresa->razon_social, $value) !== false;
        });
    }

    private function empresas()
    {
        $cuitActual = IdHelper::idEmpresa();

        $prestadorasCuits = ContratoModel::where('cuit_cliente', $cuitActual)
            ->pluck('prestadora')
            ->filter()
            ->unique()
            ->values();

        $this->contrato = ContratoModel::where('cuit_cliente', $cuitActual)->get();

        $this->empresas = MisProveedoresModel::query()
            ->where('empresa', $cuitActual)
            ->where(function ($q) use ($prestadorasCuits) {
                $q->whereIn('cuit', $prestadorasCuits)
                    ->orWhere('ordenes_sin_contrato', 1);
            })
            ->orderBy('razon_social')
            ->get(['id', 'cuit', 'razon_social', 'ordenes_sin_contrato'])
            ->unique('cuit')
            ->values();

        $this->filteredEmpresas = $this->empresas;
    }

    // Filtrar las categorías en base a los activos de mi empresa
    private function filterCategorias()
    {
        $empresaId = IdHelper::idEmpresa();

        $q = ActivosModel::query()
            ->where('empresa_titular', $empresaId);

        if ($this->tieneContrato) {
            $tipos         = $this->contratosActivos->pluck('id_tipo')->filter()->unique()->values()->all();
            $categorias    = $this->contratosActivos->pluck('id_categoria')->filter()->unique()->values()->all();
            $subcategorias = $this->contratosActivos->pluck('id_subcategoria')->filter()->unique()->values()->all();

            if (!empty($tipos)) {
                $q->whereIn('id_tipo', $tipos);
            }
            if (!empty($categorias)) {
                $q->whereIn('id_categoria', $categorias);
            }
            if (!empty($subcategorias)) {
                $q->whereIn('id_subcategoria', $subcategorias);
            }
        }

        $categoriasIds = $q->pluck('id_categoria')->unique()->values();

        $this->categoriasActivos  = $categoriasIds->isNotEmpty()
            ? CategoriaModel::whereIn('id_categoria', $categoriasIds)->get()
            : collect();

        $this->filteredCategorias = $this->categoriasActivos;
    }
    // Método de filtrado de categorías
    public function updatedSearchCategoria($value)
    {
        // Solo permitir la búsqueda de categorías si hay una empresa seleccionada
        if ($this->empresaBusqueda) {
            $this->filteredCategorias = $this->categoriasActivos->filter(function ($cat) use ($value) {
                return stripos($cat->nombre, $value) !== false;
            });
        }
    }

    public function setIdCategoria($id)
    {
        $this->limpiarSeleccion();
        $this->id_categoria = $id;
        $this->categoriaBusqueda = CategoriaModel::find($id);
        $this->searchCategoria = '';
        $this->filteredCategorias = $this->categoriasActivos;
        // Reiniciar activos seleccionados
        $this->id_activo = null;
        $this->activoBusqueda = null;
        // Reiniciar la búsqueda de activos para mostrar todos los activos relacionados con la nueva categoría
        $this->searchActivo = '';
        $this->filteredActivos = $this->activos;

        $this->dispatch('closeDropdown', ['dropdown' => 'categoria']); // Cerrar el dropdown

        $this->filterActivos();
    }

    private function filterActivos()
    {
        $empresaId = IdHelper::idEmpresa();

        $q = ActivosModel::query()
            ->where('empresa_titular', $empresaId);

        if ($this->id_categoria) {
            $q->where('id_categoria', $this->id_categoria);
        }

        if ($this->tieneContrato) {
            $ubicaciones   = $this->contratosUbicaciones->pluck('id_ubicacion')->filter()->unique()->values()->all();
            $tipos         = $this->contratosActivos->pluck('id_tipo')->filter()->unique()->values()->all();
            $subcategorias = $this->contratosActivos->pluck('id_subcategoria')->filter()->unique()->values()->all();

            if (!empty($ubicaciones)) {
                $q->whereIn('id_ubicacion', $ubicaciones);
            }
            if (!empty($tipos)) {
                $q->whereIn('id_tipo', $tipos);
            }
            if (!empty($subcategorias)) {
                $q->whereIn('id_subcategoria', $subcategorias);
            }
        }

        $this->activos         = $q->with('categoria')->get();
        $this->filteredActivos = $this->activos;
    }

    public function setIdActivo($id)
    {
        $this->id_activo = $id;
        $this->activoBusqueda = ActivosModel::find($id);
        $this->searchActivo = '';
        $this->filteredActivos = $this->activos;

        $this->dispatch('closeDropdown', ['dropdown' => 'activo']);
    }

    // Método para guardar los datos con transacción y validaciones condicionales
    public function save()
    {
        DB::beginTransaction();
        try {
            $this->validate($this->getValidationRules());

            // Creación de la orden de trabajo
            $this->crearOrdenTrabajo();

            $this->crearOrdenesBienes();

            // Lógica para programar la orden según el tipo de servicio
            if ($this->selectedTipoServicio == 'Correctivo/Reparación') {
                $this->OrdenesProgramaciones();
            } elseif ($this->selectedTipoServicio == 'Preventivo') {
                $this->OrdenSla();
            }

            if ($this->imagenesTrabajo) {
                $this->cargarImagenesInfo();
            }

            DB::commit();

            $this->dispatch('ordenTrabajo', [
                'title'   => 'Operación exitosa',
                'message' => 'Orden de trabajo creada correctamente.'
            ]);

            return redirect()->route('ordenes');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('errorInfo', [
                'title'   => 'Error al crear la orden',
                'message' => $e->getMessage()
            ]);
        }
    }

    // Método para obtener las reglas de validación
    private function getValidationRules()
    {
        $rules = [
            'id_categoria'             => ['required'],

            //  multi-selección de activos (min:1)
            'seleccionActivos'         => ['required', 'array', 'min:1'],
            'seleccionActivos.*'       => ['integer'],
            'selectedTipoServicio'     => ['required', 'string', 'in:Correctivo/Reparación,Preventivo'],

            'diasSeleccionados'        => [
                function ($attribute, $value, $fail) {
                    if (
                        $this->slaTipo === 'periodico'
                        && in_array($this->periodicidad, ['semana', '2semanas', 'mes'], true)
                        && (empty($value) || count((array)$value) === 0)
                    ) {
                        $fail('Debe seleccionar al menos un día.');
                    }
                },
            ],
        ];

        if ($this->selectedTipoServicio === 'Preventivo') {
            // ✅ al menos un SLA marcado
            if (!$this->sla_4hs && !$this->sla_8hs && !$this->sla_12hs && !$this->sla_24hs) {
                $this->addError('sla_preventivo', 'Debe seleccionar al menos un SLA para servicio preventivo.');
            }
        } elseif ($this->selectedTipoServicio === 'Correctivo/Reparación') {
            // ✅ descripción obligatoria
            $rules['descripcion'] = ['required', 'string', 'min:5'];

            if ($this->slaTipo === 'programado') {
                $rules['fechaProgramada'] =   ['required', 'date', 'after_or_equal:today'];
            }

            if ($this->slaTipo === 'periodico') {
                $rules['fechaInicio']   = ['required', 'date'];
                $rules['fechaFin']      = ['required', 'date', 'after:fechaInicio'];
                $rules['periodicidad']  = ['required', 'in:diario,semana,2semanas,mes'];
            }
        }

        return $rules;
    }

    public function removeImage($index)
    {
        if (isset($this->imagenesTrabajo[$index])) {
            array_splice($this->imagenesTrabajo, $index, 1);
        }
    }
    public function updatedNewImages()
    {
        if ($this->newImages && count($this->newImages) > 0) {
            $this->imagenesTrabajo = array_merge($this->imagenesTrabajo, $this->newImages);
            $this->reset('newImages');
        }
    }

    private function crearOrdenesBienes()
    {
        $ids = collect($this->seleccionActivos)->map(fn($v) => (int)$v)->unique()->values();

        $activos = ActivosModel::query()
            ->whereIn('id_activo', $ids)
            ->get(['id_activo', 'id_tipo', 'id_categoria', 'id_subcategoria'])
            ->keyBy('id_activo');

        foreach ($ids as $idActivo) {
            $a = $activos[$idActivo] ?? null;
            if (!$a) continue;

            ordenesBienesModel::create([
                'id_orden'      => $this->orden->id_orden,
                'id_activo'     => $a->id_activo,
                'id_subcategoria' => $a->id_subcategoria,
                'id_categoria'  => $a->id_categoria,
                'id_tipo'       => $a->id_tipo,
            ]);
        }
    }

    // Método para crear la orden de trabajo (guarda solo la fecha sin horas)
    private function crearOrdenTrabajo()
    {
        return $this->orden = OrdenesModel::create([
            'proveedor'               => $this->cuit,
            'estado_vigencia'         => 'Activo',
            'comentarios'             => $this->descripcion,
            'representante_tecnico'   => null,
            'id_relacion_usuario'     => $this->tecnicoBusqueda->id_relacion ?? null,
            'tipo_orden'              => $this->selectedTipoServicio,
            'estado_orden'            => 'Pendiente',
            'fecha'                   => now()->toDateString(),  // Guarda solo la fecha (YYYY-MM-DD)
            'id_usuario'              => auth()->user()->id,
            'cuit_Cliente'            => IdHelper::idEmpresa(),
        ]);
    }

    private function cargarImagenesInfo()
    {
        foreach ($this->imagenesTrabajo as $foto) {
            $path = $foto->store('StorageMvp/fotos_Ordenes', 's3');
            OrdenesAdjuntoModel::create([
                'id_orden'       => $this->orden->id_orden,
                'nombre_archivo' => $foto->getClientOriginalName(),
                'ruta_archivo'   => $path,
                'fecha_subida'   => now(),
                'tipo'           => 'Info',       // opcional: para distinguir tipos
            ]);
        }
    }
    // Método para la programación en órdenes correctivas (guarda fechas sin hora)
    private function OrdenesProgramaciones()
    {
        return OrdenesPorgramacionModel::create([
            'id_orden'     => $this->orden->id_orden,
            'fecha_inicio' => $this->fechaInicio ? date('Y-m-d', strtotime($this->fechaInicio)) : ($this->fechaProgramada ? date('Y-m-d', strtotime($this->fechaProgramada)) : null),
            'fecha_fin'    => $this->fechaFin ? date('Y-m-d', strtotime($this->fechaFin)) : null,
            'periodicidad' => $this->periodicidad,
            'fechas_periodicidad' => in_array($this->periodicidad, ['semana', '2semanas', 'mes']) && !empty($this->diasSeleccionados)
                ? implode(',', $this->diasSeleccionados)
                : null,
        ]);
    }

    // Método para crear el SLA en órdenes preventivas
    private function OrdenSla()
    {
        OrdenSlaModel::create([
            'id_orden' =>  $this->orden->id_orden,
            'sla_horas' => $this->sla_4hs
                ? 4
                : ($this->sla_8hs ? 8 : ($this->sla_12hs ? 12 : 24)),
        ]);
    }

    public function selectSLA($selected)
    {
        // Desmarcar todos los checkboxes
        $this->sla_4hs = false;
        $this->sla_8hs = false;
        $this->sla_24hs = false;
        $this->sla_12hs = false;

        // Marcar el seleccionado
        if ($selected === 'sla_4hs') {
            $this->sla_4hs = true;
        }
        if ($selected === 'sla_8hs') {
            $this->sla_8hs = true;
        }
        if ($selected === 'sla_24hs') {
            $this->sla_24hs = true;
        }
        if ($selected === 'sla_12hs') {
            $this->sla_12hs = true;
        }
    }

    public function render()
    {
        return view('livewire.servicios.OrdenesProveedores.ordenes-proveedores');
    }
}
