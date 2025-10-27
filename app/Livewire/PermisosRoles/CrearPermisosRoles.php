<?php

namespace App\Livewire\PermisosRoles;

use App\Models\UsuariosEmpresasModel;
use App\Services\MiddlewareInvoker;
use App\Models\PermisosRolesModel;
use App\Traits\VerificacionTrait;
use App\Models\PermisosModel;
use App\Models\RolesModel;
use App\Helpers\IdHelper;
use Livewire\Component;

class CrearPermisosRoles extends Component
{
    use VerificacionTrait;

    public $nombre, $id_rol, $id_permisos = [];
    public $cuit, $usuario_empresa, $id_relacion_empresa;
    public $datosEmpresa, $datos, $roles;
    public $open = false;

    public $buscarAsignados = '';
    public $buscarDisponibles = '';

    public $permisosAsignados = [];
    public $permisosDisponibles = [];

    protected $listeners = ['CrearPermisosRoles'];

    public function CrearPermisosRoles()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->mount();
            $this->open = true;
        }
    }

    public function mount()
    {
        $this->obtenerDatos();
    }

    private function obtenerDatos()
    {
        $this->datos = auth()->user();

        if (in_array($this->datos->panel_actual, ['Empresa', 'Prestadora', 'Estado', 'Controladora'])) {
            $cuit = IdHelper::idEmpresa();
            $this->datosEmpresa = UsuariosEmpresasModel::where('cuit', $cuit)
                ->where('id_usuario', $this->datos->id)
                ->first();

            if ($this->datosEmpresa) {
                $this->cuit = $this->datosEmpresa->cuit;
                $this->usuario_empresa = $this->datosEmpresa->id_usuario;
                $this->id_relacion_empresa = $this->datosEmpresa->id_relacion;
            } else {
                $this->cuit = null;
                $this->usuario_empresa = null;
                $this->id_relacion_empresa = null;
            }
        } else {
            $this->cuit = null;
            $this->usuario_empresa = null;
            $this->id_relacion_empresa = null;
        }
        $this->roles = RolesModel::where(function ($query) {
            $query->where('tipo_empresa', IdHelper::empresaActual()->tipo)
                ->orWhereNull('cuit');
        })->get();
    }

    public function seleccionarRol($idRol)
    {
        $this->id_rol = $idRol;
        $this->buscarAsignados = '';
        $this->buscarDisponibles = '';
        $this->id_permisos = PermisosRolesModel::where('id_rol', $this->id_rol)->pluck('id_permiso')->toArray();

        $this->actualizarPermisos();
    }

    private function actualizarPermisos()
    {
        if ($this->id_rol) {
            $this->permisosAsignados = PermisosModel::whereIn('id_permiso', $this->id_permisos)
                ->when($this->buscarAsignados, function ($query) {
                    $query->where('nombre', 'like', '%' . $this->buscarAsignados . '%');
                })
                ->get();

            $this->permisosDisponibles = PermisosModel::whereNotIn('id_permiso', $this->id_permisos)
                ->when($this->buscarDisponibles, function ($query) {
                    $query->where('nombre', 'like', '%' . $this->buscarDisponibles . '%');
                })
                ->get();
        } else {
            $this->id_permisos = [];
            $this->permisosAsignados = collect();
            $this->permisosDisponibles = collect();
        }
    }

    public function updatedBuscarAsignados()
    {
        $this->actualizarPermisos();
    }

    public function updatedBuscarDisponibles()
    {
        $this->actualizarPermisos();
    }

    protected $rules = [
        'id_rol' => 'required',
        'id_permisos' => 'array',
    ];

    public function save()
    {
        $this->obtenerDatos();
        $this->validate();

        $permisosActuales = PermisosRolesModel::where('id_rol', $this->id_rol)->pluck('id_permiso')->toArray();

        $permisosAgregar = array_diff($this->id_permisos, $permisosActuales);
        $permisosEliminar = array_diff($permisosActuales, $this->id_permisos);

        foreach ($permisosAgregar as $id_permiso) {
            $valoresNuevos = [
                'id_rol' => $this->id_rol,
                'id_permiso' => $id_permiso,
                'cuit_empresa' => IdHelper::idEmpresa(),
            ];
            PermisosRolesModel::create($valoresNuevos);
        }
        if (!empty($permisosEliminar)) {
            PermisosRolesModel::where('id_rol', $this->id_rol)
                ->whereIn('id_permiso', $permisosEliminar)
                ->delete();
        }

        $this->dispatch('lucky');
        $this->dispatch('refreshLivewireTable');
        $this->close();
    }

    public function close()
    {
        $this->reset([
            'nombre',
            'id_rol',
            'id_permisos',
            'cuit',
            'usuario_empresa',
            'id_relacion_empresa',
            'buscarAsignados',
            'buscarDisponibles',
            'permisosAsignados',
            'permisosDisponibles'
        ]);
        $this->open = false;
    }

    public function render()
    {
        $this->actualizarPermisos();
        return view('livewire.permisos-roles.crear-permisos-roles');
    }
}
