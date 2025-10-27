<?php

namespace App\Livewire\Activos;

use App\Helpers\IdHelper;
use App\Models\ActivosAsignacionModel;
use App\Models\UsuariosEmpresasModel;
use App\Models\ActivosModel;
use Illuminate\Support\Facades\DB;
use App\Models\EmpresasModel;
use App\Models\EstadoGeneralModel;
use App\Models\EstadosAltasModel;
use App\Models\NotificacionesModel;
use App\Models\User;
use Livewire\Component;

class EdicionMasiva extends Component
{
    protected $listeners = ['editMasivo'];
    public $activo = [];
    public $NroSelecionados, $TipoUsuario, $empresaPrestadora, $open, $asignado_a_id, $empleadosLista, $responsable, $nombreAsignado, $asignado_a, $general;
    public $nombreResponsable, $responsable_id, $searchAsignado, $searchResponsable, $noEmpleadosEncontrado, $altas, $noEmpleadosEncontrados, $id_estado_sit_alta, $id_estado_sit_general;

    public function editMasivo($arrayId)
    {
        $ids = $arrayId['ids'];
        $activo =  ActivosModel::whereIn('id_activo', $ids)->get();
        $this->NroSelecionados = $activo->count();
        $this->activo = $activo;
        $this->TipoUsuario = $this->comprobacion();
        $this->empresaPrestadora = EmpresasModel::where('cuit', IdHelper::idEmpresa())->value('tipo');
        $this->searchEmpleados();
        $this->altas = EstadosAltasModel::all();
        $this->general = EstadoGeneralModel::all();
        $this->open = true;
    }

    public function render()
    {
        return view('livewire.activos.edicion-masiva');
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

    public function updatedSearchResponsable()
    {
        $this->searchEmpleados();
    }

    public function updatedSearchAsignado()
    {
        $this->searchEmpleados();
    }

    public function close()
    {
        $this->reset([
            'asignado_a_id',
            'responsable_id',
            'id_estado_sit_alta',
            'id_estado_sit_general'
        ]);
        $this->open = false;
    }

    public function asignaciones()
    {
        // La entidad de la empresa actual
        $entidad = auth()->user()->entidad;

        DB::beginTransaction();

        try {
            foreach ($this->activo as $item) {
                // 1) Actualizar la asignación existente
                $asignacion = ActivosAsignacionModel::query()
                    ->where('id_activo', $item->id_activo)
                    ->where('estado_asignacion', '!=', 'Cancelado')
                    ->where('empresa_empleados', $entidad)
                    ->whereNull('fecha_fin_asignacion')
                    ->first();

                if ($asignacion) {
                    $asignacion->update([
                        'asignado_a'  => $this->asignado_a_id,
                        'responsable' => $this->responsable_id,
                    ]);
                }
                // 2) Actualizar el estado de alta en el propio modelo de Activo
                if (! is_null($this->id_estado_sit_alta)) {
                    $item->update([
                        'id_estado_sit_alta' => $this->id_estado_sit_alta,
                    ]);
                }

                if (! is_null($this->id_estado_sit_general)) {
                    $item->update([
                        'id_estado_sit_general' => $this->id_estado_sit_general,
                    ]);
                }
                // 3) Crear notificación si hay un asignado
                if (! is_null($this->asignado_a_id)) {
                    NotificacionesModel::create([
                        'cuit_empresa' => $entidad,
                        'id_usuario'   => $this->asignado_a_id,
                        'descripcion'  => 'Se le ha asignado el bien "'
                            . $item->nombre
                            . '" (edición masiva).',
                    ]);
                }
            }
            DB::commit();
            // 4) Despachar los eventos que necesites
            $this->dispatch('lucky');
            $this->dispatch('refreshLivewireTable');
            $this->dispatch('refreshBienesAceptados');
            $this->dispatch('refreshBienes');
            // 5) Cerrar el modal y limpiar estado
        } catch (\Exception $e) {
            DB::rollBack();
            // Dispara evento de error para el frontend
            $this->dispatch('errorInfo', [
                'title'   => 'Error',
                'message' => $e->getMessage()
            ]);
        }
        $this->close();
    }
}
