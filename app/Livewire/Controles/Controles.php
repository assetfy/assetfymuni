<?php

namespace App\Livewire\Controles;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use App\Models\ControlesModel;

class Controles extends LivewireTable
{
    protected string $model = ControlesModel::class;
    public $title = 'Controles'; // Nombre del emcabezado
    public $createForm = 'CreateControles'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;

    public function CreateControles()
    {
        $this->dispatch('CreateControles')->to('controles.create-controles');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Descripción'), 'descripcion'),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'EditarControles\', { value: ' . $model->getKey() . ' })" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Editar
                            </button>';
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }

}
