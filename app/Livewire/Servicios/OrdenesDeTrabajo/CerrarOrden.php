<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;

use App\Helpers\IdHelper;
use App\Models\OrdenesModel;
use App\Models\ordenesBienesModel;
use App\Models\OrdenesAdjuntoModel;
use App\Models\OrdenesBienesAdjuntoModel;

use App\Models\UsuariosEmpresasModel;
use App\Models\MaterialesModel;
use App\Models\ServiciosModel;
use App\Models\ServiciosMaterialesModel;
use App\Models\ServiciosActividadesEconomicasModel;
use App\Models\EmpresasActividadesModel;
use App\Models\OrdenesBienesMaterialModel;
use App\Models\OrdenesBienesServicioModel;

use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class CerrarOrden extends Component
{
    use WithFileUploads;

    public int $id_orden;

    public $orden;
    public $empresaActual;

    public ?int $id_relacion_representante = null;
    public ?string $nombre_representante    = null;
    public bool $usuarioEsRepresentante     = false;
    public ?string $fecha_cierre = null;

    public string $modoAplicacion = 'global';
    public bool $accionPasiva = false;

    public array $adjuntosGlobalesNuevos = [];
    public array $adjuntosGlobales = [];

    public array $serviciosDisponibles = [];
    public array $materialesDisponibles = [];
    public array $serviciosMateriales = [];
    public bool $tieneCatalogo = false;

    /* ===== Selección GLOBAL ===== */
    public array $serviciosSeleccionGlobal = [];
    public array $materialesSeleccionGlobal = [];

    public array $bienes = [];
    public array $resumenBien = [];
    public array $adjuntosBienNuevos = [];
    public array $adjuntosBienListado = [];
    public array $materialesSeleccion = [];
    public array $serviciosSeleccion = [];

    // Buscador
    public array $materialesBusqueda = ['query' => '', 'results' => []];
    public bool $modoCrearMaterial = false;
    public array $materialNuevo = [];

    public function mount(int $id_orden): void
    {
        $this->id_orden      = $id_orden;
        $this->empresaActual = IdHelper::idEmpresa();

        $this->cargarOrden();
        $this->detectarRepresentanteTecnicoActual();

        $this->cargarCatalogos();
        $this->cargarBienes();
        $this->cargarAdjuntosGlobales();
        $this->cargarAdjuntosPorBien();

        $this->fecha_cierre = $this->orden->fecha_cierre ?? now()->toDateString();
    }

    private function cargarOrden(): void
    {
        $this->orden = OrdenesModel::query()
            ->where('id_orden', $this->id_orden)
            ->firstOrFail();
    }

    private function detectarRepresentanteTecnicoActual(): void
    {
        $rel = UsuariosEmpresasModel::query()
            ->where('id_usuario', auth()->id())
            ->where('cuit', $this->empresaActual)
            ->first();

        if ($rel && (string)$rel->es_representante_tecnico === 'Si') {
            $this->usuarioEsRepresentante     = true;
            $this->id_relacion_representante  = (int)$rel->id_relacion;
            $this->nombre_representante       = optional($rel->usuarios)->name ?? 'Representante técnico';
        } else {
            $this->usuarioEsRepresentante     = false;
            $this->id_relacion_representante  = null;
            $this->nombre_representante       = null;
        }
    }

    private function cargarBienes(): void
    {
        $bienes = ordenesBienesModel::query()
            ->where('id_orden', $this->id_orden)
            ->with(['activos'])
            ->get();

        $this->bienes = $bienes->map(function ($b) {
            return [
                'id_orden_bien' => $b->id_orden_bien,
                'id_activo'     => $b->id_activo,
                'nombre_activo' => optional($b->activo)->nombre ?: 'N/D',
                'estado'        => $b->estado ?? null,
                'resolucion'    => $b->resolucion ?? null,
            ];
        })->all();

        foreach ($this->bienes as $bien) {
            $id = $bien['id_orden_bien'];
            $this->resumenBien[$id]            = ['estado' => $bien['estado'], 'resolucion' => $bien['resolucion']];
            $this->adjuntosBienNuevos[$id]     = [];
            $this->materialesSeleccion[$id]    = $this->materialesSeleccion[$id] ?? [];
            $this->serviciosSeleccion[$id]     = $this->serviciosSeleccion[$id] ?? [];
        }
    }

    private function cargarAdjuntosGlobales(): void
    {
        $this->adjuntosGlobales = OrdenesAdjuntoModel::query()
            ->where('id_orden', $this->id_orden)
            ->orderBy('fecha_subida', 'desc')
            ->get()
            ->map(fn($a) => [
                'id'     => $a->id_adjunto ?? $a->id ?? null,
                'nombre' => $a->nombre_archivo,
                'ruta'   => $a->ruta_archivo,
                'fecha'  => $a->fecha_subida,
                'tipo'   => $a->tipo,
            ])
            ->all();
    }

    private function cargarAdjuntosPorBien(): void
    {
        $idsBien = array_column($this->bienes, 'id_orden_bien');
        if (empty($idsBien)) {
            $this->adjuntosBienListado = [];
            return;
        }

        $list = OrdenesBienesAdjuntoModel::query()
            ->whereIn('id_orden_bien', $idsBien)
            ->orderBy('fecha_subida', 'desc')
            ->get()
            ->groupBy('id_orden_bien');

        $this->adjuntosBienListado = [];
        foreach ($idsBien as $idBien) {
            $this->adjuntosBienListado[$idBien] = ($list[$idBien] ?? collect())
                ->map(fn($a) => [
                    'id_adjunto' => $a->id_adjunto,
                    'nombre'     => $a->nombre_archivo,
                    'ruta'       => $a->ruta_archivo,
                    'fecha'      => $a->fecha_subida,
                    'tipo'       => $a->tipo,
                ])->all();
        }
    }

    private function cargarCatalogos(): void
    {
        $this->serviciosDisponibles  = [];
        $this->materialesDisponibles = [];
        $this->serviciosMateriales   = [];
        $this->tieneCatalogo         = false;

        $idContrato = $this->orden->id_contrato ?? null;

        $actividades = EmpresasActividadesModel::query()
            ->where('cuit', $this->empresaActual)
            ->pluck('cod_actividad')
            ->filter()
            ->values();


        if ($actividades->isNotEmpty()) {
            $id_servicios = ServiciosActividadesEconomicasModel::whereIn('cod_actividad', $actividades)
                ->pluck('id_servicio');

            $this->serviciosDisponibles = ServiciosModel::whereIn('id_servicio', $id_servicios)
                ->orderBy('nombre')
                ->get(['id_servicio', 'nombre'])
                ->toArray();

            $rels = ServiciosMaterialesModel::whereIn('id_servicio', $id_servicios)
                ->get(['id_servicio', 'id_material']);

            foreach ($rels as $r) {
                $this->serviciosMateriales[$r->id_servicio][] = $r->id_material;
            }

            $serviciosMateriales = $rels->pluck('id_material')->unique()->values();
            $this->materialesDisponibles = MaterialesModel::whereIn('id_material', $serviciosMateriales)
                ->where('estado', 'activo')
                ->orderBy('nombre')
                ->get(['id_material', 'nombre', 'unidad', 'codigo_interno'])
                ->toArray();

            $this->tieneCatalogo = !empty($this->serviciosDisponibles) || !empty($this->materialesDisponibles);
        }
    }

    public function buscarMaterial(string $query): void
    {
        $q = trim($query);
        $this->materialesBusqueda = [
            'query'   => $q,
            'results' => $q === '' ? [] : MaterialesModel::query()
                ->where(function ($w) use ($q) {
                    $w->where('nombre', 'like', "%{$q}%")
                        ->orWhere('codigo_interno', 'like', "%{$q}%");
                })
                ->orderBy('nombre')
                ->limit(20)
                ->get(['id_material', 'codigo_interno', 'nombre', 'unidad'])
                ->toArray(),
        ];
    }

    public function agregarMaterialGlobal(int $id_material, float $cantidad = 1.0): void
    {
        $ya = collect($this->materialesSeleccionGlobal)->firstWhere('id_material', $id_material);
        if (!$ya) {
            $this->materialesSeleccionGlobal[] = ['id_material' => $id_material, 'cantidad' => $cantidad];
        }
    }

    public function quitarMaterialGlobal(int $idx): void
    {
        if (isset($this->materialesSeleccionGlobal[$idx])) {
            array_splice($this->materialesSeleccionGlobal, $idx, 1);
        }
    }

    public function toggleServicioGlobal(int $id_servicio): void
    {
        $in = in_array($id_servicio, $this->serviciosSeleccionGlobal, true);
        $this->serviciosSeleccionGlobal = $in
            ? array_values(array_diff($this->serviciosSeleccionGlobal, [$id_servicio]))
            : array_values(array_unique([...$this->serviciosSeleccionGlobal, $id_servicio]));
    }

    public function agregarMaterialA(int $id_orden_bien, int $id_material, float $cantidad = 1.0): void
    {
        $this->materialesSeleccion[$id_orden_bien] = $this->materialesSeleccion[$id_orden_bien] ?? [];
        $ya = collect($this->materialesSeleccion[$id_orden_bien])->firstWhere('id_material', $id_material);
        if (!$ya) {
            $this->materialesSeleccion[$id_orden_bien][] = ['id_material' => $id_material, 'cantidad' => $cantidad];
        }
    }

    public function quitarMaterialDe(int $id_orden_bien, int $index): void
    {
        if (isset($this->materialesSeleccion[$id_orden_bien][$index])) {
            array_splice($this->materialesSeleccion[$id_orden_bien], $index, 1);
        }
    }

    public function toggleServicioItem(int $id_orden_bien, int $id_servicio): void
    {
        $arr = $this->serviciosSeleccion[$id_orden_bien] ?? [];
        $in  = in_array($id_servicio, $arr, true);
        $this->serviciosSeleccion[$id_orden_bien] = $in
            ? array_values(array_diff($arr, [$id_servicio]))
            : array_values(array_unique([...$arr, $id_servicio]));
    }


    public function updatedAdjuntosGlobalesNuevos(): void {}

    public function quitarAdjuntoGlobalNuevo(int $index): void
    {
        if (isset($this->adjuntosGlobalesNuevos[$index])) {
            array_splice($this->adjuntosGlobalesNuevos, $index, 1);
        }
    }

    public function quitarAdjuntoBienNuevo(int $id_orden_bien, int $index): void
    {
        if (isset($this->adjuntosBienNuevos[$id_orden_bien][$index])) {
            array_splice($this->adjuntosBienNuevos[$id_orden_bien], $index, 1);
        }
    }


    public function crearMaterial(): void
    {
        $this->validate([
            'materialNuevo.nombre' => ['required', 'string', 'min:2'],
            'materialNuevo.unidad' => ['nullable', 'string', 'max:50'],
            'materialNuevo.codigo_interno' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique((new MaterialesModel)->getTable(), 'codigo_interno')
            ],
            'materialNuevo.descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        $m = MaterialesModel::create([
            'codigo_interno' => $this->materialNuevo['codigo_interno'] ?? null,
            'nombre'         => $this->materialNuevo['nombre'],
            'unidad'         => $this->materialNuevo['unidad'] ?? null,
            'descripcion'    => $this->materialNuevo['descripcion'] ?? null,
            'estado'         => 'activo',
        ]);

        $this->materialNuevo = [];
        $this->modoCrearMaterial = false;

        if (!empty($this->materialesBusqueda['query'])) {
            $this->buscarMaterial($this->materialesBusqueda['query']);
        }

        $this->dispatch('toast', ['type' => 'success', 'message' => 'Material creado.']);
    }

    private function rulesCerrar(): array
    {
        return [
            'fecha_cierre' => ['required', 'date', 'after_or_equal:' . $this->orden->fecha],
            'adjuntosGlobalesNuevos.*' => ['file', 'max:10240'],
            // Por ítem 
            'resumenBien.*.estado'     => ['nullable', 'string', 'max:50'],
            'resumenBien.*.resolucion' => ['nullable', 'string', 'max:2000'],
            'adjuntosBienNuevos.*.*'   => ['file', 'max:10240'],
        ];
    }

    public function cerrarOrden()
    {
        $this->validate($this->rulesCerrar());

        DB::beginTransaction();
        try {
            $payloadOrden = [
                'estado_orden' => 'Cerrada',
                'fecha_cierre' => $this->fecha_cierre,
            ];

            if ($this->usuarioEsRepresentante) {
                $payloadOrden['id_relacion_usuario'] = $this->id_relacion_representante;
                $payloadOrden['representante_tecnico'] = $this->nombre_representante;
            }

            $this->orden->update($payloadOrden);

            // 2) Guardar GLOBAL si modo global o si acción pasiva está activa
            if ($this->modoAplicacion === 'global' || $this->accionPasiva) {
                $this->aplicarGlobalATodosLosBienes();
            } else {
                // 3) Guardar POR ÍTEM
                $this->aplicarPorItem();
            }

            // 4) Adjuntos globales (tipo Cierre)
            foreach ($this->adjuntosGlobalesNuevos as $file) {
                $path = $file->store("StorageMvp/ordenes/{$this->id_orden}/cierre_global", 's3');
                OrdenesAdjuntoModel::create([
                    'id_orden'       => $this->id_orden,
                    'nombre_archivo' => $file->getClientOriginalName(),
                    'ruta_archivo'   => $path,
                    'fecha_subida'   => now(),
                    'tipo'           => 'Cierre',
                ]);
            }

            // 5) Adjuntos por bien
            foreach ($this->adjuntosBienNuevos as $idBien => $files) {
                foreach ((array)$files as $file) {
                    $path = $file->store("StorageMvp/ordenes/{$this->id_orden}/bienes/{$idBien}", 's3');
                    OrdenesBienesAdjuntoModel::create([
                        'id_orden_bien'  => $idBien,
                        'nombre_archivo' => $file->getClientOriginalName(),
                        'ruta_archivo'   => $path,
                        'tipo'           => 'Cierre',
                        'fecha_subida'   => now(),
                        'subido_por'     => auth()->id(),
                    ]);
                }
            }

            DB::commit();

            $this->cargarAdjuntosGlobales();
            $this->cargarAdjuntosPorBien();

            $this->dispatch('toast', ['type' => 'success', 'message' => 'Orden cerrada correctamente.']);
            return redirect()->route('ordenes');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('toast', ['type' => 'error', 'message' => 'Error al cerrar la orden: ' . $e->getMessage()]);
        }
    }

    private function aplicarGlobalATodosLosBienes(): void
    {
        foreach ($this->bienes as $b) {
            $idBien = $b['id_orden_bien'];

            // Estado / Resolución (si cargaste algo global acá podrías aplicarlo también)
            if (isset($this->resumenBien[$idBien])) {
                $bien = ordenesBienesModel::where('id_orden_bien', $idBien)->first();
                if ($bien) {
                    $bien->update([
                        'estado'     => $this->resumenBien[$idBien]['estado'] ?? $bien->estado,
                        'resolucion' => $this->resumenBien[$idBien]['resolucion'] ?? $bien->resolucion,
                    ]);
                }
            }

            // Servicios globales
            foreach ($this->serviciosSeleccionGlobal as $idServicio) {
                $srv = ServiciosModel::find($idServicio);
                if ($srv) {
                    OrdenesBienesServicioModel::create([
                        'id_orden_bien'      => $idBien,
                        'id_servicio'        => $idServicio,
                        'descripcion_servicio' => $srv->nombre, // snapshot
                        'horas'              => null,
                        'precio_unitario'    => null,
                        'moneda'             => null,
                        'origen'             => $this->orden->id_contrato ? 'contrato' : 'manual',
                        'id_contrato'        => $this->orden->id_contrato,
                        'id_contrato_servicio' => null,
                        'fecha_carga'        => now(),
                        'cargado_por'        => auth()->id(),
                    ]);
                }
            }

            // Materiales globales
            foreach ($this->materialesSeleccionGlobal as $mm) {
                $mat = MaterialesModel::find($mm['id_material']);
                if ($mat) {
                    OrdenesBienesMaterialModel::create([
                        'id_orden_bien'      => $idBien,
                        'id_material'        => $mat->id_material,
                        'nombre_material'    => $mat->nombre,     // snapshot
                        'unidad'             => $mat->unidad,
                        'cantidad'           => (float)($mm['cantidad'] ?? 1),
                        'precio_unitario'    => null,
                        'moneda'             => null,
                        'origen'             => $this->orden->id_contrato ? 'contrato' : 'manual',
                        'id_contrato'        => $this->orden->id_contrato,
                        'id_contrato_servicio_material' => null,
                        'fecha_carga'        => now(),
                        'cargado_por'        => auth()->id(),
                    ]);
                }
            }
        }
    }

    private function aplicarPorItem(): void
    {
        foreach ($this->bienes as $b) {
            $idBien = $b['id_orden_bien'];

            // Estado/Resolución
            if (isset($this->resumenBien[$idBien])) {
                $bien = ordenesBienesModel::where('id_orden_bien', $idBien)->first();
                if ($bien) {
                    $bien->update([
                        'estado'     => $this->resumenBien[$idBien]['estado'] ?? $bien->estado,
                        'resolucion' => $this->resumenBien[$idBien]['resolucion'] ?? $bien->resolucion,
                    ]);
                }
            }

            // Servicios por ítem
            foreach ($this->serviciosSeleccion[$idBien] ?? [] as $idServicio) {
                $srv = ServiciosModel::find($idServicio);
                if ($srv) {
                    OrdenesBienesServicioModel::create([
                        'id_orden_bien'      => $idBien,
                        'id_servicio'        => $idServicio,
                        'descripcion_servicio' => $srv->nombre,
                        'horas'              => null,
                        'precio_unitario'    => null,
                        'moneda'             => null,
                        'origen'             => $this->orden->id_contrato ? 'contrato' : 'manual',
                        'id_contrato'        => $this->orden->id_contrato,
                        'id_contrato_servicio' => null,
                        'fecha_carga'        => now(),
                        'cargado_por'        => auth()->id(),
                    ]);
                }
            }

            // Materiales por ítem
            foreach ($this->materialesSeleccion[$idBien] ?? [] as $mm) {
                $mat = MaterialesModel::find($mm['id_material']);
                if ($mat) {
                    OrdenesBienesMaterialModel::create([
                        'id_orden_bien'      => $idBien,
                        'id_material'        => $mat->id_material,
                        'nombre_material'    => $mat->nombre,
                        'unidad'             => $mat->unidad,
                        'cantidad'           => (float)($mm['cantidad'] ?? 1),
                        'precio_unitario'    => null,
                        'moneda'             => null,
                        'origen'             => $this->orden->id_contrato ? 'contrato' : 'manual',
                        'id_contrato'        => $this->orden->id_contrato,
                        'id_contrato_servicio_material' => null,
                        'fecha_carga'        => now(),
                        'cargado_por'        => auth()->id(),
                    ]);
                }
            }
        }
    }

    public function render()
    {
        return view('livewire.servicios.ordenes-de-trabajo.cerrar-orden');
    }
}
