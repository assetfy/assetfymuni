<?php

namespace App\Livewire\Grupos;

use App\Helpers\IdHelper;
use App\Models\AsignacionesRolesModel;
use App\Models\GruposModel;
use App\Models\GruposRolesModel;
use App\Models\RolesModel;
use App\Models\User;
use App\Models\UsuariosGruposModel;
use App\Models\UsuariosEmpresasModel;
use App\Services\MiddlewareInvoker;
use Livewire\Component;
use Illuminate\Support\Facades\DB;


class EditarUsuariosGrupo extends Component
{
    protected $listeners = ['EditarGrupo'];

    public $open = false;
    public $id_grupo;
    public $nombreGrupo;
    public $usuarios;
    public $datosUsuarios;
    public $usuariosDisponibles;
    public $searchUsuario = '';
    public $filteredUsuarios;
    public $nombreDelrol;
    public $rol;

    public function EditarGrupo($data)
    {
        if (! MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        $this->datos($data);
        $this->open = true;
    }

    private function datos($data)
    {
        // Se asume que $data es el id_grupo
        $this->id_grupo = $data;
        $this->nombreGrupo = GruposModel::where('id_grupo', $data)->value('nombre');
        $id_rol = GruposRolesModel::where('id_grupo', $data)->value('id_rol');
        $this->rol = RolesModel::where('id_rol', $id_rol)->get();
        $this->nombreDelrol = $this->rol->pluck('nombre') ?? 'Sin rol asignado';
        // Obtenemos los IDs de los usuarios que pertenecen al grupo (tabla pivot)
        $ids = UsuariosGruposModel::where('id_grupo', $data)->pluck('id_usuario');
        $this->usuarios = User::whereIn('id', $ids)->get();

        // Inicializamos el buscador y la lista de usuarios disponibles (usuarios que NO están en el grupo)
        $this->searchUsuario = '';
        $this->usuariosDisponibles = UsuariosEmpresasModel::whereNotIn('id_usuario', $this->usuarios->pluck('id'))
            ->where('cuit', IdHelper::idEmpresa())
            ->get();

        // Inicializamos la lista de usuarios filtrados con los usuarios disponibles
        $this->filteredUsuarios = User::whereIn('id', $this->usuariosDisponibles->pluck('id_usuario'))
            ->limit(10)
            ->get();
    }

    // Cada vez que se actualiza el input searchUsuario se filtra la lista
    public function updatedSearchUsuario($value)
    {
        $idsEnGrupo = $this->usuarios->pluck('id');
        $this->filteredUsuarios = User::where('name', 'like', '%' . $value . '%')
            ->whereNotIn('id', $idsEnGrupo)
            ->limit(10)
            ->get();
    }

    // Agrega un usuario al grupo

    public function addUsuario($userId)
    {
        DB::beginTransaction();

        try {
            $usuarioEmpresa = UsuariosEmpresasModel::where('id_usuario', $userId)
                ->where('cuit', IdHelper::idEmpresa())
                ->first();

            if (!$usuarioEmpresa) {
                throw new \Exception('El usuario no pertenece a la empresa actual.');
            }

            UsuariosGruposModel::updateOrCreate(
                [
                    'id_usuario' => $userId,
                    'id_grupo'   => $this->id_grupo,
                ],
                [
                    'cuit'        => IdHelper::idEmpresa(),
                    'id_relacion' => $usuarioEmpresa->id_relacion,
                ]
            );

            AsignacionesRolesModel::updateOrCreate(
                [
                    'usuario_empresa'     => $userId,
                    'id_relacion_empresa' => $usuarioEmpresa->id_relacion,
                ],
                [
                    'id_rol'  => $this->rol->value('id_rol'),
                    'cuit'    => IdHelper::idEmpresa(),
                ]
            );

            DB::commit();

            // Actualizar la colección de usuarios
            $newUser = User::find($userId);
            $this->usuarios->push($newUser);

            // Actualizar la lista de usuarios filtrados
            $this->filteredUsuarios = collect($this->filteredUsuarios)
                ->reject(fn($usuario) => $usuario->id == $userId)
                ->values();

            $this->filteredUsuarios = User::where('name', 'like', '%' . $this->searchUsuario . '%')
                ->whereNotIn('id', $this->usuarios->pluck('id'))
                ->limit(10)
                ->get();

            $this->searchUsuario = '';
        } catch (\Throwable $e) {
            DB::rollBack();

            // Evento Livewire para mostrar error
            $this->dispatch('errorInfo', [
                'title'   => 'Error al agregar usuario',
                'message' => $e->getMessage(),
            ]);
        }
    }


    // Remueve un usuario del grupo
    public function removeUsuario($userId)
    {
        DB::beginTransaction();

        try {
            // Obtener id_relacion correspondiente al usuario en el grupo
            $id_relacion = UsuariosGruposModel::where('id_usuario', $userId)
                ->where('id_grupo', $this->id_grupo)
                ->value('id_relacion'); // Solo un valor, no una colección

            if (!$id_relacion) {
                throw new \Exception("No se encontró la relación del usuario con el grupo.");
            }

            // Buscar y eliminar el rol asignado (si existe)
            $RolAsignado = AsignacionesRolesModel::where('id_relacion_empresa', $id_relacion)
                ->where('usuario_empresa', $userId)
                ->where('cuit', IdHelper::idEmpresa())
                ->first();

            if ($RolAsignado) {
                $RolAsignado->delete();
            }

            // Eliminar relación del usuario con el grupo
            UsuariosGruposModel::where('id_grupo', $this->id_grupo)
                ->where('id_relacion', $id_relacion)
                ->where('id_usuario', $userId)
                ->delete();

            DB::commit();

            // Actualizar la colección de usuarios localmente
            $this->usuarios = $this->usuarios->reject(fn($user) => $user->id == $userId)->values();
        } catch (\Throwable $e) {
            DB::rollBack();

            // Evento Livewire de error
            $this->dispatch('errorInfo', [
                'title'   => 'Error al eliminar usuario',
                'message' => $e->getMessage(),
            ]);
        }
    }
    // Método save para confirmar y cerrar el modal

    public function close()
    {
        $this->reset(['id_grupo', 'nombreGrupo', 'usuarios', 'searchUsuario', 'filteredUsuarios']);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.grupos.editar-usuarios-grupo');
    }
}
