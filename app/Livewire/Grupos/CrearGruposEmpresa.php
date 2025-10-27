<?php

namespace App\Livewire\Grupos;

use App\Helpers\IdHelper;
use App\Models\GruposModel;
use App\Models\UsuariosEmpresasModel;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CrearGruposEmpresa extends Component
{
    protected $listeners = ['crearGrupos'];

    public $open;
    public $cuitEmpresa, $nombre_grupo, $descripcion;
    public $usuarios;              // Todos los usuarios técnicos disponibles
    public $searchUsuario = '';    // Valor del input de búsqueda
    public $filteredUsuarios = []; // Usuarios filtrados según búsqueda
    public $selectedUsers = [];    // Array de IDs de usuarios seleccionados

    public function crearGrupos()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        $this->open = true;
        $this->loadUsuarios();
    }

    private function loadUsuarios()
    {
        // Cargamos usuarios técnicos de la empresa
        $this->usuarios = UsuariosEmpresasModel::with('usuarios')
            ->where('cuit', IdHelper::idEmpresa())
            ->where('es_representante_tecnico', 'Si')
            ->get();
        // Inicialmente, los usuarios filtrados son todos los usuarios cargados
        $this->filteredUsuarios = $this->usuarios;
    }

    // Método que se ejecuta automáticamente cuando se actualiza searchUsuario
    public function updatedSearchUsuario($value)
    {
        // Filtramos la colección de usuarios comparando el nombre con el valor de búsqueda
        // Excluimos también a los usuarios que ya estén seleccionados.
        $this->filteredUsuarios = $this->usuarios->filter(function ($userEmpresa) use ($value) {
            return stripos($userEmpresa->usuarios->name, $value) !== false
                && !in_array($userEmpresa->id_usuario, $this->selectedUsers);
        });
    }

    // Agregar un usuario seleccionado
    public function selectUsuario($id)
    {
        $this->selectedUsers[] = $id;
        $this->searchUsuario = '';
        $this->updatedSearchUsuario(''); // Actualiza los usuarios filtrados
    }

    // Quitar un usuario seleccionado
    public function removeUsuario($id)
    {
        $this->selectedUsers = array_filter($this->selectedUsers, function ($userId) use ($id) {
            return $userId != $id;
        });
        $this->updatedSearchUsuario($this->searchUsuario);
    }

    // Método save() para guardar el grupo y la relación con los usuarios
    public function save()
    {
        DB::beginTransaction();
        try {
            // Crear el grupo
            $grupo = GruposModel::create([
                'cuit' => IdHelper::idEmpresa(),
                'nombre'       => $this->nombre_grupo,
                'descripcion'  => $this->descripcion
            ]);

            // Insertar en la tabla pivot act.usuarios_grupos para cada usuario seleccionado
            foreach ($this->selectedUsers as $userId) {
                $usuarioEmpresa = UsuariosEmpresasModel::where('id_usuario', $userId)
                    ->where('cuit', IdHelper::idEmpresa())
                    ->first();
                if ($usuarioEmpresa) {
                    DB::table('act.usuarios_grupos')->insert([
                        'id_usuario'  => $usuarioEmpresa->id_usuario,
                        'cuit'        => $usuarioEmpresa->cuit,
                        'id_relacion' => $usuarioEmpresa->id_relacion,
                        'id_grupo'    => $grupo->id_grupo,
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
        $this->dispatch('refreshLivewireTable');
        $this->reset(['nombre_grupo', 'selectedUsers', 'searchUsuario', 'filteredUsuarios']);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.grupos.crear-grupos-empresa');
    }
}
