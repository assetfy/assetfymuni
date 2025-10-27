<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;

use App\Helpers\IdHelper;
use App\Models\ActivosCompartidosModel;
use App\Models\ActivosModel;
use App\Models\CategoriaModel;
use App\Models\EmpresasModel;
use App\Models\NotificacionesModel;
use App\Models\OrdenesAdjuntoModel;
use App\Models\OrdenesModel;
use App\Models\OrdenesPorgramacionModel;
use App\Models\OrdenSlaModel;
use App\Models\UsuariosEmpresasModel;
use App\Notifications\OrdenAsignadaNotification;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

class Ordenesdetrabajo extends Component
{
    use WithFileUploads;
    // --- PROPIEDADES PARA CLIENTE, ACTIVOS Y CATEGORÍA ---
    public $empresas, $servicios, $empresa, $actividadEconomica, $misServicios, $tecnicos;
    public $activos, $activoBusqueda, $id_activo, $searchActivo, $filteredActivos;
    public $id_cliente, $filteredEmpresas, $empresaBusqueda, $searchEmpresa, $tiposServicio, $orden, $cuit;
    public $diasSeleccionados = [];

    // Para Categoría
    public $categorias, $searchCategoria, $filteredCategorias, $id_categoria, $categoriaBusqueda;

    // --- PROPIEDADES PARA TÉCNICOS ---
    public $searchTecnico, $filteredTecnicos, $id_tecnico, $tecnicoBusqueda;

    // --- PROPIEDADES PARA SERVICIOS DE LA EMPRESA ---
    public $serviciosEmpresa, $searchServicio, $filteredServiciosEmpresa, $id_servicioEmpresa, $servicioEmpresaBusqueda;

    public $selectedTipoServicio; // 1 = Correctivo/Reparación, 2 = Preventivo

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

    // Propiedad para acumular todas las imágenes a subir
    public $imagenesTrabajo = [];
    // Propiedad auxiliar para cada nueva selección de archivos
    public $newImages = [];



    public function mount()
    {
        // Cargar empresas
        $this->empresas();
        $this->filteredEmpresas = $this->empresas; // Asegurar que no sea null

        // Cargar servicios y técnicos
        $this->UsuariosTecnicos();
        $this->filteredTecnicos = $this->tecnicos;

        // Cargar categorías
        $this->categorias = CategoriaModel::all();
        $this->filteredCategorias = $this->categorias;
        $this->id_categoria = null;
        $this->categoriaBusqueda = null;
        $this->activosDelegados();
        // Y como no hay aún categoría, inicializamos:
        $this->filteredActivos = $this->activos;
    }

    public function updatedSearchEmpresa($value)
    {
        $this->updatedSearchEmpresaBusqueda($value); // Llama a cargar las ubicaciones filtradas
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

    // Métodos de Empresa
    private function empresas()
    {
        $this->empresa = EmpresasModel::where('cuit', IdHelper::idEmpresa())->first();

        $empresasTitulares = ActivosCompartidosModel::where('empresa_proveedora', $this->empresa->cuit)
            ->where('estado_asignacion', 'Aceptado')
            ->pluck('empresa_titular');

        $this->empresas = EmpresasModel::whereIn('cuit', $empresasTitulares)->get();
    }

    private function updatedSearchEmpresaBusqueda($value)
    {
        $this->filteredEmpresas = $this->empresas->filter(function ($empresa) use ($value) {
            return stripos($empresa->razon_social, $value) !== false;
        });
    }

    public function setCuitEmpresa($cuit)
    {
        // 1) Selección de empresa
        $this->cuit            = $cuit;
        $this->empresaBusqueda = EmpresasModel::where('cuit', $cuit)->first();

        // 2) Resetear estado de dropdowns y búsquedas
        $this->searchEmpresa      = '';
        $this->id_categoria       = null;
        $this->categoriaBusqueda  = null;
        $this->searchCategoria    = '';
        $this->filteredCategorias = collect();

        $this->id_activo       = null;
        $this->activoBusqueda  = null;
        $this->searchActivo    = '';
        $this->filteredActivos = collect();

        // 3) Recargar categorías y activos
        $this->filterCategorias();
        $this->activosDelegados();

        // 4) Aplicar siempre el filtro (internamente decide si 
        //    debe devolver todos o solo los de la categoría)
        $this->filterActivos();

        // 5) Cerrar dropdown
        $this->dispatch('closeDropdown', ['dropdown' => 'empresa']);
    }

    // Filtrar las categorías en base a los activos compartidos de la empresa
    private function filterCategorias()
    {
        if (! $this->empresaBusqueda) {
            $this->categorias = collect();
            $this->filteredCategorias = collect();
            return;
        }

        $idsCat = ActivosCompartidosModel::where('empresa_titular', $this->empresaBusqueda->cuit)
            ->where('estado_asignacion', 'Aceptado')
            ->pluck('id_cat');
        $this->categorias         = CategoriaModel::whereIn('id_categoria', $idsCat)->get();
        $this->filteredCategorias = $this->categorias;
    }
    // Método de filtrado de categorías
    public function updatedSearchCategoria($value)
    {
        // Solo permitir la búsqueda de categorías si hay una empresa seleccionada
        if ($this->empresaBusqueda) {
            $this->filteredCategorias = $this->categorias->filter(function ($cat) use ($value) {
                return stripos($cat->nombre, $value) !== false;
            });
        }
    }

    public function setIdCategoria($id)
    {
        $this->id_categoria      = $id;
        $this->categoriaBusqueda = CategoriaModel::find($id);
        $this->searchCategoria   = '';
        $this->filteredCategorias = $this->categorias;

        $this->dispatch('closeDropdown', ['dropdown' => 'categoria']);
    }

    private function activosDelegados()
    {

        $asignadosActivos = ActivosCompartidosModel::where('empresa_proveedora', $this->empresa->cuit)
            ->where('estado_asignacion', 'Aceptado')
            ->pluck('id_activo')
            ->toArray();
        $this->activos = ActivosModel::whereIn('id_activo', $asignadosActivos)
            ->with('categoria') // Cargar la relación con la categoría
            ->get();

        $this->filteredActivos = $this->activos;

        $this->filterActivos();
    }

    private function filterActivos()
    {
        $query = ActivosModel::query();
        // 1) Filtrar siempre por la empresa seleccionada
        if ($this->empresaBusqueda) {
            $query->where('empresa_titular', $this->empresaBusqueda->cuit)
                ->whereIn('id_activo', $this->activos->pluck('id_activo'));
        }
        // 2) Si hay categoría seleccionada, la aplico
        if ($this->id_categoria) {
            $query->where('id_categoria', $this->id_categoria)
                ->whereIn('id_activo', $this->activos->pluck('id_activo'));
        }
        // 3) Traigo y guardo
        $this->filteredActivos = $query
            ->with('categoria')
            ->get();
    }



    // Métodos para ACTIVOS
    public function updatedSearchActivo($value)
    {
        if (empty($value)) {
            $this->filteredActivos = $this->activos;
            return;
        }

        $this->filteredActivos = $this->activos->filter(function ($activo) use ($value) {
            return stripos($activo->nombre, $value) !== false;
        });
    }

    public function setIdActivo($id)
    {
        $this->id_activo      = $id;
        $this->activoBusqueda = ActivosModel::find($id);
        $this->searchActivo   = '';

        // re-aplico empresa + categorías
        $this->filterActivos();

        $this->dispatch('closeDropdown', ['dropdown' => 'activo']);
    }

    private function UsuariosTecnicos()
    {
        $this->tecnicos = UsuariosEmpresasModel::with(['usuarios'])
            ->where('cuit', IdHelper::idEmpresa())
            ->whereNot('estado', 'Deshabilitado')
            ->where('es_representante_tecnico', 'Si')
            ->get();
    }

    // Métodos para TÉCNICOS
    public function updatedSearchTecnico($value)
    {
        $this->filteredTecnicos = $this->tecnicos->filter(function ($tecnico) use ($value) {
            return stripos($tecnico->usuarios->nombre, $value) !== false;
        });
    }

    public function setIdTecnico($id)
    {
        $this->tecnicoBusqueda = $this->tecnicos->firstWhere('id_usuario', $id);
        $this->id_tecnico = $id;
        $this->searchTecnico = '';
        $this->filteredTecnicos = $this->tecnicos;
    }

    // Método para guardar los datos con transacción y validaciones condicionales
    public function save()
    {
        DB::beginTransaction();
        try {
            $this->validate($this->getValidationRules());

            // Creación de la orden de trabajo
            $this->crearOrdenTrabajo();

            $this->fotosAdjuntos();

            // Lógica para programar la orden según el tipo de servicio
            if ($this->selectedTipoServicio == 'Correctivo/Reparación') {
                $this->OrdenSla();
            } elseif ($this->selectedTipoServicio == 'Preventivo') {
                $this->OrdenesProgramaciones();
            }

            DB::commit();

            // Enviar notificación al representante técnico, si existe
            if ($this->orden->tecnico()->exists()) {
                $tecnico = $this->orden->tecnico()->first();
                // Enviar notificación al técnico
                try {
                    Notification::route('mail', $tecnico->usuarios->email)
                        ->notify(new OrdenAsignadaNotification($this->orden, auth()->user()));
                } catch (\Exception $e) {
                }
            }


            $this->dispatch('ordenTrabajo', [
                'title'   => 'Operación exitosa',
                'message' => 'Orden de trabajo creada correctamente.'
            ]);

            if ($this->id_tecnico) {
                NotificacionesModel::create([
                    'cuit_empresa' => IdHelper::idEmpresa(),
                    'id_usuario' => $this->orden->representante_tecnico,
                    'descripcion' => 'Se le ha asignado la orden ' . $this->orden->id_orden . ' - ' . $this->orden->comentarios,
                ]);
            };

            return redirect()->route('ordenes');
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->dispatch('errorInfo', [
                'title'   => 'Error al crear la orden',
                'message' => $e->getMessage()
            ]);
        }
    }

    private function fotosAdjuntos()
    {
        // 1) Guardar imágenes del trabajo
        if ($this->imagenesTrabajo) {
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
    }

    // Método para obtener las reglas de validación
    private function getValidationRules()
    {
        $rules = [
            'id_activo'            => 'required',
            'selectedTipoServicio' => 'required',
            'diasSeleccionados' => [
                function ($attribute, $value, $fail) {
                    if (in_array($this->periodicidad, ['semana', '2semanas', 'mes']) && empty($value)) {
                        $fail('Debe seleccionar al menos un día.');
                    }
                },
            ],
        ];

        if ($this->selectedTipoServicio == 'Preventivo') {
            // Servicio Preventivo
            if (!$this->sla_4hs && !$this->sla_8hs && !$this->sla_12hs && !$this->sla_24hs) {
                $this->addError('sla_preventivo', 'Debe seleccionar al menos un SLA para servicio preventivo.');
            }
        } else {
            // Servicio Correctivo/Reparación
            $rules['descripcion'] = 'required';
            if ($this->slaTipo == 'programado') {
                $rules['fechaProgramada'] = 'required|date';
            }
            if ($this->slaTipo == 'periodico') {
                $rules['fechaInicio']   = 'required|date';
                $rules['fechaFin']      = 'required|date|after:fechaInicio';
                $rules['periodicidad'] = 'required|in:semana,2semanas,mes';
            }
        }
        return $rules;
    }

    // Método para crear la orden de trabajo (guarda solo la fecha sin horas)
    private function crearOrdenTrabajo()
    {
        $Prestadora = EmpresasModel::where('cuit', IdHelper::idEmpresa())->pluck('tipo')->first() == 2
            ? $this->empresaBusqueda->cuit
            : null;

        return $this->orden = OrdenesModel::create([
            'id_activo'               => $this->activoBusqueda->id_activo,
            'id_subcategoria_activo'  => $this->activoBusqueda->id_subcategoria,
            'id_categoria_activo'     => $this->activoBusqueda->id_categoria,
            'id_tipo_activo'          => $this->activoBusqueda->id_tipo,
            'proveedor'               => IdHelper::idEmpresa(),
            'estado_vigencia'         => 'Activo',
            'comentarios'             => $this->descripcion,
            'representante_tecnico'   => $this->tecnicoBusqueda->id_usuario ?? null,
            'id_relacion_usuario'     => $this->tecnicoBusqueda->id_relacion ?? null,
            'tipo_orden'              => $this->selectedTipoServicio,
            'estado_orden'            => 'Pendiente',
            'fecha' => now()->format('Y-m-d'),  // Guarda solo la fecha (YYYY-MM-DD)
            'id_usuario'              => auth()->user()->id,
            'cuit_Cliente'            => $Prestadora,
        ]);
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

    public function selectSLA(string $selected): void
    {
        // Lista de todas las opciones de SLA
        $slas = ['sla_4hs', 'sla_8hs', 'sla_12hs', 'sla_24hs'];
        // recorro para ver cual esta selecionado y asi saco el if 
        foreach ($slas as $sla) {
            $this->$sla = ($sla === $selected);
        }
    }

    public function render()
    {
        return view('livewire.servicios.ordenes-de-trabajo.ordenesdetrabajo');
    }
}
