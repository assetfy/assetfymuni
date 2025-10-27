<?php

namespace App\Livewire\Servicios;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\MisProveedoresModel;
use Illuminate\Support\Facades\Log;
use App\Helpers\IdHelper;

class MisProvedoresFavoritos extends LivewireTable
{
    protected string $model = MisProveedoresModel::class;
    public $title = 'Mis Proveedores'; // Nombre del encabezado
    public $createForm = 'RegistrarProveedores'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    public $userId, $empresa;

    public function asignar()
    {
        $this->empresa = IdHelper::idEmpresa();
        $this->userId = auth()->user()->id;
    }

    protected function query(): Builder
    {
        $this->asignar();

        $this->dispatch('openModal', ['servicios.cargar-mis-proveedores', 'servicios.editar-contratos']);

        $query = MisProveedoresModel::query()->where('id_usuario', $this->userId);

        return $query;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                $id   = $model->getKey();
                $cuit = $model->cuit;

                // 1) Eliminar – Rojo pastel
                $eliminarBtn = '
                <button 
                    wire:click="eliminarProveedor(' . $id . ')" 
                    style="background-color: #FECACA;" 
                    class="text-red-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12"
                    title="Eliminar">
                    <i class="fa-solid fa-trash text-base"></i>
                </button>
            ';

                // 2) Editar contrato – Índigo pastel
                $editarBtn = '
                <button 
                    wire:click="dispatch(\'openEditContrato\', { data: \'' . $cuit . '\' })" 
                    style="background-color: #C7D2FE;" 
                    class="text-indigo-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12"
                    title="Editar contrato">
                    <i class="fa-solid fa-file-contract text-base"></i>
                </button>
            ';

                // 3) Detalle – Azul pastel
                $detalleBtn = '
                <button 
                    wire:click="$dispatch(\'PrestadoraDetalle\', { data: \'' . $cuit . '\' })" 
                    style="background-color: #BFDBFE;" 
                    class="text-blue-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12"
                    title="Detalle">
                    <i class="fa-solid fa-eye text-base"></i>
                </button>
            ';

                return '<div class="flex space-x-2">' . $eliminarBtn . $editarBtn . $detalleBtn . '</div>';
            })
                ->clickable(false)
                ->asHtml(),

            Column::make(__('Empresa'), 'razon_social')
                ->sortable()
                ->searchable(),
            Column::make(__('Registrada en la plataforma'), 'existe_en_la_plataforma'),
            Column::make(__('Localidad'), 'localidad'),
            Column::make(__('Provincia'), 'provincia'),
            // Utilizamos el accesor para el contrato
            Column::make(__('Contrato'), 'contrato_formatted')
                ->asHtml(),
        ];
    }

    public function eliminarProveedor($id)
    {
        // Intenta encontrar el proveedor por ID
        $proveedor = MisProveedoresModel::find($id);

        if (!$proveedor) {
            Log::warning("No se pudo encontrar el proveedor con ID: $id para eliminar.");
            return;
        }
        // Si existe un contrato asociado, elimínalo usando la relación
        if ($proveedor->contratoRelacion) {
            $proveedor->contratoRelacion->delete();
        }
        // Elimina el proveedor
        $proveedor->delete();
        // Notifica al front y refresca la tabla
        $this->dispatch('eliminado');
        $this->dispatch('refreshLivewireTable');
    }


    public function RegistrarProveedores()
    {
        $this->dispatch('RegistrarProveedores')->to('servicios.cargar-mis-proveedores');
    }
}
