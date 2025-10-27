<?php

namespace App\Livewire\Categoria;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use App\Models\CategoriaModel;

class Categoria extends LivewireTable
{
    protected string $model = CategoriaModel::class;
    public $title = 'Categoria'; // Nombre del emcabezado
    public $createForm = 'crearCategoria'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    protected bool $modalDispatched = false;

    public function hydrate(): void
    {
        if (! $this->modalDispatched) {
            $this->dispatch('openModal', [
                'categoria.create-categoria',
                'categoria.edit-categoria'
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
                ->sortable()
                ->searchable(),
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Descripción'), 'descripcion'),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'openModalCategoria\', { data: ' . $model->getKey() . ' })" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Editar
                                </button>';
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Tipo'), 'id_tipo')
                ->options($this->getTipos()),
        ];
    }

    protected function getTipos()
    {
        $tiposConCategorias = CategoriaModel::has('data')->get();

        $options = $tiposConCategorias->pluck('data.nombre', 'data.id_tipo')->toArray();

        return $options;
    }

    public function crearCategoria()
    {
        $this->dispatch('crearCategoria')->to('categoria.create-categoria');
    }
}
