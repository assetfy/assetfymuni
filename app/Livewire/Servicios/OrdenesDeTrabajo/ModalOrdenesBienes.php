<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;

use App\Helpers\IdHelper;
use App\Models\ActivosModel;
use App\Models\ContratoModel;
use App\Models\ContratoBienesModel;
use App\Models\ContratoUbicacionesModel;
use App\Models\MisProveedoresModel;
use App\Models\OrdenesAdjuntoModel;
use App\Models\ordenesBienesModel;
use App\Models\OrdenesModel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithFileUploads;

class ModalOrdenesBienes extends Component
{
    use WithFileUploads;

    protected $listeners = ['openSoliitarOrden'];

    // Modal
    public bool $open = false;

    // Contexto
    public int $id_activo;
    public $activo;
    public $empresaActual;

    // Selecciones
    public ?string $proveedorCuit = null;
    public ?int $id_contrato = null;

    // Listas
    public $proveedores = [];   // array de arrays: [['cuit'=>..., 'razon_social'=>..., 'ordenes_sin_contrato'=>...], ...]
    public $contratos   = [];   // array de stdClass/arrays (desde Collection->all())

    // Tipo de servicio + SLA / descripción
    public ?string $selectedTipoServicio = null; // "Correctivo/Reparación" | "Preventivo"
    public bool $sla_4hs = false;
    public bool $sla_8hs = false;
    public bool $sla_12hs = false;
    public bool $sla_24hs = false;

    public ?string $slaTipo = null;        // "programado" | "periodico"
    public ?string $fechaProgramada = null;
    public ?string $periodicidad = null;   // "diario"|"semana"|"2semanas"|"mes"
    public ?string $fechaInicio = null;
    public ?string $fechaFin = null;
    public array $diasSeleccionados = [];

    public ?string $descripcion = null;

    // Adjuntos
    public array $newImages = [];
    public array $imagenesTrabajo = [];

    // Orden creada
    public $orden;

    public function openSoliitarOrden($data)
    {
        // Reset de estado (pero mantené $open al final)
        $this->reset([
            'proveedorCuit',
            'id_contrato',
            'contratos',
            'selectedTipoServicio',
            'sla_4hs',
            'sla_8hs',
            'sla_12hs',
            'sla_24hs',
            'slaTipo',
            'fechaProgramada',
            'periodicidad',
            'fechaInicio',
            'fechaFin',
            'diasSeleccionados',
            'descripcion',
            'newImages',
            'imagenesTrabajo',
            'orden'
        ]);

        $this->empresaActual = IdHelper::idEmpresa();
        $this->id_activo = $data;

        $this->activo = ActivosModel::query()
            ->with(['ubicacion'])
            ->findOrFail($this->id_activo);

        // Prestadoras con contrato con mi empresa
        $prestadorasCuits = ContratoModel::where('cuit_cliente', $this->empresaActual)
            ->pluck('prestadora')
            ->filter()
            ->unique()
            ->values();

        // Mis proveedores (con contrato o que permiten sin contrato)
        $misProv = MisProveedoresModel::query()
            ->where('empresa', $this->empresaActual)
            ->where(function ($q) use ($prestadorasCuits) {
                $q->whereIn('cuit', $prestadorasCuits)
                    ->orWhere('ordenes_sin_contrato', 1);
            })
            ->orderBy('razon_social')
            ->get(['cuit', 'razon_social', 'ordenes_sin_contrato'])
            ->unique('cuit')
            ->values();

        // Contratos que aplican a ESTE activo (para cualquier prestadora)
        $aplican = $this->contratosQueAplicanAActivo(); // Collection
        $cuitsConContratoAplicable = $aplican->pluck('prestadora')->unique()->values()->all();

        // Proveedores finales: tienen contrato aplicable o permiten sin contrato
        $this->proveedores = $misProv->filter(function ($p) use ($cuitsConContratoAplicable) {
            return in_array($p->cuit, $cuitsConContratoAplicable, true)
                || (int)$p->ordenes_sin_contrato === 1;
        })->values()->toArray();

        // Lista de contratos vacía (array)
        $this->contratos = [];

        // Abrir modal
        $this->open = true;
    }

    private function contratosQueAplicanAActivo(?string $prestadoraCuit = null)
    {
        $q = ContratoModel::query()
            ->where('cuit_cliente', $this->empresaActual);

        if ($prestadoraCuit) {
            $q->where('prestadora', $prestadoraCuit);
        }

        $contratos = $q->get();
        if ($contratos->isEmpty()) {
            return collect();
        }

        $ids = $contratos->pluck('id_contrato')->all();

        $restrBienes = ContratoBienesModel::whereIn('id_contrato', $ids)->get();
        $restrUbic   = ContratoUbicacionesModel::whereIn('id_contrato', $ids)->get();

        $aTipo      = $this->activo->id_tipo ?? null;
        $aCat       = $this->activo->id_categoria ?? null;
        $aSubcat    = $this->activo->id_subcategoria ?? null;
        $aUbicacion = $this->activo->id_ubicacion ?? ($this->activo->ubicacion->id_ubicacion ?? null);

        $aplican = $contratos->filter(function ($c) use ($restrBienes, $restrUbic, $aTipo, $aCat, $aSubcat, $aUbicacion) {
            $b = $restrBienes->where('id_contrato', $c->id_contrato);
            $u = $restrUbic->where('id_contrato', $c->id_contrato);

            $aplicaBien = $b->isEmpty() ? true : $b->contains(function ($rb) use ($aTipo, $aCat, $aSubcat) {
                $okTipo   = is_null($rb->id_tipo)         || $rb->id_tipo == $aTipo;
                $okCat    = is_null($rb->id_categoria)    || $rb->id_categoria == $aCat;
                $okSubcat = is_null($rb->id_subcategoria) || $rb->id_subcategoria == $aSubcat;
                return $okTipo && $okCat && $okSubcat;
            });

            $aplicaUbic = $u->isEmpty() ? true : $u->contains('id_ubicacion', $aUbicacion);

            return $aplicaBien && $aplicaUbic;
        });

        return $aplican->values();
    }

    public function updatedProveedorCuit($cuit): void
    {
        $this->id_contrato = null;

        if (!$cuit) {
            $this->contratos = []; // array vacío
            return;
        }

        // Armar contratos aplicables para ese proveedor => guardar como array
        $lista = $this->contratosQueAplicanAActivo($cuit)
            ->sortByDesc('fecha_inicio')
            ->values();

        $this->contratos = $lista->all(); // <- *** array ***

        // Validación: si no hay contratos y el proveedor NO permite sin contrato => error
        $prov = collect($this->proveedores)->firstWhere('cuit', $cuit);
        $permiteSinContrato = (int)($prov['ordenes_sin_contrato'] ?? 0) === 1;

        if (count($this->contratos) === 0 && !$permiteSinContrato) {
            $this->addError('id_contrato', 'Este proveedor requiere contrato aplicable para este activo.');
        } else {
            $this->resetErrorBag('id_contrato');
        }
    }

    public function updatedNewImages(): void
    {
        if (!empty($this->newImages)) {
            $this->imagenesTrabajo = array_merge($this->imagenesTrabajo, $this->newImages);
            $this->reset('newImages');
        }
    }

    public function removeImage(int $index): void
    {
        if (isset($this->imagenesTrabajo[$index])) {
            array_splice($this->imagenesTrabajo, $index, 1);
        }
    }

    private function rules(): array
    {
        $rules = [
            'proveedorCuit'        => ['required', 'string'],
            'id_contrato'          => ['nullable', 'integer'],
            'selectedTipoServicio' => ['required', 'in:Correctivo/Reparación,Preventivo'],
            'imagenesTrabajo'      => ['array'],
            'imagenesTrabajo.*'    => ['file', 'max:10240'],
        ];

        if ($this->selectedTipoServicio === 'Preventivo') {
            if (!($this->sla_4hs || $this->sla_8hs || $this->sla_12hs || $this->sla_24hs)) {
                $this->addError('sla_preventivo', 'Debe seleccionar al menos un SLA.');
            }
        }

        if ($this->selectedTipoServicio === 'Correctivo/Reparación') {
            $rules['descripcion'] = ['required', 'string', 'min:5'];

            if ($this->slaTipo === 'programado') {
                $rules['fechaProgramada'] = ['required', 'date', 'after_or_equal:today'];
            }

            if ($this->slaTipo === 'periodico') {
                $rules['fechaInicio']  = ['required', 'date', 'after_or_equal:today'];
                $rules['fechaFin']     = ['required', 'date', 'after:fechaInicio'];
                $rules['periodicidad'] = ['required', 'in:diario,semana,2semanas,mes'];
            }
        }

        return $rules;
    }

    public function save()
    {
        $this->validate($this->rules());

        // Regla opcional: si el proveedor NO permite sin contrato, exigir contrato
        $prov = collect($this->proveedores)->firstWhere('cuit', $this->proveedorCuit);
        $permiteSinContrato = (int)($prov['ordenes_sin_contrato'] ?? 0) === 1;

        if (!$permiteSinContrato && empty($this->id_contrato)) {
            $this->addError('id_contrato', 'Seleccioná un contrato para este proveedor.');
            return;
        }

        DB::beginTransaction();
        try {
            $this->orden = OrdenesModel::create([
                'proveedor'             => $this->proveedorCuit,
                'estado_vigencia'       => 'Activo',
                'comentarios'           => $this->descripcion,
                'representante_tecnico' => null,
                'id_relacion_usuario'   => null,
                'tipo_orden'            => $this->selectedTipoServicio,
                'estado_orden'          => 'Pendiente',
                'fecha'                 => now()->toDateString(),
                'id_usuario'            => auth()->id(),
                'cuit_Cliente'          => $this->empresaActual,
                'id_contrato'           => $this->id_contrato,
            ]);

            ordenesBienesModel::create([
                'id_orden'        => $this->orden->id_orden,
                'id_activo'       => $this->id_activo,
                'id_tipo'         => $this->activo->id_tipo,
                'id_categoria'    => $this->activo->id_categoria,
                'id_subcategoria' => $this->activo->id_subcategoria,
            ]);

            if ($this->selectedTipoServicio === 'Preventivo') {
                $horas = $this->sla_4hs ? 4 : ($this->sla_8hs ? 8 : ($this->sla_12hs ? 12 : 24));
                \App\Models\OrdenSlaModel::create([
                    'id_orden'  => $this->orden->id_orden,
                    'sla_horas' => $horas,
                ]);
            } else {
                \App\Models\OrdenesPorgramacionModel::create([
                    'id_orden'            => $this->orden->id_orden,
                    'fecha_inicio'        => $this->fechaInicio ? date('Y-m-d', strtotime($this->fechaInicio)) : ($this->fechaProgramada ? date('Y-m-d', strtotime($this->fechaProgramada)) : null),
                    'fecha_fin'           => $this->fechaFin ? date('Y-m-d', strtotime($this->fechaFin)) : null,
                    'periodicidad'        => $this->periodicidad,
                    'fechas_periodicidad' => in_array($this->periodicidad, ['semana', '2semanas', 'mes'], true) && !empty($this->diasSeleccionados)
                        ? implode(',', $this->diasSeleccionados)
                        : null,
                ]);
            }

            foreach ($this->imagenesTrabajo as $file) {
                $path = $file->store('StorageMvp/fotos_Ordenes', 's3');
                OrdenesAdjuntoModel::create([
                    'id_orden'       => $this->orden->id_orden,
                    'nombre_archivo' => $file->getClientOriginalName(),
                    'ruta_archivo'   => $path,
                    'fecha_subida'   => now(),
                    'tipo'           => 'Info',
                ]);
            }

            DB::commit();

            $this->dispatch('ordenTrabajo', [
                'title'   => 'Orden creada',
                'message' => 'La orden fue generada correctamente.',
            ]);

            $this->open = false;
        } catch (\Throwable $e) {
            DB::rollBack();

            $this->dispatch('errorInfo', [
                'title'   => 'Error al crear la orden',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.servicios.ordenes-de-trabajo.modal-ordenes-bienes');
    }
}
