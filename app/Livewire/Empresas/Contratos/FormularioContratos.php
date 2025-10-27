<?php

namespace App\Livewire\Empresas\Contratos;

use App\Helpers\IdHelper;
use App\Models\ActivosModel;
use App\Models\CategoriaModel;
use App\Models\ContratoBienesModel;
use App\Models\ContratoEstadoModel;
use App\Models\ContratoModel;
use App\Models\ContratoUbicacionesModel;
use App\Models\EmpresasModel;
use App\Models\MaterialesModel;
use App\Models\MisProveedoresModel;
use App\Models\ServiciosActividadesEconomicasModel;
use Livewire\WithFileUploads;
use App\Models\ContratoServicioMaterialModel;
use App\Models\ContratoServicioModel;
use App\Models\ContratosTiposModel;
use App\Models\ServiciosMaterialesModel;
use App\Models\ServiciosModel;
use App\Models\SubcategoriaModel;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class FormularioContratos extends Component
{
    use WithFileUploads;
    public $proveedores, $EmpresaActual, $servicios = [], $tipoContratos, $activos, $tiposActivos, $ubicaciones, $estadosContratos, $materialesDisponibles;
    public $cuitPrestador = '', $estadoContrato = '', $contratos, $TipoBien, $activosContrato;
    public $activeTab = 'principal'; // 'principal' | 'servicios'
    public $rutaArchivo;
    public $serviciosMateriales = [];           // [id_servicio => [id_material, ...]]
    public $indiceMateriales = [];              // [id_material => material (array)]
    public $materialesVisiblesPorServicio = [];
    public $categorias = [];              // listado de categorías (para el select)
    public $subcategorias;           // listado de subcategorías filtradas por categorías seleccionadas
    public $categoriasSel = [];           // seleccionadas (múltiple)
    public $subcategoriasSel = [];        // seleccionadas (múltiple)

    public $form = [
        'nombre'            => null,
        'id_tipo_contrato'  => null,
        'nro_contrato'      => null,
        'fecha_inicio'      => null,
        'fecha_fin'         => null,
        'monto'             => null,
        'moneda'            => null,
        'ubicaciones'       => [],
        'TipoBien'          => null,
        'servicios'         => [],
        'materiales'        => [],
    ];

    protected function rules()
    {
        return [
            'form.nombre'           => ['required', 'string', 'min:3', 'max:200'],
            'form.id_tipo_contrato' => ['required', 'integer'],
            'estadoContrato'        => ['required', 'integer'],
            'form.nro_contrato'     => ['required', 'string', 'max:50'],
            'cuitPrestador'         => ['required', 'regex:/^\d{11}$/'], // CUIT 11 dígitos
            'form.fecha_inicio'     => ['required', 'date'],
            'form.fecha_fin'        => ['required', 'date', 'after:form.fecha_inicio'],
            'form.monto'            => ['nullable', 'numeric', 'min:0'],
            'form.moneda'           => ['nullable', 'in:ARS,USD,EUR'],
            'form.ubicaciones' => ['required', 'array', 'min:1', 'max:1'],
            'TipoBien'              => ['required', 'integer'],
            'form.servicios'        => ['sometimes', 'array'],
            'rutaArchivo'           => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }

    protected $messages = [
        'form.nombre.required'         => 'El nombre es obligatorio.',
        'form.tipos_contrato.required' => 'Seleccione al menos un tipo de solicitud.',
        'estadoContrato.required'      => 'Seleccione el estado del contrato.',
        'form.nro_contrato.required'   => 'Indique el número del contrato.',
        'Cuit_prestador.required'      => 'Seleccione un prestador.',
        'form.fecha_fin.after'         => 'La fecha de fin debe ser posterior a la de inicio.',
        'form.ubicaciones.required'    => 'Seleccione al menos una ubicación.',
        'TipoBien.required'      => 'Seleccione el tipo de bien.',
    ];

    public function mount()
    {
        $this->EmpresaActual = IdHelper::empresaActual();

        $this->proveedores = MisProveedoresModel::query()
            ->where('empresa', $this->EmpresaActual->cuit)
            ->where('existe_en_la_plataforma', 'Si')
            ->get()
            ->unique('cuit')
            ->values();

        $this->activos = ActivosModel::where('empresa_titular', $this->EmpresaActual->cuit)
            ->with(['tipo', 'ubicacion'])
            ->get() ?? collect();

        $this->tipoContratos     = ContratosTiposModel::all() ?? collect();
        $this->estadosContratos  = ContratoEstadoModel::all() ?? collect();

        if (empty($this->form['nro_contrato'])) {
            $this->form['nro_contrato'] = $this->GeneralNroContrato();
        }

        $this->tiposActivos = $this->activos->pluck('tipo')->filter()->unique('id_tipo')->values();
        $this->ubicaciones  = $this->activos->pluck('ubicacion')->filter()->unique('id_ubicacion')->values();
    }

    public function goTab($tab)
    {
        $this->activeTab = in_array($tab, ['principal', 'servicios']) ? $tab : 'principal';
    }

    public function updatedCuitPrestador($value)
    {
        $this->form['servicios']  = [];
        $this->form['materiales'] = [];
        $this->materialesVisiblesPorServicio = [];
        $this->serviciosMateriales = [];
        $this->indiceMateriales = [];

        $actividades = EmpresasModel::where('cuit', $value)->pluck('COD_ACTIVIDAD');

        if ($actividades != null && $actividades->count()) {
            // 1) Servicios habilitados
            $id_servicios = ServiciosActividadesEconomicasModel::whereIn('cod_actividad', $actividades)
                ->pluck('id_servicio');

            $this->servicios = ServiciosModel::whereIn('id_servicio',  $id_servicios)
                ->orderBy('nombre')
                ->get();

            $rels = ServiciosMaterialesModel::whereIn('id_servicio', $id_servicios)
                ->get(['id_servicio', 'id_material']);

            foreach ($rels as $r) {
                $this->serviciosMateriales[$r->id_servicio][] = $r->id_material;
            }

            $serviciosMateriales = $rels->pluck('id_material')->unique()->values();
            $this->materialesDisponibles = MaterialesModel::whereIn('id_material', $serviciosMateriales)
                ->where('estado', 'activo')
                ->get();

            $this->indiceMateriales = $this->materialesDisponibles->keyBy('id_material')->toArray();
        }
    }

    public function onServiciosChange(): void
    {
        $seleccionados = collect($this->form['servicios'] ?? []);
        $out = [];
        foreach ($seleccionados as $sid) {
            $idsMat = $this->serviciosMateriales[$sid] ?? [];
            $lista = [];

            foreach ($idsMat as $mid) {
                if (isset($this->indiceMateriales[$mid])) {
                    $m = $this->indiceMateriales[$mid];
                    $lista[] = [
                        'id_material'     => $m['id_material'],
                        'codigo_interno'  => $m['codigo_interno'] ?? null,
                        'nombre'          => $m['nombre'] ?? null,
                        'unidad'          => $m['unidad'] ?? null,
                        'descripcion'     => $m['descripcion'] ?? null,
                    ];
                }
            }

            $out[$sid] = $lista;
        }

        $this->materialesVisiblesPorServicio = $out;

        $this->form['materiales'] = collect($this->form['materiales'] ?? [])
            ->only($seleccionados)
            ->toArray();
    }

    protected function validateSelectedMaterialsQuantities(): bool
    {
        $ok = true;
        foreach ($this->form['materiales'] as $sid => $materials) {
            foreach ($materials as $mid => $data) {
                $selected = (bool)($data['selected'] ?? false);
                if ($selected) {
                    $qty = $data['cantidad'] ?? null;
                    if (!is_numeric($qty) || $qty <= 0) {
                        $this->addError("form.materiales.$sid.$mid.cantidad", 'La cantidad debe ser mayor a 0.');
                        $ok = false;
                    }
                }
            }
        }
        return $ok;
    }

    public function enviar()
    {
        try {
            $this->validate();
        } catch (ValidationException $e) {
            logger()->error('Validación fallida', [
                'failed' => $e->validator->failed(),
                'errors' => $e->validator->errors()->toArray(),
            ]);

            $this->dispatch('errorInfo', [
                'title' => 'Errores de validación',
                'message' => implode("\n", $e->validator->errors()->all()),
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            $this->generalContratos();
            $this->contratoBienes();
            $this->contratosUbicaciones();
            $this->contratoServicios();
            DB::commit();

            $this->dispatch('lucky');
            $this->resetFormulario();
            return redirect()->route('contratos-empresas');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('errorInfo', ['title' => 'Error', 'message' => $e->getMessage()]);
        }
    }

    public function setUbicacionUnica(int $id): void
    {
        $this->form['ubicaciones'] = [$id];
    }

    public function updatedFormUbicaciones($value): void
    {
        if (is_array($value) && count($value) > 1) {
            // Tomo el último elegido como el definitivo
            $last = end($value);
            $this->form['ubicaciones'] = [$last];
        }
    }

    private function recalcularActivos(): void
    {
        $cuit = (string) $this->EmpresaActual->cuit;

        $query = ActivosModel::query()
            ->where('empresa_titular', $cuit);

        if (!empty($this->TipoBien)) {
            $query->where('id_tipo', (int) $this->TipoBien);
        }

        if (!empty($this->categoriasSel)) {
            $query->whereIn('id_categoria', array_map('intval', (array) $this->categoriasSel));
        }

        if (!empty($this->subcategoriasSel)) {
            $query->whereIn('id_subcategoria', array_map('intval', (array) $this->subcategoriasSel));
        }

        $this->activosContrato = $query->get();
    }

    private function cargarCategoriasDisponiblesParaTipo(int $idTipo): array
    {
        $cuit = (string) $this->EmpresaActual->cuit;

        $idsCategorias = ActivosModel::where('empresa_titular', $cuit)
            ->where('id_tipo', $idTipo)
            ->whereNotNull('id_categoria')
            ->distinct()
            ->pluck('id_categoria')
            ->all();

        return CategoriaModel::whereIn('id_categoria', $idsCategorias)
            ->orderBy('nombre')
            ->get(['id_categoria', 'nombre'])
            ->map(fn($c) => [
                'id_categoria' => (int) $c->id_categoria,
                'nombre'       => (string) $c->nombre,
            ])
            ->all();
    }

    private function cargarSubcategoriasDisponiblesParaCategorias(int $idTipo, array $idsCategorias): array
    {
        $cuit = (string) $this->EmpresaActual->cuit;

        $idsSubcategorias = ActivosModel::where('empresa_titular', $cuit)
            ->where('id_tipo', $idTipo)
            ->whereIn('id_categoria', $idsCategorias)
            ->whereNotNull('id_subcategoria')
            ->distinct()
            ->pluck('id_subcategoria')
            ->all();

        return SubcategoriaModel::whereIn('id_subcategoria', $idsSubcategorias)
            ->orderBy('nombre')
            ->get(['id_subcategoria', 'nombre'])
            ->map(fn($s) => [
                'id_subcategoria' => (int) $s->id_subcategoria,
                'nombre'          => (string) $s->nombre,
            ])
            ->all();
    }

    public function updatedTipoBien($value)
    {
        $this->TipoBien = (int) $value;

        // Reset dependientes
        $this->categoriasSel    = [];
        $this->subcategoriasSel = [];
        $this->subcategorias    = [];
        $this->activosContrato  = collect();

        if (empty($this->TipoBien)) {
            $this->categorias = [];
            return;
        }

        // Cargar categorías válidas para este tipo (solo las que tienen activos)
        $this->categorias = $this->cargarCategoriasDisponiblesParaTipo($this->TipoBien);

        $this->recalcularActivos();
    }

    public function updatedCategoriasSel(): void
    {
        // Normalizar selección de categorías
        $this->categoriasSel = collect($this->categoriasSel)
            ->filter()
            ->map(fn($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

        if (empty($this->categoriasSel)) {
            $this->subcategorias    = [];
            $this->subcategoriasSel = [];
            $this->recalcularActivos();
            return;
        }

        $this->subcategorias = $this->cargarSubcategoriasDisponiblesParaCategorias(
            (int) $this->TipoBien,
            $this->categoriasSel
        );

        $idsSubcategoriasValidas = array_column($this->subcategorias, 'id_subcategoria');
        $this->subcategoriasSel = array_values(array_intersect(
            array_map('intval', (array) $this->subcategoriasSel),
            $idsSubcategoriasValidas
        ));

        $this->recalcularActivos();
    }

    public function updatedSubcategoriasSel(): void
    {
        $this->subcategoriasSel = collect($this->subcategoriasSel)
            ->filter()
            ->map(fn($v) => (int) $v)
            ->unique()
            ->values()
            ->all();
        $this->recalcularActivos();
    }

    private function GeneralNroContrato(): string
    {
        $year   = Carbon::now()->year;
        $prefix = "CTR-{$year}-";

        // Busca el último correlativo del año actual (formato CTR-YYYY-###)
        $last = ContratoModel::query()
            ->where('nro_contrato', 'like', $prefix . '%')
            ->orderByDesc('nro_contrato')
            ->value('nro_contrato');

        $next = 1;
        if ($last) {
            $parts = explode('-', $last);
            $num   = isset($parts[2]) ? (int) $parts[2] : 0;
            $next  = $num + 1;
        }

        return $prefix . str_pad((string) $next, 3, '0', STR_PAD_LEFT);
    }

    private function contratosUbicaciones()
    {
        $ids = collect($this->form['ubicaciones'] ?? [])->filter()->unique()->values();
        foreach ($ids as $idUbi) {
            ContratoUbicacionesModel::create([
                'id_contrato'  => $this->contratos->id_contrato,
                'id_ubicacion' => (int) $idUbi,
            ]);
        }
    }

    private function contratoServicios()
    {
        $serviciosSel = collect($this->form['servicios'] ?? [])->filter()->unique()->values();
        if ($serviciosSel->isEmpty()) {
            return;
        }

        foreach ($serviciosSel as $sid) {

            $cs = ContratoServicioModel::create([
                'id_contrato'     => $this->contratos->id_contrato,
                'id_servicio'     => (int) $sid,
                'precio_unitario' => $this->form['precio_servicios'][$sid] ?? 0,
                'moneda'          => $this->form['moneda'] ?? 'ARS',
                'estado'          => 'Activo',
            ]);

            $mats = data_get($this->form, "materiales.$sid", []);
            foreach ($mats as $mid => $data) {
                $selected = (bool) data_get($data, 'selected', false);
                if (!$selected) continue;

                $cantidad = data_get($data, 'cantidad');
                ContratoServicioMaterialModel::create([
                    'id_contrato_servicio' => $cs->id_contrato_servicio,
                    'id_material'          => (int) $mid,
                    'cantidad'             => (int) $cantidad,
                ]);
            }
        }
    }

    private function contratoBienes()
    {
        if ($this->activosContrato != null) {
            foreach ($this->activosContrato as $a) {
                ContratoBienesModel::create([
                    'id_contrato'   =>  $this->contratos->id_contrato,
                    'id_activo'     => $a->id_activo,
                    'id_tipo'       => $a->id_tipo      ?? null,
                    'id_categoria'  => $a->id_categoria ?? null,
                    'id_subcategoria' => $a->id_subcategoria ?? null,
                ]);
            }
        }
    }

    private function generalContratos()
    {
        if ($this->rutaArchivo) {
            $archivo = $this->rutaArchivo->store('StorageMvp/contratos', 's3');
        }
        if (empty($this->form['nro_contrato'])) {
            $this->form['nro_contrato'] = $this->GeneralNroContrato();
        }

        $this->contratos = ContratoModel::create(
            [
                'nro_contrato'        => $this->form['nro_contrato'],
                'nombre'              => $this->form['nombre'],
                'cuit_cliente'        => $this->EmpresaActual->cuit,  // cliente actual
                'prestadora'          => $this->cuitPrestador,        // proveedor seleccionado
                'id_estado_contrato'  => $this->estadoContrato,       // estado elegido
                'fecha_inicio'        => $this->form['fecha_inicio'],
                'fecha_fin'           => $this->form['fecha_fin'],
                'fecha_creacion'      => Carbon::now(),
                'id_tipo_contrato'   => (int) ($this->form['id_tipo_contrato'] ?? 0),
                'monto'               => $this->form['monto'] ?? 0,
                'moneda'              => $this->form['moneda'] ?? 'ARS',
                'contrato_file'       => $archivo  ?? null,  //
            ]
        );
    }

    private function resetFormulario()
    {
        // Limpia el array del formulario a sus valores por defecto
        $this->form = [
            'nombre'            => null,
            'id_tipo_contrato'  => null,
            'nro_contrato'      => null,
            'fecha_inicio'      => null,
            'fecha_fin'         => null,
            'monto'             => null,
            'moneda'            => null,
            'ubicaciones'       => [],
            'TipoBien'          => null,
            'servicios'         => [],
            'materiales'        => [],
            'categoriasSel'   => [],
        ];
        // Limpia selecciones y estados auxiliares
        $this->categorias = null;
        $this->TipoBien = null;
        $this->cuitPrestador = '';
        $this->estadoContrato = '';
        $this->activosContrato = null;
        $this->servicios = [];
        $this->serviciosMateriales = [];
        $this->indiceMateriales = [];
        $this->materialesVisiblesPorServicio = [];
        $this->categoriasSel = [];           // seleccionadas (múltiple)
        $this->subcategoriasSel = [];

        // Archivo subido
        $this->rutaArchivo = null;

        // Pestaña inicial
        $this->activeTab = 'principal';

        // Errores/validación
        $this->resetErrorBag();
        $this->resetValidation();
    }


    public function render()
    {
        return view('livewire.empresas.contratos.formulario-contratos');
    }
}
