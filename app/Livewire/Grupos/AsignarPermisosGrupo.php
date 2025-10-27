<?php

namespace App\Livewire\Grupos;

use App\Helpers\IdHelper;
use App\Models\AsignacionesRolesModel;
use App\Models\GruposModel;
use App\Models\GruposRolesModel;
use App\Models\PermisosModel;
use App\Models\PermisosRolesModel;
use App\Models\RolesModel;
use App\Models\User;
use App\Models\UsuariosEmpresasModel;
use App\Models\UsuariosGruposModel;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AsignarPermisosGrupo extends Component
{
    protected $listeners = ['AsignarRol'];

    public $open = false;
    public $roles;              // Roles disponibles para la empresa
    public $permisos;           // Permisos asociados al rol seleccionado (se cargan al seleccionar un rol)
    public $usuarios;           // Usuarios que pertenecen al grupo
    public $cuitEmpresa;        // La empresa actual (cuit)
    public $nombreGrupo;        // Nombre del grupo a asignar permisos
    public $selectedRole = null; // Rol seleccionado para ver sus permisos
    public $id_grupo;
    public $assignedRoleName;   // Nombre del rol asignado previamente

    public function AsignarRol($data)
    {
        if (! MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        $this->datos($data);
        $this->open = true;
    }

    public function datos($data)
    {
        // Se asume que $data es el id_grupo
        $this->id_grupo = $data;
        $this->nombreGrupo = GruposModel::where('id_grupo', $data)->value('nombre');

        // Se obtiene el id del rol ya asignado, si existe
        $id_rol = GruposRolesModel::where('id_grupo', $data)->value('id_rol');

        // Asignar el nombre del rol asignado o 'Sin rol asignado'
        if ($id_rol) {
            $this->assignedRoleName = RolesModel::where('id_rol', $id_rol)->value('nombre');
        } else {
            $this->assignedRoleName = 'Sin rol asignado';
        }

        $this->cuitEmpresa = IdHelper::idEmpresa();

        // Roles disponibles para la empresa (excluye el rol ya asignado si existe)
        $this->roles = RolesModel::where('cuit', $this->cuitEmpresa)
            ->when($id_rol, function ($query, $id_rol) {
                return $query->where('id_rol', '<>', $id_rol);
            })->get();

        // Obtener los usuarios pertenecientes al grupo
        $id_usuarios = UsuariosGruposModel::where('id_grupo', $data)->pluck('id_usuario');
        $this->usuarios = User::whereIn('id', $id_usuarios)->get();

        // Reiniciar permisos y rol seleccionado
        $this->permisos = collect();
        $this->selectedRole = null;
    }

    // Cuando se selecciona un rol, se cargan los permisos asociados
    public function updatedSelectedRole($roleId)
    {
        // Primero se obtienen las asignaciones de permisos para el rol
        $permisosRoles = PermisosRolesModel::where('id_rol', $roleId)->get();
        // Luego se consultan los permisos de la tabla de permisos
        $this->permisos = PermisosModel::whereIn('id_permiso', $permisosRoles->pluck('id_permiso'))->get();
    }

    public function save()
    {
        DB::beginTransaction();
        try {
            // Crear registro en grupos_roles para asignar el rol al grupo
            GruposRolesModel::create([
                'id_rol'  => $this->selectedRole,
                'id_grupo' => $this->id_grupo,
            ]);

            // Recorrer cada usuario perteneciente al grupo
            foreach ($this->usuarios as $usuario) {
                // Buscamos el registro de usuario en la tabla de usuarios_empresas
                $usuarioEmpresa = UsuariosEmpresasModel::where('id_usuario', $usuario->id)
                    ->where('cuit', IdHelper::idEmpresa())
                    ->first();

                if ($usuarioEmpresa) {
                    // Creamos la asignación del rol para el usuario usando la información de usuarios_empresas
                    AsignacionesRolesModel::create([
                        'id_rol'              => $this->selectedRole,
                        'cuit'                => IdHelper::idEmpresa(),
                        'usuario_empresa'     => $usuario->id,
                        'id_unico_rol'        => $usuarioEmpresa->id_unico_rol,
                        'id_relacion_empresa' => $usuarioEmpresa->id_relacion,
                    ]);
                }
            }

            DB::commit();
            $this->dispatch('Exito', ['title' => 'Éxito', 'message' => 'Grupo creado exitosamente.']);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('errorInfo', ['title' => 'Error', 'message' => $e->getMessage()]);
        }
        $this->close();
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.grupos.asignar-permisos-grupo');
    }
}
