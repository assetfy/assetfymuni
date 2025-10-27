<?php

namespace App\Livewire\Tipos;

use App\Models\TiposModel;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;

class Tipos extends LivewireTable
{
    protected string $model = TiposModel::class;
    public $title = 'Tipos'; // Nombre del emcabezadp
    public $createForm = 'crearTipos'; // Nombre de la funcion que llama al evento
    protected bool $useSelection = false;
    protected bool $modalDispatched = false;

    public function hydrate(): void
    {
        if (! $this->modalDispatched) {
            $this->dispatch('openModal', [
                'tipos.create-tipos',
                'tipos.edit-tipos'
            ]);
            $this->modalDispatched = true;
        }
    }

    protected function columns(): array
    {
        return [
            ImageColumn::make(__('Imagen'), 'imagen')
                ->size(75, 75),
            Column::make(__('Sigla'), 'sigla')
                ->sortable(),
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Descripción'), 'descripcion')
                ->sortable(),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'openEditTipo\', { data: ' . $model->getKey() . ' })" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Editar
                            </button>';
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }

    public function crearTipos()
    {
        $this->dispatch('crearTipo')->to('tipos.create-tipos');
    }
}
