<?php

namespace App\Livewire\Roles;

use App\Models\AsignacionesRolesModel;
use App\Models\UsuariosEmpresasModel;
use App\Services\MiddlewareInvoker;
use App\Models\PermisosRolesModel;
use App\Traits\VerificacionTrait;
use App\Models\EmpresasModel;
use App\Models\PermisosModel;
use App\Models\RolesModel;
use Livewire\Component;
use App\Models\User;


class EditAsignacionesRoles extends Component
{
    use VerificacionTrait;
    public $open, $id_rol, $cuit, $asignacionRoles;
    public $upRol, $upPermiso, $upUsuario, $upUnicoRol, $id_relacion_empresa, $rolesFiltrados, $userIds;
    protected $listeners = ['openModalEditarPermisos'];

    protected $rules =
    [
        'upRol' => 'required',
        'upPermiso' => 'required',
        'upUsuario' => 'required',
    ];

    public function openModalEditarPermisos($data){
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->openModalAsignacion($data);
        }
    }

    public function openModalAsignacion($data)
    {
        $asignacionRoles = AsignacionesRolesModel::where('id_asignacion', $data)->first();
        if ($asignacionRoles) {
            $this->asignacionRoles = $asignacionRoles;
            $this->upRol = $asignacionRoles->id_rol;
            $this->upUnicoRol = $asignacionRoles->id_unico_rol;
            $this->upPermiso = $asignacionRoles->id_permiso;
            $this->upUsuario = $asignacionRoles->usuario_empresa;
            $this->cuit = $asignacionRoles->cuit;
            $this->id_relacion_empresa = $asignacionRoles->id_relacion_empresa;
            $this->open = true;
        }
    }

    public function update()
    {
        $this->validate();

        $campos = ['id_rol', 'id_permiso', 'usuario_empresa'];

        $valoresActualizados = [
            'id_rol' => $this->upRol, 'id_permiso' => $this->upPermiso, 'id_unico_rol' => $this->upUnicoRol, 'upUsuario' => $this->upUsuario,
            'cuit' => $this->cuit, 'id_relacion_empresa' => $this->id_relacion_empresa
        ];

        $this->verificar($this->roles, $campos, $valoresActualizados);

        $this->dispatch('refreshLivewireTable');
    }

    public function render()
    {
        $roles = RolesModel::all();
        $permisos = PermisosModel::all();
        $tipos = EmpresasModel::all();
        $usuarios = User::all();

        if ($this->cuit) {
            $userIds = UsuariosEmpresasModel::where('cuit', $this->cuit)->pluck('id_usuario');
            $usuariosFiltrados = User::whereIn('id', $userIds)->get();
        } else {
            $usuariosFiltrados = collect(); // o maneja este caso de manera apropiada
        }

        return view('livewire.roles.edit-asignaciones-roles', [
            'asignacionRoles' => $this->asignacionRoles,
            'tipos' => $tipos,
            'roles' => $roles,
            'permisos' => $permisos,
            'usuarios' => $usuarios,
            'usuariosFiltrados' => $usuariosFiltrados,
        ]);
    }

    public function close()
    {
        $this->reset(['upPermiso', 'upUnicoRol', 'upUsuario', 'upRol']);
        $this->open = false;
    }

    public function Permiso($value)
    {
        $this->selectPermiso($value);
    }

    private function selectPermiso($value)
    {
        if ($value) {
            $this->rolesFiltrados = PermisosRolesModel::where('id_permiso', $value)
                ->where('cuit', $this->cuit)
                ->pluck('id_rol');
        } else {
            $this->rolesFiltrados = collect();
        }
    }

    public function updatedUsuarioEmpresa()
    {
        $this->updateRelacionEmpresa($this->usuario_empresa);
    }

    private function updateRelacionEmpresa($usuario_empresa)
    {
        $usuarioEmpresa = UsuariosEmpresasModel::where('cuit', $this->cuit)
            ->where('id_usuario', $usuario_empresa)
            ->first();
        if ($usuarioEmpresa) {
            $this->id_relacion_empresa = $usuarioEmpresa->id_relacion;
        } else {
            $this->id_relacion_empresa = null;
        }
    }
}
