<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;


use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\IdHelper;
use App\Models\OrdenesModel;
use App\Models\RolesModel;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrdenesClientes extends LivewireTable
{
    protected string $model = OrdenesModel::class;
    public $title = 'Mis Ordenes'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    public $empresa, $clientes_empresas, $key, $usuario;

    protected $listeners = ['RechazarOrden', 'refreshMisOrdenes'];

    public function asignar()
    {
        $this->empresa = IdHelper::idEmpresa();
        $this->usuario = auth()->id();
    }

    protected function query(): Builder
    {
        $this->asignar();

        $idsRol = RolesModel::where('nombre', 'Prestadora - Manager')
            ->pluck('id_rol');

        $prestadoraManager = \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();

        // Obtén el nombre de la tabla del modelo (aunque en este ejemplo no se usa $table)
        // $table = (new $this->model)->getTable();

        // $query = $this->model::with(['activos.ubicacion'])
        //     ->where('proveedor', $this->empresa)
        //     ->whereNotNull("cuit_cliente");

        if ($prestadoraManager) {
            return $this->model::where('proveedor', $this->empresa)
                ->where('act.ordenes.id_usuario', $this->usuario)
                ->where(function ($query) {
                    $query->where('estado_vigencia', '=', 'Cerrado');
                });
        } else {
            return $this->model::where('proveedor', $this->empresa)
                ->where('act.ordenes.representante_tecnico', $this->usuario)
                ->where(function ($query) {
                    $query->where('estado_vigencia', '!=', 'Cerrado');
                });
        }
        return $query;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Id Orden'), 'id_orden')
                ->sortable()
                ->searchable(),
            Column::make(__('Estado vigencia'), 'estado_vigencia'),
            Column::make(__('comentarios'), 'comentarios'),
            Column::make(__('Tecnico asignado'), 'Representante_tecnico.usuarios.name'),
            Column::make(__('Tipo de orden'), 'tipo_orden_colored')->asHtml(),
            Column::make(__('Estado orden'), 'estado_orden_colored')->asHtml(),
            DateColumn::make(__('Fecha Creación'), 'fecha')
                ->format('Y-m-d'),
            // Column::make(__('Acciones'), function (Model $model): string {
            //     // Botón para abrir la orden (Visitar/Detalles)
            //     $editButton = '<button wire:click="$dispatch(\'openEditOrden\', { data: ' . $model->getKey() . ' })" 
            //                     class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
            //                     ' . (($model->estado_orden === 'Pendiente') ? 'Visitar' : 'Detalles') . '
            //                 </button>';

            //     // Botón para rechazar la orden (solo si está pendiente, por ejemplo)
            //     $rejectButton = '';
            //     if ($model->estado_orden === 'Pendiente') {
            //         $id = $model->getKey();

            //         $rejectButton = '<button title="Rechazar" wire:click="confirmarRechazo(' . $id . ')" 
            //                         class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded ml-2">
            //                         <i class="fa fa-times"></i>
            //                     </button>';
            //     }

            //     return $editButton . $rejectButton;
            // })
            //     ->clickable(false)
            //     ->asHtml(),
        ];
    }


    public function openEditOrdens()
    {
        $this->dispatch('openEditOrden')->to('servicios.ordenes-de-trabajo.editar-orden');
    }

    public function confirmarRechazo($id)
    {
        // Lanzar el SweetAlert desde Livewire
        $mensaje = "Esta acción cancelará la orden de forma definitiva. ¿Desea continuar?";
        $this->dispatch('rechazarOrden', ['message' => $mensaje, 'id' => $id]);
    }

    public function RechazarOrden($id)
    {
        // Se asume que $payload['data'] contiene el ID de la orden
        $orden = OrdenesModel::where('id_orden', $id);

        if ($orden) {
            $orden->update([
                'estado_orden' => 'Rechazado',
                'estado_vigencia' => 'Cerrado',
            ]);

            // Opcionalmente, emitir un mensaje o refrescar la tabla
            $this->dispatch('Exito', [
                'title' => 'Orden Rechazada',
                'message' => 'La orden de trabajo ha sido rechazada correctamente.'
            ]);
            $this->dispatch('refreshLivewireTable'); // Refrescar la tabla
        }
    }
}
