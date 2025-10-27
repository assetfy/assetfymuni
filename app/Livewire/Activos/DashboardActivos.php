<?php

namespace App\Livewire\Activos;

use Illuminate\Support\Facades\Auth;
use App\Traits\SortableTrait;
use Livewire\WithPagination;
use App\Models\ActivosModel;
use App\Helpers\IdHelper;
use App\Models\TiposModel;
use Livewire\Component;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;

class DashboardActivos extends Component
{
    use SortableTrait, WithPagination;

    public $search = "";
    public $tipos, $open;
    public $idTipoSeleccionado, $activoBusqueda, $id_ubicacion;
    public $id_tipo;
    protected $listeners = ['render'];
    public $reabrirCrearServicio = false; // Bandera para abrir el modal de servicio


    public function mount($id_tipo = null, $id_ubicacion = null)
    {
        $this->id_tipo = $id_tipo ?: $this->id_tipo; // Si id_tipo no es null, se asigna
        $this->id_ubicacion = $id_ubicacion;
        Session::put('tipo', null); // Asigna el valor de la vble recibida por parametro a tipo
        $this->tipos = $this->getTipos();
        $this->busqueda($id_tipo, $id_ubicacion);
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function busqueda($id_tipo = null, $id_ubicacion = null)
    {
        $this->idTipoSeleccionado = $id_tipo;

        $query = ActivosModel::query();

        if ($id_tipo) {
            $query->where('id_tipo', $id_tipo);
        }

        if ($id_ubicacion) {
            $query->where('id_ubicacion', $id_ubicacion);
        }

        $this->activoBusqueda = $query->get();
    }

    public function render()
    {
        $activos = $this->coleccionActivos();

        return view('livewire.activos.dashboard-activos', [
            'activos' => $activos,
            'tipos' => $this->tipos,
        ]);
    }

    private function coleccionActivos()
    {
        $user = Auth::user();

        $query = ActivosModel::with('tipo', 'ubicacion')
            ->where('nombre', 'like', '%' . $this->search . '%');

        if ($this->id_tipo) {
            $query->where('id_tipo', $this->id_tipo);
        }

        if ($this->id_ubicacion) {
            $query->where('id_ubicacion', $this->id_ubicacion);
        }

        if ($user->panel_actual === 'Usuario') {
            $query->where('usuario_titular', $user->cuil)
                ->whereHas('estadoGeneral', function ($query) {
                    $query->where('nombre', '!=', 'Baja');
                });
        } else {
            $query->where('empresa_titular', IdHelper::idEmpresa())
                ->whereHas('estadoGeneral', function ($query) {
                    $query->where('nombre', '!=', 'Baja');
                });
        }

        return $query->paginate(8);
    }

    private function getTipos()
    {
        return TiposModel::all();
    }

    public function openCreateControlModal($activo)
    {
        $this->dispatch('openModal', ['activoId' => $activo])->to('controles.activoscontroles.create-controles-activos');
    }

    public function openCreateAtributoModal($activo)
    {
        $this->open = true;
        $this->dispatch('openModal', ['activoId' => $activo])->to('atributos.nuevosatributos.create-nuevo-atributos-activos');
    }

    public function openServiciosActivosModal($activo)
    {
        if (!empty($activo['id_ubicacion'])) {
            // Abrir directamente el modal de servicios
            $this->dispatch('openModal', ['activoId' => $activo])->to('servicios.activos.crear-solicitud-servicio');
        } else {
            // Bandera para reabrir modal de servicios después de crear/cambiar ubicación
            $this->reabrirCrearServicio = true;
            // Abrir el modal para crear/cambiar ubicación
            $activoCollection = collect($activo);
            $activoId = $activoCollection['id_activo'];

            Cache::put('abrirModalServicio', [
                'estado' => true,
                'activoCompleto' =>  $activo,
                'id_activo' =>  $activoId

            ], now()->addMinutes(2)); // Cache válida por 2 minutos
            $this->dispatch('openModalCambiarUbicacion', ['activo' => $activoId])->to('ubicaciones.cambiar-ubicacion');
        }
    }

    public function openModalCambiarUbicacion($activo)
    {
        $this->dispatch('openModalCambiarUbicacion', ['activo' => $activo])->to('ubicaciones.cambiar-ubicacion');
    }

    public function openEditarActivo($activo)
    {
        $this->dispatch('editActivos', ['activoId' => $activo])->to('activos.edit-activos');
    }

    private function getUserId()
    {
        $id = session('cuitEmpresaSeleccionado');
        if ($id == null) {
            $id = auth()->user()->cuil;
        }
        return $id;
    }

    public function crearActivos(){
        $this->dispatch('createActivos')->to('activos.create-activos');
    }
}
