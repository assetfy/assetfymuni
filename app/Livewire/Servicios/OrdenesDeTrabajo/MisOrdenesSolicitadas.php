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
use App\Helpers\IdHelper;
use App\Models\OrdenesModel;

class MisOrdenesSolicitadas extends LivewireTable
{
    protected string $model = OrdenesModel::class;
    public $title = 'Mis Ordenes de Trabajo'; // Nombre del emcabezado
    public $createForm = 'crearOrdenes'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = true;
    public $empresa, $clientes_empresas, $key, $usuario;

    public function asignar()
    {
        $this->empresa = IdHelper::idEmpresa();
        $this->usuario = auth()->id();
    }

    protected function query(): Builder
    {
        $this->asignar();
        $this->dispatch('openModal', ['servicios.OrdenesDeTrabajo.editar-orden']);
        // Obtén el nombre de la tabla del modelo
        $table = (new $this->model)->getTable();
        $query = $this->model::with(['activos.ubicacion'])
            ->where('cuit_Cliente', $this->empresa)
            ->where('estado_vigencia', 'Cerrado');

        return $query;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                $editButton = '';

                // Mostrar botón solo si está Cerrado o Pendiente
                if ($model->estado_vigencia === 'Cerrado' || $model->estado_orden === 'Pendiente') {
                    $editButton = sprintf(
                        '<div class="flex items-center">
                    <button 
                        wire:click="$dispatch(\'openEditOrden\', { data: %d })"
                        style="background-color: #BFDBFE;"
                        class="text-blue-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                        title="Ver Detalle">
                        <i class="fa-solid fa-circle-info text-base"></i>
                    </button>
                </div>',
                        $model->getKey()
                    );
                }

                return $editButton;
            })
                ->clickable(false)
                ->asHtml(),

            Column::make(__('Proveedor'), 'proveedores.razon_social')
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

    public function crearOrdenes()
    {
        return redirect()->route('ordenes-proveedores');
    }

    public function openEditOrdens(int $idOrden)
    {
        // Navegación SPA (Livewire 3)
        return $this->redirectRoute('servicios.ordenes.editar', ['id' => $idOrden], navigate: true);
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
