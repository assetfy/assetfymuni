<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;

use App\Helpers\IdHelper;
use App\Models\ActivosFotosModel;
use App\Models\ClientesEmpresaModel;
use App\Models\EmpresasModel;
use App\Models\OrdenesAdjuntoModel;
use App\Models\OrdenesModel;
use App\Models\OrdenesPorgramacionModel;
use App\Models\OrdenSlaModel;
use App\Models\ServiciosModel;
use App\Models\ServiciosSubcategoriasModel;
use App\Models\UbicacionesModel;
use App\Models\UsuariosEmpresasModel;
use App\Models\ordenesBienesModel;
use App\Models\ordenesBienesAdjuntosModel;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;


class EditarOrden extends Component
{
    // --------- Estado principal (DETALLE SOLO LECTURA) ----------
    public $open = false; // si lo usás como modal
    public $orden;

    public $cliente;
    public $prestadora;
    public $tecnico;
    public $ordenProgramacion;
    public $horario;
    public $nombreCliente;
    public $Visita;

    // Múltiples activos
    public $activosOrden = [];           // array (no colección)
    public $activoSeleccionadoId = null; // id_ot_bien (preferente) o id_activo
    public $ubicacion = null;            // Ubicacion del activo en foco
    public $imagenesActivos = [];        // URLs temporales de fotos (s3)

    // Servicios por subcategoría del activo en foco (solo mostrar)
    public $servicios = [];
    public $searchServicio = '';

    // Adjuntos de la ORDEN (solo mostrar existentes)
    public $imagenesInfo = [];
    public $firma; // registro si existe
    public $trabajo = [];
    public $trabajoCount = 0;

    // Adjuntos por ACTIVO (solo mostrar existentes)
    // [id_ot_bien => [ ['id_adjunto'=>..,'nombre_archivo'=>..,'ruta_archivo'=>url_temporal,...], ... ]]
    public $adjuntosPorBien = [];

    // Pestañas de detalle (dejamos Solicitud/Datos)
    public $activeTab = 'solicitud';

    // --------- Modal: listener para abrir (si usás modal) ----------
    protected $listeners = ['openEditOrden'];

    public function openEditOrden($data)
    {
        $this->orden = OrdenesModel::find($data);
        if (!$this->orden) return;

        $this->activeTab = 'solicitud';
        $this->cargarDatosBase();
        $this->cargarActivosOrden();
        $this->open = true; // mostrás modal
    }


    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    // --------- Datos base (cabecera) ----------
    private function cargarDatosBase(): void
    {
        $empresa = IdHelper::empresaActual();

        // Adjuntos existentes de la orden
        $this->imagenesInfo = OrdenesAdjuntoModel::where('id_orden', $this->orden->id_orden)
            ->where('tipo', 'info')
            ->pluck('ruta_archivo')
            ->map(fn($r) => Storage::disk('s3')->temporaryUrl($r, now()->addMinutes(10)))
            ->toArray();

        $this->firma = OrdenesAdjuntoModel::where('id_orden', $this->orden->id_orden)
            ->where('tipo', 'firma')
            ->first();

        $this->trabajo = OrdenesAdjuntoModel::where('id_orden', $this->orden->id_orden)
            ->where('tipo', 'trabajo')
            ->pluck('ruta_archivo')
            ->toArray();

        $this->trabajoCount = count($this->trabajo);

        // Cliente / prestadora / técnico
        $this->cliente = ClientesEmpresaModel::query()
            ->where('cliente_cuit', $this->orden->cuit_Cliente)
            ->where('empresa_cuit', $empresa->cuit ?? null)
            ->with('contratos')
            ->first();

        $this->nombreCliente = EmpresasModel::where('cuit', $this->orden->cuit_Cliente)->value('razon_social');
        $this->prestadora    = EmpresasModel::where('cuit', $this->orden->proveedor)->first();
        $this->tecnico       = UsuariosEmpresasModel::with('usuarios')
            ->where('id_relacion', $this->orden->id_relacion_usuario)
            ->first();

        // Programación / SLA
        if ($this->orden->tipo_orden == 'Correctivo/Reparación') {
            $this->ordenProgramacion = OrdenesPorgramacionModel::where('id_orden', $this->orden->id_orden)->first();
        } else {
            $this->horario = OrdenSlaModel::where('id_orden', $this->orden->id_orden)->first();
        }

        // Servicio base de la orden (si estaba definido)
        $this->servicios = ServiciosModel::where('id_servicio', $this->orden->id_servicio)->get();
    }

    // --------- Activos de la orden ----------
    private function cargarActivosOrden(): void
    {
        $this->activosOrden = ordenesBienesModel::query()
            ->where('id_orden', $this->orden->id_orden)
            ->with([
                'tipo:id_tipo,nombre',
                'categoria:id_categoria,nombre',
                'subcategoria:id_subcategoria,nombre',
                'activos:id_activo,id_tipo,id_categoria,id_subcategoria,id_ubicacion,nombre',
            ])
            ->get()
            ->map(fn($m) => $m->toArray())
            ->all();

        if (empty($this->activosOrden)) {
            $this->servicios = [];
            $this->ubicacion = null;
            $this->imagenesActivos = [];
            $this->adjuntosPorBien = [];
            return;
        }

        // Foco inicial
        if (empty($this->activoSeleccionadoId)) {
            $primero = $this->activosOrden[0];
            $this->activoSeleccionadoId = (int)($primero['id_ot_bien'] ?? $primero['id_activo']);
        }

        $this->refrescarFocoYAdjuntos();
    }

    public function setActivoSeleccionado(int $id): void
    {
        $this->activoSeleccionadoId = $id;
        $this->refrescarFocoYAdjuntos();
    }

    private function refrescarFocoYAdjuntos(): void
    {
        $bien = collect($this->activosOrden)->first(function ($b) {
            return (int)($b['id_ot_bien'] ?? $b['id_activo']) === (int)$this->activoSeleccionadoId;
        });

        if (!$bien) {
            $this->servicios = [];
            $this->ubicacion = null;
            $this->imagenesActivos = [];
            return;
        }

        // Servicios por subcategoría
        $idSubcat = (int)($bien['id_subcategoria'] ?? ($bien['subcategoria']['id_subcategoria'] ?? 0));
        $this->servicios = ServiciosSubcategoriasModel::with('servicios')
            ->where('id_subcategoria', $idSubcat)
            ->get()
            ->map(fn($r) => (object)[
                'id_servicio' => $r->id_servicio,
                'nombre'      => optional($r->servicios)->nombre,
            ])
            ->sortBy('nombre')
            ->values()
            ->all();

        // Fotos del activo (URLs temporales)
        $idActivo = (int)($bien['activos']['id_activo'] ?? 0);
        $rutas = $idActivo
            ? ActivosFotosModel::where('id_activo', $idActivo)->pluck('ruta_imagen')->toArray()
            : [];
        $this->imagenesActivos = collect($rutas)->map(
            fn($r) => Storage::disk('s3')->temporaryUrl($r, now()->addMinutes(10))
        )->toArray();

        // Ubicación
        $idUbi = (int)($bien['activos']['id_ubicacion'] ?? 0);
        $this->ubicacion = $idUbi ? UbicacionesModel::find($idUbi) : null;

        // Adjuntos existentes por bien (cacheo todos, solo lectura)
        $this->cargarAdjuntosDeTodosLosBienes();
    }

    private function cargarAdjuntosDeTodosLosBienes(): void
    {
        $ids = [];
        foreach ($this->activosOrden as $b) {
            $ids[] = (int)($b['id_ot_bien'] ?? 0);
        }
        $ids = array_filter($ids);

        $this->adjuntosPorBien = [];
        if (!empty($ids)) {
            $reg = ordenesBienesAdjuntosModel::whereIn('id_orden_bien', $ids)
                ->orderBy('id_adjunto', 'desc')
                ->get(['id_adjunto', 'id_orden_bien', 'nombre_archivo', 'ruta_archivo', 'fecha_subido', 'subido_por']);

            foreach ($reg as $r) {
                $this->adjuntosPorBien[$r->id_orden_bien][] = [
                    'id_adjunto'    => $r->id_adjunto,
                    'nombre_archivo' => $r->nombre_archivo,
                    'ruta_archivo'  => Storage::disk('s3')->temporaryUrl($r->ruta_archivo, now()->addMinutes(10)),
                    'fecha_subido'  => $r->fecha_subido,
                    'subido_por'    => $r->subido_por,
                ];
            }
        }
    }

    // --------- Búsqueda local de servicios (solo para mostrar) ----------
    public function updatedSearchServicio($value)
    {
        $q = mb_strtolower(trim((string)$value));
        $bien = collect($this->activosOrden)->first(
            fn($b) =>
            (int)($b['id_ot_bien'] ?? $b['id_activo']) === (int)$this->activoSeleccionadoId
        );
        $idSubcat = $bien ? (int)($bien['id_subcategoria'] ?? ($bien['subcategoria']['id_subcategoria'] ?? 0)) : 0;

        $lista = ServiciosSubcategoriasModel::with('servicios')
            ->where('id_subcategoria', $idSubcat)
            ->get()
            ->map(fn($r) => (object)[
                'id_servicio' => $r->id_servicio,
                'nombre'      => optional($r->servicios)->nombre,
            ])
            ->sortBy('nombre')
            ->values()
            ->all();

        if ($q !== '') {
            $lista = array_values(array_filter(
                $lista,
                fn($s) => str_contains(mb_strtolower($s->nombre ?? ''), $q)
            ));
        }

        $this->servicios = $lista;
    }

    // --------- Render ----------
    public function render()
    {
        return view('livewire.servicios.ordenes-de-trabajo.editar-orden');
    }
}
