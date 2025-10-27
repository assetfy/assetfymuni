<?php

namespace App\Livewire\General;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use App\Models\EstadoGeneralModel;

class EstadoGeneral extends LivewireTable
{
    protected string $model = EstadoGeneralModel::class;
    public $title = 'Estado General'; // Nombre del emcabezado
    public $createForm = 'createEstadoGeneral'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;

    public function createEstadoGeneral()
    {
        $this->dispatch('CreateEstadogeneral')->to('general.create-estado-general');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Descripción'), 'descripcion'),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'EditarEstadoGeneral\', { value: ' . $model->getKey() . ' })" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Editar
                            </button>';
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }
}
