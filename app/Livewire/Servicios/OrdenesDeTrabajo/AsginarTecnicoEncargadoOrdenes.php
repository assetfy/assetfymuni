<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;

use App\Helpers\IdHelper;
use App\Models\OrdenesModel;
use App\Models\UsuariosEmpresasModel;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AsginarTecnicoEncargadoOrdenes extends Component
{
    protected $listeners = ['openASignarTecnicoEncargadoOrdenes'];

    public $open = false;
    public $orden;
    public $tecnicos = [];
    public $searchTecnico = '';
    public $selectedTecnicoId;
    public $selectedTecnicoName, $idrelacion, $tecnicoActual;

    public function render()
    {
        return view('livewire.servicios.ordenes-de-trabajo.asginar-tecnico-encargado-ordenes');
    }

    public function openASignarTecnicoEncargadoOrdenes($data)
    {
        if (! MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        // 1) Cargo la orden con su técnico asignado (eager load de la relación 'tecnico.usuarios')
        $this->orden = OrdenesModel::with('tecnico.usuarios')
            ->findOrFail($data);
        // 2) Si ya tiene un técnico asignado, lo precargo
        if ($this->orden->tecnico) {
            $this->tecnicoActual        = optional($this->orden->tecnico->usuarios)->name;
            $this->selectedTecnicoId    = $this->orden->tecnico->id_usuario;
            $this->selectedTecnicoName  = $this->tecnicoActual;
        } else {
            $this->tecnicoActual       = null;
            $this->selectedTecnicoId   = null;
            $this->selectedTecnicoName = null;
        }
        // 3) Reinicio búsqueda y abro modal
        $this->searchTecnico        = '';
        $this->open                 = true;
        // 4) Cargo la lista completa de técnicos disponibles
        $this->cargarTecnicos();
    }

    protected function cargarTecnicos()
    {
        $this->tecnicos = UsuariosEmpresasModel::with('usuarios')
            ->where('cuit', IdHelper::idEmpresa())
            ->where('es_representante_tecnico', 'Si')
            ->where('estado', '!=', 'Deshabilitado')
            ->when($this->searchTecnico, function ($query) {
                $query->whereHas('usuarios', function ($q) {
                    $q->where('name', 'like', '%' . $this->searchTecnico . '%');
                });
            })
            ->get();
    }

    public function updatedSearchTecnico($value)
    {
        $this->cargarTecnicos();
    }

    public function setTecnico($idUsuario)
    {
        $usuarioEmpresa = UsuariosEmpresasModel::with('usuarios')
            ->where('cuit', IdHelper::idEmpresa())
            ->where('id_usuario', $idUsuario)
            ->where('es_representante_tecnico', 'Si')
            ->first();


        if ($usuarioEmpresa) {
            $this->idrelacion = $usuarioEmpresa->id_relacion;
            $this->selectedTecnicoId   = $usuarioEmpresa->id_usuario;
            $this->selectedTecnicoName = $usuarioEmpresa->usuarios->name;
        }
    }

    public function asignarTecnico()
    {
        DB::beginTransaction();

        try {
            // Asignar valores
            $this->orden->representante_tecnico   = $this->selectedTecnicoId;
            $this->orden->id_relacion_usuario     = $this->idrelacion;
            $this->orden->save();

            DB::commit();

            $this->dispatch('Exito', [
                'title'   => 'Técnico asignado',
                'message' => 'El técnico ha sido asignado correctamente.'
            ]);

            $this->open = false;
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('errorInfo', [
                'title'   => 'Error al asignar técnico',
                'message' => $e->getMessage()
            ]);
        }
        $this->dispatch('refreshLivewireTable');
    }
}
