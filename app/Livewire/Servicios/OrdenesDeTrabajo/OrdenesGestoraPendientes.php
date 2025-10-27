<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Enumerable;
use App\Exports\OrdenesExport;
use App\Models\OrdenesModel;
use App\Helpers\IdHelper;

class OrdenesGestoraPendientes extends LivewireTable
{
    protected string $model = OrdenesModel::class;
    public $title = 'Mis Ordenes de Trabajo'; // Nombre del emcabezado
    public $createForm = 'crearOrdenes'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = true;
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
        // Obtén el nombre de la tabla del modelo
        $table = (new $this->model)->getTable();
        $query = $this->model::with(['activos.ubicacion'])
            ->where('cuit_Cliente', $this->empresa)
            ->where('estado_vigencia', 'Activo');

        return $query;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                $editButton = '';
                $rejectButton = '';

                // Botón "Detalle" – Azul pastel
                if ($model->estado_vigencia === 'Cerrado' || $model->estado_orden === 'Pendiente') {
                    $editButton = sprintf(
                        '<button 
                wire:click="$dispatch(\'openEditOrden\', { data: %d })"
                style="background-color: #BFDBFE;"
                class="text-blue-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                title="Ver Detalle">
                <i class="fa-solid fa-circle-info text-base"></i>
            </button>',
                        $model->getKey()
                    );
                }

                // Botón "Rechazar" – Rojo pastel
                if ($model->estado_orden === 'Pendiente') {
                    $rejectButton = sprintf(
                        '<button 
                wire:click="confirmarRechazo(%d)"
                style="background-color: #FECACA;"
                class="text-red-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                title="Rechazar">
                <i class="fa fa-times text-base"></i>
            </button>',
                        $model->getKey()
                    );
                }

                return '<div class="flex space-x-2 items-center">' . $editButton . $rejectButton . '</div>';
            })
                ->clickable(false)
                ->asHtml(),

            Column::make(__('Prasdfsadfasdfoveedor'), 'proveedores.razon_social')
                ->sortable()
                ->asHtml(),
            Column::make(__('Descripción'), function (Model $model): string {
                return $model->comentarios
                    ? e($model->comentarios)
                    : '<span style="color: red;">No tiene descripción</span>';
            })
                ->sortable()
                ->asHtml(),
            Column::make(__('Estado vigencia'), 'estado_vigencia'),
            Column::make(__('Tipo de orden'), 'tipo_orden_colored')->asHtml(),
            Column::make(__('Estado orden'), 'estado_orden_colored')->asHtml(),
            DateColumn::make(__('Fecha Creación'), 'fecha')
                ->format('Y-m-d'),
        ];
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

    public function crearOrdenes()
    {
        return redirect()->route('ordenes-proveedores');
    }

    protected function actions(): array
    {
        return [
            Action::make('Exportar Historial', 'export_selected', function (Enumerable $models) {
                // $models son los registros marcados
                return Excel::download(
                    new OrdenesExport($models),
                    'historial_Ordenes.xlsx'
                );
            }),
        ];
    }
}
