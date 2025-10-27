<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;

use App\Models\OrdenesModel;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\IdHelper;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;

class OrdenesGeneradas extends LivewireTable
{
    protected string $model = OrdenesModel::class;
    public $title = 'Mis Ordenes'; // Nombre del emcabezado
    public $createForm = 'crearOrdenes'; // Nombre del componente de creación predeterminado
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

        // Obtén el nombre de la tabla del modelo
        $table = (new $this->model)->getTable();

        $query = $this->model::with(['activos.ubicacion'])
            ->where('proveedor', $this->empresa)
            ->where("{$table}.id_usuario", $this->usuario);
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
            Column::make(__('Ubicacion'), function (Model $model): string {
                // Usamos el operador nullsafe para evitar errores si alguna relación no existe
                return e($model->activos?->ubicacion?->nombre ?? 'Sin ubicación');
            })->sortable()->asHtml(),
            Column::make(__('Acciones'), function (Model $model): string {
                // Contenedor flex para alinear los botones en línea
                $html = '<div class="flex items-center gap-2">';

                // 1) Botón para abrir la orden (Visitar/Detalles)
                $label = $model->estado_orden === 'Pendiente' ? 'Visitar' : 'Detalles';
                $html .= '<button 
                                wire:click="$dispatch(\'openEditOrden\', { data: ' . $model->getKey() . ' })"
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                ' . $label . '
                           </button>';

                // 2) Botón para rechazar la orden (solo si está pendiente)
                if ($model->estado_orden === 'Pendiente') {
                    $html .= '<button 
                                    title="Rechazar" 
                                    wire:click="confirmarRechazo(' . $model->getKey() . ')"
                                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                                    <i class="fa fa-times"></i>
                               </button>';
                }

                // 3) Botón para asignar encargado (solo si está pendiente)
                if ($model->estado_orden === 'Pendiente') {
                    $html .= '<button 
                                    wire:click="$dispatch(\'openASignarTecnicoEncargadoOrdenes\', { data: ' . $model->getKey() . ' })"
                                    class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded flex items-center gap-1"
                                    title="Asignar Técnico">
                                    <i class="fa-solid fa-hard-hat"></i>
                                    <span>Asignar Técnico</span>
                               </button>';
                }

                $html .= '</div>';

                return $html;
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }

    public function openEditOrdens()
    {
        $this->dispatch('openEditOrden')->to('servicios.ordenes-de-trabajo.editar-orden');
    }

    public function crearOrdenes()
    {
        return redirect()->route('ordenes-de-trabajo');
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

    public function refreshMisOrdenes()
    {
        $this->render(); // Refresca la vista del componente.

    }
}
