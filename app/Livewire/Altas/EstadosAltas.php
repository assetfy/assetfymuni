<?php

namespace App\Livewire\Altas;

use App\Models\EstadosAltasModel;
use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;

class EstadosAltas extends LivewireTable
{
    protected string $model = EstadosAltasModel::class;
    public $title = 'Estados Alta'; // Nombre del emcabezado
    public $createForm = 'CreateEstadoAltas'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
     // Función para cambiar el componente de creación
     protected function columns(): array
     {
         return [
             Column::make(__('Nombre'), 'nombre')
             ->sortable(),
             Column::make(__('Descripción'), 'descripcion'),
             Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'editarEstadoAlta\', { data: ' . $model->getKey() . ' })" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Editar
                                </button>';
            })
                ->clickable(false)
                ->asHtml(),
         ];  
     }

     public function CreateEstadoAltas(){
        $this->dispatch('CreateEstadoAltas')->to('altas.create-estados-altas');
    }
}