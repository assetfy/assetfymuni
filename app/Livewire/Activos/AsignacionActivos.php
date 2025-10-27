<?php

namespace App\Livewire\Activos;

use App\Helpers\IdHelper;
use Illuminate\Support\Facades\Storage;
use App\Models\ActivosModel;
use App\Models\UsuariosEmpresasModel;
use App\Models\ActivosAsignacionModel;
use App\Models\ActivosFotosModel;
use App\Models\EmpresasModel;
use App\Models\EstadoGeneralModel;
use App\Models\EstadosAltasModel;
use App\Models\UbicacionesModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Models\NotificacionesModel;

class AsignacionActivos extends Component
{
    public $activo;
    public $nombreActivo;
    public $usuariosEmpresa = [];
    public $searchUsuario = '';
    public $selectedGestorId = null;
    public $selectedAsignadoNombre = 'Sin Asignado';
    public $selectedAsignadoId = null;
    public $selectedResponsableNombre = 'Sin Responsable';
    public $selectedResponsableId = null;
    public $userId, $open, $TipoUsuario, $asignacion, $nombreEncargado, $nombreAsignado, $nombreResponsable;
    public $searchGestionado, $searchAsignado, $searchResponsable, $fecha_asignacion, $asignado_a_id, $gestionado_por_id,
        $empleadosLista, $noEmpleadosEncontrados, $responsable, $responsable_id, $asignado_a;
    public $origen = 'default';

    public ?string $fotoActivo = null;
    public ?string $fotoActivoUrl = null;
    protected $listeners = ['asignarActivo'];
    public ?string $asignadoEmail = null;
    public ?string $gestorEmail   = null;

    public function mount(ActivosModel $activo)
    {
        $this->activo = $activo;

        $foto = ActivosFotosModel::where('id_activo', $activo->id_activo)->first();
        if ($this->fotoActivo) {
            // Genero la URL temporal usando la misma key
            $this->fotoActivoUrl = Storage::disk('s3')
                ->temporaryUrl($this->fotoActivo, now()->addMinutes(10));
        } else {
            $this->fotoActivoUrl = null;
        }

        $this->nombreActivo = $activo->nombre;

        $this->empresa();

        // Comprueba que rol esta dado al usuario
        $this->TipoUsuario = $this->comprobacion();

        if ($this->activo->id_activo) {

            $this->searchEmpleados();

            // Cargar la asignación actual del activo
            $this->asignacion = ActivosAsignacionModel::where('id_activo', $this->activo->id_activo)
                ->where('empresa_empleados', IdHelper::idEmpresa())
                ->get();

            // Verificar si existe asignación
            if (!empty($this->asignacion)) {
                $this->responsableAsignados();
            } else {
                if ($this->TipoUsuario === 'Gestor') {
                    $this->nombreAsignado = 'Sin Asignado';
                    $this->nombreResponsable = 'Sin Responsable';
                } else {
                    $this->selectedAsignadoId = null;
                    $this->nombreAsignado = 'Sin Asignado';
                }
            }

            $this->open = true;
        }
    }

    private function comprobacion()
    {
        // Obtener el ID del usuario autenticado
        $userId = auth()->id();

        // Obtener la entidad del usuario autenticado
        $entidad = auth()->user()->entidad;

        // Buscar el registro en UsuariosEmpresasModel que coincide con el ID de usuario y la entidad
        if (!$entidad) {
            return 'Error';
        } else {
            $usuarioEmpresa = UsuariosEmpresasModel::where('cuit', $entidad)
                ->where('id_usuario', $userId)
                ->where('tipo_user', '2')
                ->first();
        }

        // Verificar el tipo de usuario
        $gestor = ActivosAsignacionModel::where('gestionado_por', $userId)->exists();

        $responsable = ActivosAsignacionModel::where('responsable', $userId)->exists();

        $asignado = ActivosAsignacionModel::where('asignado_a', $userId)->exists();


        if ($usuarioEmpresa) {
            if ($gestor) {
                return 'Gestor';
            } elseif ($responsable) {
                return 'Responsable';
            } elseif ($asignado) {
                return 'Asignado';
            }
        }
    }

    private function responsableAsignados()
    {
        // Obtiene el Id de cada uno de los usuarios para mostrar el correspondiente dato
        foreach ($this->asignacion as $asignacion) {

            $this->selectedGestorId = $asignacion->gestionado_por;
            $this->selectedAsignadoId = $asignacion->asignado_a;
            $this->selectedResponsableId = $asignacion->responsable;

            $this->asignados();
            $this->gestores();
            $this->responsable();
        }
    }

    private function gestores()
    {
        $this->nombreEncargado = $this->nombreUsuario($this->selectedGestorId) ?? 'Error al obtener el nombre';
    }

    private function asignados()
    {
        if ($this->selectedAsignadoId) {
            $this->nombreAsignado = $this->nombreUsuario($this->selectedAsignadoId) ?? 'Error al obtener el nombre';

            $this->asignado_a = $this->nombreAsignado;
            $this->asignado_a_id = $this->selectedAsignadoId;
        } else {
            $this->nombreAsignado = 'Sin Asignado';

            $this->asignado_a = null;
            $this->asignado_a_id = null;
        }
    }

    private function responsable()
    {
        if ($this->selectedResponsableId) {
            $this->nombreResponsable = $this->nombreUsuario($this->selectedResponsableId) ?? 'Error al obtener el nombre';

            $this->responsable = $this->nombreResponsable;
            $this->responsable_id = $this->selectedResponsableId;
        } else {
            $this->nombreResponsable  = 'Sin Responsable';

            $this->responsable = null;
            $this->responsable_id = null;
        }
    }

    private function nombreUsuario($id)
    {
        // Obtiene el nombre de los usuarios asignados correspondientes
        return User::find($id)->name;
    }

    // Funcionamiento para poder abrir el modal desde las diferentes vistas para delegar bien a los empleados
    public function asignarActivo($data, $origen = 'default')
    {
        // Verificar si el parámetro es un array o un entero
        if (is_int($data)) {
            $this->activo = ActivosModel::find($data);
        } elseif (is_array($data)) {
            $activoId = $data['data'];
            $this->activo  = ActivosModel::find($activoId);
        }

        // Inicializa los datos
        $this->mount($this->activo);
        $this->origen = $origen;
    }

    public function updatedSearchResponsable()
    {
        $this->searchEmpleados();
    }

    public function updatedSearchAsignado()
    {
        $this->searchEmpleados();
    }

    //Busqueda de los empleados para ser asignados
    public function setAsignadoA($id)
    {
        if (is_null($id)) {
            $this->asignado_a = 'Sin Asignado';
            $this->asignado_a_id = null;
        } else {
            $empleado = User::find($id);
            $this->asignado_a = $empleado ? $empleado->name : null;
            $this->asignado_a_id = $empleado->id;
        }

        $this->searchAsignado = ''; // Limpiar búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'asignado']); // Cerrar el dropdown
    }

    //Busqueda de los empleados para ser responsables
    public function setResponsable($id)
    {
        if (is_null($id)) {
            $this->responsable = 'Sin Responsable';
            $this->responsable_id = null;
        } else {
            $empleado = User::find($id);
            $this->responsable = $empleado ? $empleado->name : null;
            $this->responsable_id = $empleado->id;
        }

        $this->searchResponsable = ''; // Limpiar búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'responsable']); // Cerrar el dropdown
    }

    // Busca los empleados de dicha empresa para ser mostrados
    private function searchEmpleados()
    {
        // Busca los Id de los empleados en UsuariosEmpresasModel
        $usuariosIds = UsuariosEmpresasModel::where('cuit', auth()->user()->entidad) // Misma empresa
            ->where('estado', 'Aceptado')
            ->pluck('id_usuario'); // Excluir usuario actuala

        // Validar si hay IDs, si no, no hace la consulta en User
        if ($usuariosIds->isEmpty()) {
            $this->empleadosLista = collect(); // Colección vacía
            $this->noEmpleadosEncontrados = true;
            $this->responsable = null;
            $this->asignado_a = null;
            return;
        }

        $nombreBuscado = $this->searchResponsable ?: $this->searchAsignado ?: '';

        // Buscar en User nombre que coincida con los existentes
        $this->empleadosLista = User::whereIn('id', $usuariosIds)
            ->where('name', 'like', "%{$nombreBuscado}%")
            ->get();

        // Verificar si hay resultados
        $this->noEmpleadosEncontrados = $this->empleadosLista->isEmpty();

        if ($this->noEmpleadosEncontrados) {
            $this->responsable = null;
            $this->asignado_a = null;
        }
    }

    public function asignaciones()
    {
        // Obtener la entidad del usuario autenticado
        $entidad = auth()->user()->entidad;

        // dd($entidad, $this->asignado_a_id, $this->responsable_id, $this->activo->id_activo);
        DB::beginTransaction();

        try {

            $asignacion = ActivosAsignacionModel::where('id_activo', $this->activo->id_activo)
                ->where('estado_asignacion', '!=', 'Cancelado')
                ->where('empresa_empleados', $entidad)
                ->whereNull('fecha_fin_asignacion')
                ->first();

            if ($asignacion) {
                $asignacion->update([
                    'asignado_a' => $this->asignado_a_id,
                    'responsable' => $this->responsable_id,
                ]);
            }

            if ($this->asignado_a_id) {
                NotificacionesModel::create([
                    'cuit_empresa' => $entidad,
                    'id_usuario' =>  $this->asignado_a_id,
                    'descripcion' => 'Se le ha asignado el bien ' . $this->activo->nombre,
                ]);
            }

            DB::commit();

            // Despachar eventos
            $this->dispatch('lucky');
            $this->dispatch('refreshLivewireTable');

            $this->dispatch('refreshBienesAceptados');

            $this->dispatch('refreshBienes');

            // Cerrar el modal
            $this->close();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error al asignar gestor/asignado: ' . $e->getMessage());
        }
    }

    private function empresa()
    {
        return EmpresasModel::where('cuit', IdHelper::idEmpresa())->pluck('tipo')->first();
    }

    public function close()
    {
        $this->reset([
            'activo',
            'fotoActivo',
            'nombreActivo',
            'selectedResponsableNombre',
            'selectedResponsableId',
            'selectedAsignadoNombre',
            'selectedAsignadoId',
            'searchUsuario',
            'asignado_a',
            'asignado_a_id',
            'responsable_id',
            'responsable'
        ]);
        $this->open = false;
    }

    public function render()
    {
        $altas = EstadosAltasModel::all();
        $generales = EstadoGeneralModel::all();
        $empresas = EmpresasModel::all();
        $ubicaciones = UbicacionesModel::all();

        return view('livewire.activos.asignacion-activos', [
            'usuariosEmpresa' => $this->usuariosEmpresa,
            'altas' => $altas,
            'generales' => $generales,
            'empresas' => $empresas,
            'ubicaciones' => $ubicaciones,
            'empresaPrestadora' => $this->empresa(),
        ]);
    }
}
