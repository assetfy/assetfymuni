<?php

namespace App\Livewire\Servicios;

use App\Models\ActividadesEconomicasModel;
use App\Models\ActivosModel;
use App\Models\CategoriaModel;
use App\Models\ServiciosActividadesEconomicasModel;
use App\Models\ServiciosSubcategoriasModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\EmpresasActividadesModel;
use App\Models\ServiciosModel;
use App\Models\EmpresasModel;
use App\Models\EstadoGeneralModel;
use App\Models\MisProveedoresModel;
use App\Models\SubcategoriaModel;
use App\Models\TiposModel;
use App\Models\TiposSolicitudModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiciosVistaFormulario extends Component
{
    public $user;
    public $empresa_solicitante;
    public $activos;
    public $filteredActivos;
    public $id_activo;
    public $activoBusqueda;
    public $searchActivo = '';

    public $selectedTipoNombre;
    public $id_tipo;
    public $searchTipo = '';
    public $tipoPrueba;
    public $tipoInicial;

    public $selectedCategoriaNombre;
    public $id_categoria;
    public $searchCategoria = '';
    public $categorias2;
    public $categoria;

    public $selectedSubcategoriaNombre;
    public $id_subcategoria;
    public $searchSubcategoria = '';

    public $subcategoria;
    public $servicios;
    public $filteredServicios;
    public $searchServicio = '';
    public $id_servicio;

    public $actividades;
    public $actividadesEconomicas;
    public $prestadoras;
    public $prestadorasInvitadas;
    public $searchPrestadora = '';
    public $id_prestadora = [];
    public $fav;

    public $fechaHora;
    public $descripcion;
    public $cod;
    public $errorMessage;

    public $tiposServicio;
    public $selectedTipoServicio;
    public $nombre_solicitud;

    public $codigo, $cod_actividad, $searchActividad = '', $filteredActividad = [], $actividad, $selectedActividadNombre;
    public $actividadIncompatible = false, $subcategoriaServicio, $selectedServicioNombre;

    protected $listeners = [
        'setIdActivo',
        'setIdServicio',
        'setIdActividad',
        'activoCreado' => 'actualizarActivos',
    ];

    public function mount()
    {
        $this->datos();
        $this->filteredActivos = collect($this->activos);
        $this->filteredServicios = collect();
        $this->prestadoras = collect();
        $this->prestadorasInvitadas = collect();
        $this->tiposServicio = TiposSolicitudModel::all();
        $this->tipoPrueba   = TiposModel::all();
        $this->tipoInicial  = false;
    }

    public function crearActivos()
    {
        $this->dispatch('createActivos')->to('activos.create-activos');
    }

    // 3) Al recibir activoCreado, recarga y filtra
    public function actualizarActivos($idActivo)
    {
        // recarga completa de activos
        $this->activos = $this->fetchActivos($this->getUserId());

        // si hay subcategoría seleccionada, vuelve a filtrar por ella
        if ($this->id_subcategoria) {
            $this->activos = collect($this->activos)
                ->where('id_subcategoria', $this->id_subcategoria)
                ->where('id_categoria',    $this->id_categoria)
                ->where('id_tipo',         $this->id_tipo)
                ->values();
        }

        $this->filteredActivos = collect($this->activos);

        // seleccionar inmediatamente el nuevo activo
        $this->setIdActivo($idActivo);
    }

    private function datos()
    {
        $this->panel();
        $id = $this->getUserId();
        $this->activos = $this->fetchActivos($id);
    }

    private function panel()
    {
        $this->user = auth()->user();
        if ($this->user->panel_actual == 'Empresa') {
            $this->empresa_solicitante = Session::get('cuitEmpresaSeleccionado')
                ?? (Auth::check() ? Auth::user()->entidad : null);
        } else {
            $this->empresa_solicitante = null;
        }
    }

    private function getUserId()
    {
        $id = session('cuitEmpresaSeleccionado');
        return $id ?: Auth::user()->cuil;
    }

    private function fetchActivos($id)
    {
        $estado = EstadoGeneralModel::where('nombre', 'Baja')
        ->pluck('id_estado_sit_general')->first();

        $query = ActivosModel::where(function ($q) use ($id) {
            $q->where('usuario_titular', (int)$id)
                ->orWhere('empresa_titular', (int)$id);
        })
            ->whereNotNull('id_ubicacion')
            ->whereNot('id_estado_sit_general', $estado);

        if ($this->id_tipo) {
            $query->where('id_tipo', $this->id_tipo);
        }
        if ($this->id_categoria) {
            $query->where('id_categoria', $this->id_categoria);
        }
        if ($this->id_subcategoria) {
            $query->where('id_subcategoria', $this->id_subcategoria);
        }

        return $query->get();
    }

    public function updatedSearchActivo()
    {
        if ($this->searchActivo) {
            $this->filteredActivos = ActivosModel::where('nombre', 'like', "%{$this->searchActivo}%")
                ->where(function ($q) {
                    $q->where('usuario_titular', (int)$this->getUserId())
                        ->orWhere('empresa_titular', (int)$this->getUserId());
                })->get();
        } else {
            $this->filteredActivos = $this->activos;
        }

        if ($this->id_activo && ! $this->filteredActivos->contains('id_activo', $this->id_activo)) {
            $this->reset([
                'id_activo',
                'activoBusqueda',
                'subcategoria',
                'servicios',
                'filteredServicios',
                'id_servicio',
                'prestadoras',
                'prestadorasInvitadas',
                'id_prestadora',
                'searchServicio'
            ]);
        }
    }

    public function setIdActivo($id)
    {
        $this->id_activo = $id;
        $this->activoBusqueda = ActivosModel::find($id);
        $this->searchActivo = '';
        $this->filteredActivos = $this->activos;
        $this->cod_actividad = null;
        $this->servicios = null;
        $this->actividadIncompatible = false;
        $this->cargarDatosServicios();
        $this->dispatch('closeDropdown', ['dropdown' => 'activo']);
    }

    private function cargarDatosServicios()
    {
        if ($this->activoBusqueda) {
            $ServiciosSucategorias = ServiciosSubcategoriasModel::where('id_tipo', $this->activoBusqueda->id_tipo)
                ->where('id_categoria', $this->activoBusqueda->id_categoria)
                ->where('id_subcategoria', $this->activoBusqueda->id_subcategoria)
                ->get();


            $this->codigo = ServiciosActividadesEconomicasModel::whereIn('id_servicio', $ServiciosSucategorias->pluck('id_servicio'))
                ->pluck('cod_actividad')
                ->unique()
                ->toArray();

            $this->actividad = ActividadesEconomicasModel::whereIn('COD_ACTIVIDAD', $this->codigo)
                ->where('estado', 1)
                ->get();

            // dd($this->codigo, $this->servicios);
            $this->filteredActividad = $this->actividad;
        } else {
            $this->actividad = collect();
            $this->filteredActividad = collect();
        }
    }

    public function updatedSearchActividad()
    {
        if ($this->searchActividad) {
            $this->filteredActividad = ActividadesEconomicasModel::where('nombre', 'like', '%' . $this->searchActividad . '%')
                ->whereIn('cod_actividad', $this->actividad->pluck('cod_actividad'))
                ->get();
        } else {
            $this->filteredActividad = $this->actividad;
        }
    }

    public function setIdActividad($cod_actividad)
    {
        $this->cod_actividad = $cod_actividad;
        $this->selectedActividadNombre = ActividadesEconomicasModel::find($this->cod_actividad);
        $this->searchActividad = '';
        $this->servicios = null;
        $this->id_servicio = null;
        $this->actividadIncompatible = false;
        $this->prestadoras = collect();
        $this->prestadorasInvitadas = collect();
        $this->dispatch('closeDropdown', ['dropdown' => 'actividad']);
        $this->cargarDatosEspecialidad();
    }

    private function cargarDatosEspecialidad()
    {
        $this->actividadIncompatible = false;

        if ($this->cod_actividad) {
            $t = $this->activoBusqueda;

            // Buscar servicios relacionados con la actividad
            $idServiciosActividad = ServiciosActividadesEconomicasModel::where('cod_actividad', $this->cod_actividad)
                ->pluck('id_servicio');

            // dd($idServiciosActividad);
            if ($idServiciosActividad->isEmpty()) {
                $this->actividadIncompatible = true;
                $this->id_servicio = '';
                return;
            }

            // Buscar subcategorías relacionadas a los servicios
            $subcategorias = ServiciosSubcategoriasModel::whereIn('id_servicio', $idServiciosActividad)
                ->get();

            if ($subcategorias->isEmpty()) {
                $this->actividadIncompatible = true;
                $this->id_servicio = '';
                return;
            }

            // Verificar compatibilidad con el activo
            $subcategoriasCompatibles = $subcategorias->filter(function ($subcat) use ($t) {
                return $subcat->id_subcategoria == $t->id_subcategoria &&
                    $subcat->id_categoria    == $t->id_categoria &&
                    $subcat->id_tipo         == $t->id_tipo;
            });

            // dd($subcategorias, $t, $subcategoriasCompatibles);
            if ($subcategoriasCompatibles->isEmpty()) {
                // No hay compatibilidad, mostrar mensaje
                $this->actividadIncompatible = true;
                $this->subcategoria = collect();
                $this->servicios = collect();
                $this->filteredServicios = collect();
                return;
            }

            // Si hay compatibilidad, continuar normalmente
            $this->subcategoriaServicio = $subcategoriasCompatibles;
            $idServiciosCompatibles = $subcategoriasCompatibles->pluck('id_servicio')->unique();
            // dd($idServiciosCompatibles);
            $this->servicios = ServiciosModel::whereIn('id_servicio', $idServiciosCompatibles)
                // ->where('cod_actividad', $this->cod_actividad)
                ->get();

            // dd($this->subcategoriaServicio, $this->servicios, $idServiciosCompatibles);
            $this->filteredServicios = $this->servicios;

            $this->dispatch('serviciosActualizados');
        } else {
            $this->subcategoriaServicio = collect();
            $this->servicios = collect();
            $this->filteredServicios = collect();
        }
    }

    public function setTipo($id)
    {
        $tipo = TiposModel::find($id);
        $this->selectedTipoNombre = $tipo?->nombre;
        $this->id_tipo = $id;
        Session::put('tipoNombre', $this->selectedTipoNombre);
        $this->searchTipo = '';
        $this->tipoPrueba = TiposModel::where('nombre', 'like', '%' . $this->searchTipo . '%')->get();
        $this->cargarCategoria($id);
        $this->dispatch('closeDropdown', ['dropdown' => 'tipo']);

        $this->activos = $this->fetchActivos($this->getUserId());
        $this->filteredActivos = collect($this->activos);
        if ($this->id_activo && ! $this->filteredActivos->contains('id_activo', $this->id_activo)) {
            $this->reset(['id_activo', 'activoBusqueda']);
        }
    }

    public function setCategoria($id)
    {
        $cat = CategoriaModel::find($id);
        $this->selectedCategoriaNombre = $cat?->nombre;
        $this->id_categoria = $id;
        $this->searchCategoria = '';
        $this->cargaSubcategoria($id);
        $this->dispatch('closeDropdown', ['dropdown' => 'categoria']);

        $this->activos = $this->fetchActivos($this->getUserId());
        $this->filteredActivos = collect($this->activos);
        if ($this->id_activo && ! $this->filteredActivos->contains('id_activo', $this->id_activo)) {
            $this->reset(['id_activo', 'activoBusqueda']);
        }
    }

    public function cargarCategoria($id)
    {
        if ($id) {
            $this->categorias2 = CategoriaModel::where('id_tipo', $id)->get();
            $this->id_categoria = null;
            $this->selectedCategoriaNombre = null;
            $this->subcategoria = collect();
            $this->id_subcategoria = null;
        } else {
            $this->categorias2 = collect();
            $this->id_categoria = null;
            $this->selectedCategoriaNombre = null;
            $this->subcategoria = collect();
            $this->id_subcategoria = null;
        }
    }

    public function cargaSubcategoria($value)
    {
        $this->categoria = $value;
        $this->loadSubcategorias($value);
    }

    protected function loadSubcategorias($value)
    {
        $this->subcategoria = collect();
        $this->id_subcategoria = null;
        $this->selectedSubcategoriaNombre = null;
        if ($value) {
            $this->subcategoria = SubcategoriaModel::where('id_categoria', $value)
                ->where('id_tipo', $this->id_tipo)
                ->get();
        }
    }

    public function setSubcategoria($id)
    {
        $sub = SubcategoriaModel::find($id);
        $this->selectedSubcategoriaNombre = $sub?->nombre;
        $this->id_subcategoria = $id;
        $this->searchSubcategoria = '';
        $this->dispatch('closeDropdown', ['dropdown' => 'subcategoria']);

        $this->activos = $this->fetchActivos($this->getUserId());
        $this->filteredActivos = collect($this->activos);
        if ($this->id_activo && ! $this->filteredActivos->contains('id_activo', $this->id_activo)) {
            $this->reset(['id_activo', 'activoBusqueda']);
        }
    }

    public function updatedSearchTipo()
    {
        if ($this->searchTipo) {
            $this->tipoPrueba = TiposModel::where('nombre', 'like', '%' . $this->searchTipo . '%')->get();
        } else {
            $this->tipoPrueba = TiposModel::all();
        }
        if ($this->tipoPrueba->isEmpty() || ! $this->tipoPrueba->contains('id_tipo', $this->id_tipo)) {
            $this->id_tipo = null;
            $this->selectedTipoNombre = null;
            $this->categorias2 = collect();
            $this->id_categoria = null;
            $this->selectedCategoriaNombre = null;
            $this->subcategoria = collect();
            $this->id_subcategoria = null;
        }
    }

    public function updatedSearchCategoria()
    {
        if ($this->id_tipo && $this->searchCategoria) {
            $this->categorias2 = CategoriaModel::where('id_tipo', $this->id_tipo)
                ->where('nombre', 'like', '%' . $this->searchCategoria . '%')
                ->get();
        } elseif ($this->id_tipo) {
            $this->categorias2 = CategoriaModel::where('id_tipo', $this->id_tipo)->get();
        } else {
            $this->categorias2 = collect();
        }
        if ($this->categorias2->isEmpty() || ! $this->categorias2->contains('id_categoria', $this->id_categoria)) {
            $this->id_categoria = null;
            $this->selectedCategoriaNombre = null;
            $this->subcategoria = collect();
            $this->id_subcategoria = null;
        }
    }

    public function updatedSearchSubcategoria()
    {
        if ($this->id_categoria && $this->searchSubcategoria) {
            $this->subcategoria = SubcategoriaModel::where('id_categoria', $this->id_categoria)
                ->where('id_tipo', $this->id_tipo)
                ->where('nombre', 'like', '%' . $this->searchSubcategoria . '%')
                ->get();
        } elseif ($this->id_categoria) {
            $this->subcategoria = SubcategoriaModel::where('id_categoria', $this->id_categoria)
                ->where('id_tipo', $this->id_tipo)
                ->get();
        } else {
            $this->subcategoria = collect();
        }
        if ($this->subcategoria->isEmpty() || ! $this->subcategoria->contains('id_subcategoria', $this->id_subcategoria)) {
            $this->id_subcategoria = null;
            $this->selectedSubcategoriaNombre = null;
        }
    }

    public function updatedSearchServicio()
    {
        if ($this->searchServicio) {
            $this->filteredServicios = ServiciosModel::where('nombre', 'like', '%' . $this->searchServicio . '%')
                ->whereIn('id_servicio', $this->subcategoriaServicio->pluck('id_servicio'))
                ->get();
        } else {
            $this->filteredServicios = $this->servicios ?? collect();
        }

        if ($this->id_servicio && !$this->filteredServicios->contains('id_servicio', $this->id_servicio)) {
            $this->reset(['id_servicio', 'prestadoras', 'prestadorasInvitadas', 'id_prestadora']);
        }
    }

    public function setIdServicio($id)
    {
        $this->id_servicio = $id;
        $this->selectedServicioNombre = ServiciosModel::find($this->id_servicio);
        $this->searchServicio = '';
        $this->actividadIncompatible = false;
        $this->prestadoras = collect();
        $this->prestadorasInvitadas = collect();
        $this->prestadora();
        $this->dispatch('closeDropdown', ['dropdown' => 'servicio']);
    }

    public function prestadora()
    {
        if ($this->id_servicio) {
            $this->actividades = ServiciosActividadesEconomicasModel::where('id_servicio', $this->id_servicio)->get();
            $this->actividadesEconomicas = EmpresasModel::whereIn('COD_ACTIVIDAD', $this->actividades->pluck('cod_actividad'))->get();
            $actividadesCuit = $this->actividadesEconomicas->pluck('cuit')->toArray();
            $this->fav = MisProveedoresModel::where('id_usuario', auth()->id())
                ->where('existe_en_la_plataforma', 'Si')
                ->get();
            $favCuit = $this->fav->pluck('cuit')->toArray();
            $prestadorasCuit = array_intersect($actividadesCuit, $favCuit);

            if (!empty($prestadorasCuit)) {
                $this->prestadoras = EmpresasModel::whereIn('cuit', $prestadorasCuit)
                    ->when($this->searchPrestadora, function ($query) {
                        $query->where('razon_social', 'like', '%' . $this->searchPrestadora . '%');
                    })
                    ->get();

                $this->prestadorasInvitadas = MisProveedoresModel::where('id_usuario', auth()->id())
                    ->whereNotIn('cuit', $this->prestadoras->pluck('cuit'))
                    ->where('existe_en_la_plataforma', 'No')
                    ->get();
            } else {
                $this->prestadoras = collect();
                $this->prestadorasInvitadas = collect();
            }
        } else {
            $this->prestadoras = collect();
            $this->prestadorasInvitadas = collect();
        }
    }

    public function updatedSearchPrestadora()
    {
        $this->prestadora();
    }

    public function removePrestadora($cuit)
    {
        if (($key = array_search($cuit, $this->id_prestadora)) !== false) {
            unset($this->id_prestadora[$key]);
            $this->id_prestadora = array_values($this->id_prestadora);
            $this->prestadora();
        }
    }

    public function getPrestadorasSeleccionadasProperty()
    {
        if (!empty($this->id_prestadora)) {
            return EmpresasModel::whereIn('cuit', $this->id_prestadora)->get();
        } else {
            return collect();
        }
    }

    public function render()
    {
        return view('livewire.servicios.servicios-vista-formulario');
    }

    public function save()
    {
        $this->validacion();
        if ($this->fechaEsValida()) {
            $this->panel();
            DB::beginTransaction();
            try {
                $this->crearRegistro();
                $this->actualizarEstado();
                DB::commit();
                $this->dispatch('lucky');
                $this->close();
                $this->dispatch('render');
                redirect()->route('usuarios-servicios');
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errorMessage = 'Ocurrió un error al guardar la solicitud: ' . $e->getMessage();
                $this->dispatch('errorInfo', [
                    'title'   => 'Error al Guardar',
                    'message' => $this->errorMessage
                ]);
            }
        }
    }


    protected $messages = [
        'nombre_solicitud.required' => 'El nombre de la solicitud es obligatorio.',
        'fechaHora.required'        => 'La fecha y hora son obligatorias.',
        'id_activo.required'        => 'Debe seleccionar un activo.',
        'id_servicio.required'      => 'Debe seleccionar un servicio.',
        'id_prestadora.required'    => 'Debe invitar al menos una prestadora.',
        'descripcion.required'      => 'La descripción es obligatoria.',
    ];

    private function actualizarEstado()
    {
        $this->activoBusqueda->save();
    }

    private function validacion()
    {
        $this->validate([
            'nombre_solicitud'  => 'required|string|max:255',
            'fechaHora'         => 'required|date',
            'id_activo'         => 'required',
            'id_servicio'       => 'required',
            'id_prestadora'     => 'required|array|min:1|max:5',
            'descripcion'       => 'required|max:200',
        ], $this->messages);
    }

    private function fechaEsValida()
    {
        $fechaIngresada = Carbon::parse($this->fechaHora);
        $fechaActual = Carbon::now();

        if ($fechaIngresada->lt($fechaActual)) {
            $this->errorMessage = 'La fecha y hora no pueden ser anteriores a la fecha y hora actual.';
            session()->flash('error', $this->errorMessage);
            return false;
        }
        return true;
    }

    private function crearRegistro()
    {
        $fechaHoraFormateada = $this->formatoFecha($this->fechaHora);
        foreach ($this->id_prestadora as $prestadora) {
            SolicitudesServiciosModel::create([
                'id_servicio'        => $this->id_servicio,
                'id_activo'          => $this->activoBusqueda->id_activo,
                'id_tipo'            => $this->activoBusqueda->id_tipo,
                'id_categoria'       => $this->activoBusqueda->id_categoria,
                'id_subcategoria'    => $this->activoBusqueda->id_subcategoria,
                'empresa_prestadora' => $prestadora,
                'empresa_solicitante' => $this->empresa_solicitante,
                'id_solicitante'     => $this->user->id,
                'fechaHora'          => $fechaHoraFormateada,
                'descripcion'        => $this->descripcion,
                'estado_presupuesto' => 'Esperando confirmación de prestadora',
                'Nombre_solicitud'   => $this->nombre_solicitud,
                'id_tipo_solicitud'  => $this->selectedTipoServicio,
            ]);
        }
    }

    private function formatoFecha($fecha)
    {
        return date('Y-m-d H:i:s', strtotime($fecha));
    }

    public function close()
    {
        $this->reset([
            'id_servicio',
            'descripcion',
            'id_activo',
            'id_prestadora',
            'fechaHora',
            'cod',
            'errorMessage',
            'searchPrestadora',
            'searchActivo',
            'searchServicio'
        ]);

        $this->filteredServicios = collect();
        $this->filteredActivos = $this->activos;
        $this->prestadoras = collect();
        $this->prestadorasInvitadas = collect();
        $this->activoBusqueda = null;
        $this->subcategoria = collect();
        $this->servicios = collect();
        $this->actividades = null;
        $this->actividadesEconomicas = collect();
    }
}
