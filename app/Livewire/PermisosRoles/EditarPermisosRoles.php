<?php

namespace App\Livewire\PermisosRoles;

use App\Helpers\IdHelper;
use App\Models\AsignacionesRolesModel;
use App\Models\UsuariosEmpresasModel;
use App\Services\MiddlewareInvoker;
use App\Models\PermisosRolesModel;
use App\Traits\VerificacionTrait;
use App\Models\PermisosModel;
use App\Models\RolesModel;
use Livewire\Component;
use App\Models\User;

class EditarPermisosRoles extends Component
{
    use VerificacionTrait;
    public $id_rol, $id_permiso, $updaterol, $udatepermiso, $roles_permisos, $permisosUser, $roles, $permiso, $user;
    public $open;
    public $mostrarBotonModificar = false;
    public $rolesDisponibles = [];

    protected $listeners = ['openModalEditarPermisos'];

    protected $rules = [
        'updaterol' => 'required',
        'udatepermiso' => 'required',
    ];

    public function actualizar()
    {
        $this->actualizarPermiso();
    }

    public function openModalEditarPermisos($data)
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->opendModal($data);
        }
    }

    protected function actualizarPermiso()
    {
        $this->permisosUser->id_rol = $this->id_rol;
        $this->permisosUser->save();
        $this->dispatch('lucky');
        $this->open = false;
    }

    public function opendModal($data)
    {
        $usuarioEmpresa = UsuariosEmpresasModel::find($data);
        // Cargar el usuario
        $this->user = User::find($usuarioEmpresa->id_usuario);
        // Cargar la relación de permisos y roles para el usuario
        $this->permisosUser = AsignacionesRolesModel::where('usuario_empresa', $usuarioEmpresa->id_usuario)
            ->where('id_relacion_empresa', $usuarioEmpresa->id_relacion)
            ->first();
        // Cargar el rol asociado
        $this->roles = RolesModel::find($this->permisosUser->id_rol);
        $this->id_rol = $this->roles->id_rol; // Inicializar id_rol con el rol asignado
        // Cargar todos los roles disponibles, excluyendo el rol asignado al usuario
        $this->rolesDisponibles = RolesModel::where('id_rol', '!=', $this->roles->id_rol)
            ->where('tipo_empresa', IdHelper::empresaActual()->tipo)
            ->get();
        // Inicializar los permisos del rol actual
        $this->updatePermisos($this->permisosUser->id_rol);
        $this->open = true;
    }

    public function updatePermisos($value)
    {
        $permisosRoles = PermisosRolesModel::where('id_rol', $value)->pluck('id_permiso');
        $this->permiso = PermisosModel::whereIn('id_permiso', $permisosRoles)->get();
        // Mostrar o no el botón de modificar
        $this->mostrarBotonModificar = true;
    }

    public function render()
    {
        return view('livewire.permisos-roles.editar-permisos-roles');
    }

    public function close()
    {
        $this->reset(['rolesDisponibles']);
        $this->open = false;
    }
}
