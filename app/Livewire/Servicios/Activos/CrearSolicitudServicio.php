<?php

namespace App\Livewire\Servicios\Activos;

use App\Models\ActividadesEconomicasModel;
use App\Models\ServiciosActividadesEconomicasModel;
use Illuminate\Validation\ValidationException;
use App\Models\ServiciosSubcategoriasModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\EmpresasActividadesModel;
use Illuminate\Support\Facades\Session;
use App\Models\EmpresasModel;
use App\Models\ActivosModel;
use App\Models\MisProveedoresModel;
use App\Models\ServiciosActivosModel;
use App\Models\ServiciosModel;
use App\Models\TiposSolicitudModel;
use Livewire\Component;
use Carbon\Carbon;
use Illuminate\Support\Str;


class CrearSolicitudServicio extends Component
{
    public $servicios, $prestadoras, $subcategoria;
    public $id_servicio, $id_activo;
    public $id_prestadora = []; // Cambio para múltiples selecciones
    public $descripcion, $fechaHora;
    public $open = false;
    public $searchServicio;
    public $filteredServicios;
    public $activo;
    public $empresa_solicitante;
    public $user;
    public $empresaActividad;
    public $codActividad;
    public $tiposServicio;
    public $selectedTipoServicio;
    public $nombre_solicitud;
    public $actividades;
    public $actividadesEconomicas, $COD_ACTIVIDAD, $selectedEspecializacion;
    public $fav;
    public $prestadorasInvitadas;
    public $searchPrestadora;
    public $especializacion         = [];
    public $filteredEspecializacion = [];
    public $searchEspecializacion   = '';
    public $selectedEspecializacionNombre;
    public $selectedActividadNombre;

    protected $listeners = ['openModal', 'solicitarServicios'];

    protected $rules = [
        'id_servicio'    => 'required',
        'id_prestadora'  => 'required|array|min:1',  // Validación para array
        'descripcion'    => 'required|max:100',
        'fechaHora'      => 'required|date',
    ];

    public function mount()
    {
        $this->servicios = collect();
        $this->prestadoras = collect();
        $this->filteredServicios = collect();
        $this->filteredServicios = collect();
        $this->tiposServicio = TiposSolicitudModel::all();
        $this->id_prestadora = [];
    }

    private function cargarDatosServicios()
    {
        if ($this->activo) {
            $tipo_id = $this->activo->id_tipo;
            $categoria_id = $this->activo->id_categoria;
            $subcategoria_id = $this->activo->id_subcategoria;

            $this->subcategoria = ServiciosSubcategoriasModel::where([
                ['id_subcategoria', $subcategoria_id],
                ['id_categoria', $categoria_id],
                ['id_tipo', $tipo_id],
            ])->get();

            $codActividad =  ServiciosActividadesEconomicasModel::whereIn('id_servicio',  $this->subcategoria->pluck('id_servicio'))
                ->pluck('cod_actividad')
                ->unique()
                ->toArray();

            $this->servicios = ActividadesEconomicasModel::whereIn('COD_ACTIVIDAD', $codActividad)
                ->where('estado', 1)
                ->get();


            $this->filteredServicios = $this->servicios;
        } else {
            $this->subcategoria = collect();
            $this->servicios = collect();
            $this->filteredServicios = collect();
        }
    }

    public function updatedSearchEspecializacion()
    {
        $term = trim($this->searchEspecializacion);
        // Nos aseguramos de trabajar siempre con un array
        $source = $this->especializacion instanceof \Illuminate\Support\Collection
            ? $this->especializacion->toArray()
            : $this->especializacion;
        if ($term !== '') {
            $search = mb_strtolower($term);
            // Filtramos sobre el array
            $filtered = array_filter($source, function (array $item) use ($search) {
                return mb_stripos(mb_strtolower($item['nombre']), $search) !== false;
            });
            // Reindexamos para que queden 0,1,2...
            $this->filteredEspecializacion = array_values($filtered);
        } else {
            // Si borran el input, restauramos todo el listado
            $this->filteredEspecializacion = $source;
        }
    }

    public function updatedSearchServicio()
    {
        if ($this->searchServicio) {
            $search = Str::lower($this->searchServicio);

            // Filtramos la colección ya cargada
            $this->filteredServicios = $this->servicios->filter(function ($servicio) use ($search) {
                // stripos es más rápido que strtolower+contains, pero Str::contains también vale
                return stripos($servicio->nombre, $search) !== false;
            });
        } else {
            // Sin término de búsqueda, volvemos al listado completo
            $this->filteredServicios = $this->servicios ?? collect();
        }

        // Si el servicio seleccionado ya no está en el listado filtrado, reseteamos selects
        if ($this->id_servicio && ! $this->filteredServicios->contains('id_servicio', $this->id_servicio)) {
            $this->reset(['id_servicio', 'id_prestadora', 'prestadoras', 'prestadorasInvitadas', 'filteredEspecializacion']);
        }
    }

    public function setIdServicio($COD_ACTIVIDAD)
    {
        $this->id_servicio = $COD_ACTIVIDAD;
        $this->selectedActividadNombre = ActividadesEconomicasModel::find($this->id_servicio);
        $this->selectedEspecializacion = '';
        $this->searchServicio = '';
        $this->prestadoras = collect();
        $this->prestadorasInvitadas = collect();
        $this->especializacion();
        $this->dispatch('closeDropdown', ['dropdown' => 'servicio']);
    }

    private function especializacion()
    {
        // 1) Array de IDs
        $serviciosSub = ServiciosActividadesEconomicasModel::where('cod_actividad', $this->id_servicio)
            ->pluck('id_servicio')
            ->unique()
            ->toArray();
        // 2) Traes la colección y la conviertes a array
        $this->especializacion = ServiciosModel::whereIn('id_servicio', $serviciosSub)
            ->get()
            ->toArray();
        // 3) Inicializas el array filtrado para que empiece mostrando todo
        $this->filteredEspecializacion = $this->especializacion;
    }


    public function setIdEspecializacion($id_servicio)
    {
        // 1) Guarda el ID de especialización
        $this->selectedEspecializacion = $id_servicio;
        // 2) Encuentra el nombre en el array original
        foreach ($this->especializacion as $item) {
            if ($item['id_servicio'] == $id_servicio) {
                $this->selectedEspecializacionNombre = $item['nombre'];
                break;
            }
        }
        // 3) Limpia el buscador y restablece el listado
        $this->searchEspecializacion      = '';
        $this->filteredEspecializacion    = $this->especializacion;
        $this->prestadora();
        // 4) Cierra el dropdown
        $this->dispatch('closeDropdown', ['dropdown' => 'especializacion']);
    }

    public function prestadora()
    {

        if ($this->id_servicio) {
            // 2. Obtener las actividades económicas relacionadas
            $this->actividadesEconomicas = EmpresasModel::where('COD_ACTIVIDAD', $this->id_servicio)->get();
            $actividadesCuit = $this->actividadesEconomicas->pluck('cuit')->toArray();
            $this->fav = MisProveedoresModel::where('id_usuario', auth()->id())
                ->where('existe_en_la_plataforma', 'Si')
                ->get();
            $favCuit = $this->fav->pluck('cuit')->toArray();
            $prestadorasCuit = array_intersect($actividadesCuit, $favCuit);
            // 3. Obtener los CUITs de las actividades económicas
            $actividadesCuit = $this->actividadesEconomicas->pluck('cuit')->toArray();
            // 4. Obtener las prestadoras favoritas que existen en la plataforma
            $this->fav = MisProveedoresModel::where('id_usuario', auth()->id())
                ->where('existe_en_la_plataforma', 'Si')
                ->get();
            // 5. Filtrar los CUITs de las prestadoras favoritas que están en las actividades económicas
            $favCuit = $this->fav->pluck('cuit')->toArray();
            $prestadorasCuit = array_intersect($actividadesCuit, $favCuit);

            if (!empty($prestadorasCuit)) {
                // 6. Obtener las prestadoras registradas que están en los favoritos y actividades económicas
                $this->prestadoras = EmpresasModel::whereIn('cuit', $prestadorasCuit)
                    ->when($this->searchPrestadora, function ($query) {
                        $query->where('razon_social', 'like', '%' . $this->searchPrestadora . '%');
                    })
                    ->get();
                // 7. Obtener las prestadoras invitadas que no están en las prestadoras registradas
                $this->prestadorasInvitadas = MisProveedoresModel::where('id_usuario', auth()->id())
                    ->whereNotIn('cuit', $this->prestadoras->pluck('cuit'))
                    ->where('existe_en_la_plataforma', 'No')
                    ->get();
            } else {
                // Si no hay CUITs en común, limpiar las colecciones
                $this->prestadoras = collect();
                $this->prestadorasInvitadas = collect();
            }
        } else {
            // Si no hay servicio seleccionado, limpiar las colecciones
            $this->prestadoras = collect();
            $this->prestadorasInvitadas = collect();
        }
    }

    public function panel()
    {
        $this->user = auth()->user();
        $this->empresa_solicitante = $this->user->panel_actual == 'Empresa'
            ? Session::get('cuitEmpresaSeleccionado') ?? ($this->user->entidad ?? null)
            : null;
    }

    public function solicitarServicios($data)
    {
        $this->activo = ActivosModel::find($data);

        // Verificar si el activo existe y tiene un id_ubicacion no nulo
        if ($this->activo && !is_null($this->activo->id_ubicacion)) {
            $this->cargarDatosServicios();
            $this->open = true;
        } else {
            // manejar el caso en que no se cumpla la condición
            $this->dispatch('errorUbicacion');
        }
    }

    public function openModal($data)
    {
        $activoId = $data['activoId']['id_activo'];
        $this->id_activo = ActivosModel::find($activoId);
        if ($this->id_activo) {
            $this->activo = $this->id_activo;
            $this->cargarDatosServicios();
            $this->open = true;
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();
        $this->panel();
        $this->validarFechaHora();
        $this->registrarServicios();
        $this->actualizarEstado();
        $this->close();
    }

    private function registrarServicios()
    {
        $descripcionLimpia = htmlspecialchars($this->descripcion);
        $fechaHoraFormateada = Carbon::parse($this->fechaHora)->format('Y-m-d H:i:s');
        foreach ($this->id_prestadora as $prestadora) {
            SolicitudesServiciosModel::create([
                'id_servicio'        =>  $this->selectedEspecializacion,
                'id_activo'          => $this->activo->id_activo,
                'id_tipo'            => $this->activo->id_tipo,
                'id_categoria'       => $this->activo->id_categoria,
                'id_subcategoria'    => $this->activo->id_subcategoria,
                'empresa_prestadora' => $prestadora,
                'empresa_solicitante' => $this->empresa_solicitante,
                'id_solicitante'     => $this->user->id,
                'fechaHora'          => $fechaHoraFormateada,
                'descripcion'        => $descripcionLimpia,
                'estado_presupuesto' => 'Esperando confirmación de prestadora',
                'Nombre_solicitud'   => $this->nombre_solicitud,
                'id_tipo_solicitud'  => $this->selectedTipoServicio,
            ]);
        }
        $this->dispatch('lucky');
    }


    private function actualizarEstado()
    {
        $this->activo->save();
    }

    private function validarFechaHora()
    {
        if (Carbon::parse($this->fechaHora)->lt(Carbon::now())) {
            throw ValidationException::withMessages([
                'fechaHora' => 'La fecha y hora no pueden ser anteriores a la fecha y hora actuales.',
            ]);
        }
    }

    public function close()
    {
        $this->dispatch('refreshLivewireTable');
        $this->open = false;
        $this->reset([
            'id_servicio',
            'descripcion',
            'id_prestadora',
            'fechaHora',
        ]);
    }

    public function render()
    {
        return view('livewire.servicios.activos.crear-solicitud-servicio');
    }
}
