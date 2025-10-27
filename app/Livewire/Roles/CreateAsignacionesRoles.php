<?php

namespace App\Livewire\Roles;

use App\Helpers\IdHelper;
use App\Models\RolesModel;
use App\Models\EmpresasModel;
use App\Models\PermisosRolesModel;
use App\Models\UsuariosEmpresasModel;
use App\Services\MiddlewareInvoker;
use App\Models\AsignacionesRolesModel;
use Livewire\Component;
use App\Models\User;

class CreateAsignacionesRoles extends Component
{

    public $open = false;
    public $id_rol, $id_unico_rol, $usuario_empresa, $cuit, $rolesFiltrados, $id_relacion_empresa, $usuario, $empresa, $roles, $UsuarioActual, $usuariosNombre;
    public $permisos = [];

    protected $listeners = ['render' => 'render', 'openModalAsignarRol', 'opernModalAsignarlRolUnicoUsuario'];

    protected function rules()
    {
        return [
            'id_rol' => 'required',
            'usuario_empresa' => 'required_if:usuario,null',
        ];
    }

    public function opernModalAsignarlRolUnicoUsuario($data)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->datosUnicos($data);
            $this->open = true;
        }
    }

    private function datosUnicos($data)
    {
        $this->usuario_empresa = null;
        $this->empresa = IdHelper::empresaActual();
        $this->UsuarioActual = UsuariosEmpresasModel::where('id_relacion', $data)
            ->with([
                'usuarios',
            ])
            ->first();
        $this->usuariosNombre =  optional($this->UsuarioActual->usuarios)->name;
        $this->usuario =  $this->UsuarioActual->id_usuario;
        $this->id_relacion_empresa = $data;

        $this->roles = RolesModel::query()
            ->where('tipo_empresa',  $this->empresa->tipo)
            ->where(function ($q) {
                $q->whereNull('cuit')
                    ->orWhere('cuit', IdHelper::empresaActual()->cuit);
            })
            ->get();
    }

    public function openModalAsignarRol($data = null)
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->datos();
            $this->open = true;
        }
    }

    private function datos()
    {
        $this->UsuarioActual = null;
        $this->empresa = IdHelper::empresaActual();
        $this->usuario_empresa = UsuariosEmpresasModel::where('cuit', $this->empresa->cuit)
            ->with([
                'usuarios',
            ])
            ->get();
        $this->roles = RolesModel::query()
            ->where('tipo_empresa', $this->empresa->tipo)
            ->where(function ($q) {
                $q->whereNull('cuit')
                    ->orWhere('cuit', $this->empresa->cuit);
            })
            ->get();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();
        AsignacionesRolesModel::create([
            'id_rol' => $this->id_rol,
            'cuit' => $this->empresa->cuit,
            'usuario_empresa' => $this->usuario,
            'id_unico_rol' => $this->id_rol,
            'id_relacion_empresa' => $this->id_relacion_empresa,
        ]);

        $this->dispatch('lucky');
        $this->dispatch('refreshLivewireTable');
        $this->close();
    }

    public function render()
    {
        return view('livewire.roles.create-asignaciones-roles');
    }

    public function close()
    {
        $this->open = false;
        $this->reset([
            'id_rol',
            'id_unico_rol',
            'usuario_empresa',
            'cuit',
            'rolesFiltrados',
            'id_relacion_empresa',
            'usuario',
            'permisos'
        ]);
    }

    public function updatedusuario()
    {
        $usuarioEmpresa = UsuariosEmpresasModel::where('cuit', $this->empresa->cuit)
            ->where('id_usuario', $this->usuario)
            ->first();
        if ($usuarioEmpresa) {
            $this->id_relacion_empresa = $usuarioEmpresa->id_relacion;
        } else {
            $this->id_relacion_empresa = null;
        }
    }
}
