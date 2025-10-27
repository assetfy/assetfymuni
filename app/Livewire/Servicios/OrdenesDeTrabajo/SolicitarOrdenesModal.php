<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;

use Livewire\Component;
use App\Helpers\IdHelper;
use App\Models\ActivosModel;
use App\Models\EmpresasModel;
use App\Models\MisProveedoresModel;
use App\Models\OrdenesAdjuntoModel;
use App\Models\OrdenesModel;
use App\Models\OrdenesPorgramacionModel;
use App\Models\OrdenSlaModel;
use App\Models\provedoresContratosModel;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\WithFileUploads;


class SolicitarOrdenesModal extends Component
{
    use WithFileUploads;
    protected $listeners = ['openSoliitarOrden'];

    public $open = false;
    public $activo;

    // selección técnica
    public $tecnicos, $filteredTecnicos, $id_tecnico, $tecnicoBusqueda, $searchTecnico = '';
    public $filteredEmpresas, $empresas, $empresaBusqueda, $searchEmpresa = '', $cuit, $Gestora;

    // tipo de servicio y descripción
    public $selectedTipoServicio, $descripcion;

    // SLA preventivo
    public $sla_4hs = false;
    public $sla_8hs = false;
    public $sla_12hs = false;
    public $sla_24hs = false;

    // SLA correctivo
    public $slaTipo, $fechaProgramada, $periodicidad, $fechaInicio, $fechaFin;
    public $diasSeleccionados = [];

    // Propiedad para acumular todas las imágenes a subir
    public $imagenesTrabajo = [];
    // Propiedad auxiliar para cada nueva selección de archivos
    public $newImages = [];


    public function openSoliitarOrden($data)
    {
        // permisos
        if (! app(\App\Services\MiddlewareInvoker::class)::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }

        $this->Gestora = EmpresasModel::where('cuit', IdHelper::idEmpresa())->pluck('tipo')->first() == 1;
        if ($this->Gestora) {
            $this->empresas();
            $this->filteredEmpresas = $this->empresas; // Asegurar que no sea null
        }
        $this->datos($data);

        $this->open = true;
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

    private function datos($data)
    {
        $this->activo = ActivosModel::findOrFail($data);

        // carga técnicos de esta empresa
        $this->tecnicos = UsuariosEmpresasModel::with('usuarios')
            ->where('cuit', IdHelper::idEmpresa())
            ->where('es_representante_tecnico', 'Si')
            ->whereNot('estado', 'Deshabilitado')
            ->get();

        $this->filteredTecnicos = $this->tecnicos;
        $this->id_tecnico = null;
        $this->tecnicoBusqueda = null;
        $this->selectedTipoServicio = null;
        $this->descripcion = '';
        $this->sla_4hs = $this->sla_8hs = $this->sla_12hs = $this->sla_24hs = false;
        $this->slaTipo = null;
        $this->fechaProgramada = $this->fechaInicio = $this->fechaFin = null;
        $this->diasSeleccionados = [];
    }

    public function updatedSearchTecnico($value)
    {
        $this->filteredTecnicos = $this->tecnicos
            ->filter(fn($t) => stripos($t->usuarios->name, $value) !== false);
    }

    public function setIdTecnico($id)
    {
        $this->tecnicoBusqueda = $this->tecnicos->firstWhere('id_usuario', $id);
        $this->id_tecnico = $id;
        $this->searchTecnico = '';
        $this->filteredTecnicos = $this->tecnicos;
    }

    public function selectSLA($field)
    {
        // desmarca todo
        $this->sla_4hs = $this->sla_8hs = $this->sla_12hs = $this->sla_24hs = false;
        $this->{$field} = true;
    }

    public function save()
    {
        DB::beginTransaction();
        try {
            // regla básica
            if (!$this->selectedTipoServicio) {
                $this->addError('selectedTipoServicio', 'Debe elegir un tipo de servicio.');
                throw new \Exception('Validación.');
            }

            $propietario = ActivosModel::where('id_activo', $this->activo->id_activo)
                ->pluck('empresa_titular')
                ->first();

            if ($this->Gestora) {
                $empresa = $this->empresaBusqueda->cuit;
                $tecnico = null;
                $relacion = null;
            } else {
                $empresa = IdHelper::idEmpresa();
                $tecnico = $this->tecnicoBusqueda->id_usuario;
                $relacion = $this->tecnicoBusqueda->id_relacion;
            }

            // creo la orden
            $orden = OrdenesModel::create([
                'id_activo'           => $this->activo->id_activo,
                'id_subcategoria_activo' => $this->activo->id_subcategoria,
                'id_categoria_activo' => $this->activo->id_categoria,
                'id_tipo_activo'      => $this->activo->id_tipo,
                'proveedor'           => $empresa,
                'estado_vigencia'     => 'Activo',
                'comentarios'         => $this->descripcion,
                'representante_tecnico' => $tecnico,
                'id_relacion_usuario' => $relacion,
                'tipo_orden'          => $this->selectedTipoServicio,
                'estado_orden'        => 'Pendiente',
                'fecha'               => now()->toDateString(),
                'id_usuario'          => Auth::id(),
                'cuit_Cliente'        => $propietario,
            ]);
            // programaciones o SLA
            if ($this->selectedTipoServicio === 'Preventivo') {
                OrdenesPorgramacionModel::create([
                    'id_orden'           => $orden->id_orden,
                    'fecha_inicio'       => $this->fechaInicio ?? $this->fechaProgramada,
                    'fecha_fin'          => $this->fechaFin,
                    'periodicidad'       => $this->periodicidad,
                    'fechas_periodicidad' => $this->diasSeleccionados
                        ? implode(',', $this->diasSeleccionados)
                        : null,
                ]);
            } else {
                OrdenSlaModel::create([
                    'id_orden'  => $orden->id_orden,
                    'sla_horas' => $this->sla_4hs
                        ? 4
                        : ($this->sla_8hs ? 8 : ($this->sla_12hs ? 12 : 24)),
                ]);
            }

            // 1) Guardar imágenes del trabajo
            if ($this->imagenesTrabajo) {
                foreach ($this->imagenesTrabajo as $foto) {
                    $path = $foto->store('StorageMvp/fotos_Ordenes', 's3');
                    OrdenesAdjuntoModel::create([
                        'id_orden'       => $orden->id_orden,
                        'nombre_archivo' => $foto->getClientOriginalName(),
                        'ruta_archivo'   => $path,
                        'fecha_subida'   => now(),
                        'tipo'           => 'Info',       // opcional: para distinguir tipos
                    ]);
                }
            }
            DB::commit();

            $this->dispatch('Exito', [
                'title'   => 'Orden creada',
                'message' => 'La orden de trabajo se ha creado correctamente.',
            ]);

            $Prestadora = EmpresasModel::where('cuit', IdHelper::idEmpresa())->pluck('tipo')->first() == 2;

            if ($Prestadora) {
                return redirect()->route('ordenes');
            } else {
                $this->open = false;
            }
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('errorInfo', [
                'title'   => 'Error al crear orden',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function updatedSearchEmpresa($value)
    {
        $this->updatedSearchEmpresaBusqueda($value); // Llama a cargar las ubicaciones filtradas
    }

    // Métodos de Empresa
    private function empresas()
    {
        // 1. Obtener los IDs de mis proveedores favoritos
        $misProveedoresIds = MisProveedoresModel::where('empresa', IdHelper::idEmpresa())->pluck('id');

        // 2. Obtener los proveedores que tienen contratos activos
        $proveedoresConContratos = provedoresContratosModel::whereIn('id_mis_proveedor', $misProveedoresIds)
            ->pluck('id_mis_proveedor');

        // 3. Obtener los proveedores favoritos filtrando solo los que tienen contrato
        $this->empresas = MisProveedoresModel::whereIn('id', $proveedoresConContratos)->get();
    }

    private function updatedSearchEmpresaBusqueda($value)
    {
        $this->filteredEmpresas = $this->empresas->filter(function ($empresa) use ($value) {
            return stripos($empresa->razon_social, $value) !== false;
        });
    }

    public function setCuitEmpresa($cuit)
    {
        $this->cuit = $cuit;

        $this->empresaBusqueda = EmpresasModel::where('cuit', $cuit)->first();
        $this->searchEmpresa = '';

        $this->filteredEmpresas = $this->empresas;

        $this->dispatch('closeDropdown', ['dropdown' => 'empresa']); // Cerrar el dropdown
    }

    public function render()
    {
        return view('livewire.servicios.ordenes-de-trabajo.solicitar-ordenes-modal');
    }
}
