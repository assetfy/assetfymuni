<?php

namespace App\Livewire\Servicios;

use App\Models\ActivosModel;
use App\Models\EmpresasModel;
use App\Models\ServiciosActividadesEconomicasModel;
use App\Models\ServiciosModel;
use App\Models\ServiciosSubcategoriasModel;
use App\Models\SolicitudesServiciosModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PrestadoraSolicitudCotizacion extends Component
{
    protected $listeners = ['solicitarCotizacion'];

    public $prestadora, $servicio, $activos, $user, $empresa_solicitante, $id_activo, $activoBusqueda, $searchActivo, $filteredActivos, $errorMessage,$nombreServicio,$serviciosSubcategoria,$fechaHora,$descripcion;
    public $open = false;

    public function mount()
    {
        $this->activos = collect();
    }

    private function validacion()
    {
        $this->validate([
            'fechaHora' => 'required|date',
            'descripcion'  => 'required|max:200',
        ]);
    }

    public function solicitarCotizacion($data)
    {
        $this->prestadora = EmpresasModel::find($data);
        $codActividad = $this->prestadora->pluck('COD_ACTIVIDAD')->first();
        if ($codActividad !== null) {
            // Si COD_ACTIVIDAD no es null, ejecuta el código
            $this->servicio = ServiciosActividadesEconomicasModel::where('cod_actividad', $codActividad)->get();
            $this->datos();
            $this->open = true;
        } else {
            // Si COD_ACTIVIDAD es null, dispara el error y cierra
            $this->dispatch('errorActividad');
            $this->open = false;
        }
    }

    private function getUserId()
    {
        $id = session('cuitEmpresaSeleccionado');
        if ($id == null) {
            $id = auth()->user()->cuil;
        }
        return $id;
    }

    private function datos()
    {
        $this->panel();
        $id = $this->getUserId(); // Obtiene el ID del usuario
        $this->nombreServicio = ServiciosModel::where('id_servicio',$this->servicio->pluck('id_servicio'))->first();
        $this->serviciosSubcategoria = ServiciosSubcategoriasModel::where('id_servicio', $this->nombreServicio->id_servicio )->first();
        $this->activos = $this->fetchActivos($id); // Obtiene los activos del usuario
        $this->filteredActivos = collect($this->activos);
        
    }


    private function fetchActivos($id)
    {
        return ActivosModel::where(function ($query) use ($id) {
            // Filtra por usuario_titular o empresa_titular
            $query->where('usuario_titular', (int)$id)
                ->orWhere('empresa_titular', (int)$id);
        })
            ->whereNotNull('id_ubicacion') // Asegúrate de que id_ubicacion no sea null
            ->where('id_estado_sit_general', 1) // Asegúrate de que id_estado_sit_general sea igual a 1
            ->where('id_tipo',  $this->serviciosSubcategoria->id_tipo)
            ->where('id_categoria',  $this->serviciosSubcategoria->id_categoria)
            ->where('id_subcategoria',  $this->serviciosSubcategoria->id_subcategoria)
            ->get();
    }

    public function setIdActivo($id)
    {
        $this->id_activo = $id;
        $this->activoBusqueda = ActivosModel::find($id);
        $this->searchActivo = '';
        $this->filteredActivos = $this->activos; // Resetea filteredActivos a todos los activos
        $this->dispatch('closeDropdown', ['dropdown' => 'activo']);
    }

    public function updatedSearchActivo()
    {
        if ($this->searchActivo) {
            $this->filteredActivos = ActivosModel::where('nombre', 'like', '%' . $this->searchActivo . '%')
                ->where(function ($query) {
                    $query->where('usuario_titular', (int)$this->getUserId())
                        ->orWhere('empresa_titular', (int)$this->getUserId());
                })
                ->get();
        } else {
            $this->filteredActivos = $this->activos;
        }
        // Si la búsqueda no coincide con el Activo seleccionado, resetea la selección
        if ($this->id_activo && !$this->filteredActivos->contains('id_activo', $this->id_activo)) {
            $this->reset([
                'id_activo',
                'activoBusqueda',
            ]);
        }
    }

    public function save()
    {
        if ($this->fechaEsValida()) {
            $this->validacion();
            $this->panel();
            DB::beginTransaction();
            try {
                $this->crearRegistro();
                $this->actualizarEstado();
                DB::commit();
                $this->dispatch('lucky');
            } catch (\Exception $e) {
                DB::rollBack();
                $this->dispatch('errorServicio');
            }
        }
        $this->close();
    }

    private function fechaEsValida()
    {
        $fechaIngresada = Carbon::parse($this->fechaHora);
        $fechaActual = Carbon::now();

        if ($fechaIngresada->lt($fechaActual)) {
            $this->errorMessage = 'La fecha y hora ingresadas no pueden ser anteriores a la fecha y hora actual.';
            session()->flash('error', $this->errorMessage);
            return false;
        }
        return true;
    }

    private function actualizarEstado()
    {
        $this->activoBusqueda->id_estado_sit_general = '3';
        $this->activoBusqueda->save();
    }

    private function crearRegistro()
    {
        $fechaHoraFormateada = $this->formatoFecha($this->fechaHora);
        SolicitudesServiciosModel::create([
            'id_servicio'         => $this->servicio->value('id_servicio'),
            'id_activo'           => $this->activoBusqueda->id_activo,
            'id_tipo'             => $this->activoBusqueda->id_tipo,
            'id_categoria'        => $this->activoBusqueda->id_categoria,
            'id_subcategoria'     => $this->activoBusqueda->id_subcategoria,
            'empresa_prestadora'  => $this->prestadora->value('cuit'),
            'empresa_solicitante' => $this->empresa_solicitante,
            'id_solicitante'      => $this->user->id,
            'fechaHora'           => $fechaHoraFormateada,
            'descripcion'         => $this->descripcion,
            'estado_presupuesto'  => 'Esperando confirmación de prestadora',
        ]);
    }


    private function formatoFecha($fecha)
    {
        return date('Y-m-d H:i:s', strtotime($fecha));
    }


    private function panel()
    {
        $this->user = auth()->user();
        if ($this->user->panel_actual == 'Empresa') {
            $this->empresa_solicitante = Session::get('cuitEmpresaSeleccionado') ?? (Auth::check() ? Auth::user()->entidad : null);
        } else {
            $this->empresa_solicitante = null;
        }
    }

    public function render()
    {
        return view('livewire.servicios.prestadora-solicitud-cotizacion');
    }

    public function close()
    {
        $this->reset([
            'prestadora','id_activo',
        ]);
        $this->open = false;
    }
}
