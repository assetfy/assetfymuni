<?php

namespace App\Livewire\Subcategoria;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use App\Models\SubcategoriaModel;


class Subcategoria extends LivewireTable
{
    protected string $model = SubcategoriaModel::class;
    public $title = 'Subcategoria'; // Nombre del emcabezado
    public $createForm = 'crearSubcategoria'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    protected bool $modalDispatched = false;

    public function hydrate(): void
    {
        if (! $this->modalDispatched) {
            $this->dispatch('openModal', [
                'subcategoria.create-subcategoria',
                'subcategoria.edit-subcategoria'
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
            Column::make(__('Movil o Fijo'), 'movil_o_fijo'),
            Column::make(__('Se relaciona'), 'se_relaciona'),
            Column::make(__('Descripción'), 'descripcion'),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'openModalSubcategoria\', { data: ' . $model->getKey() . ' })" 
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
            SelectFilter::make(__('Categoria'), 'id_categoria')
                ->options($this->getCategorias())
        ];
    }

    protected function getTipos()
    {
        $tiposConCategorias = SubcategoriaModel::has('tipos')->get();

        $options = $tiposConCategorias->pluck('tipos.nombre', 'tipos.id_tipo')->toArray();

        return $options;
    }

    protected function getCategorias()
    {
        $categorias = SubcategoriaModel::has('categoria')->get();

        $values = $categorias->pluck('categoria.nombre', 'categoria.id_tipo')->toArray();

        return $values;
    }

    public function crearSubcategoria()
    {
        $this->dispatch('crearSubcategoria')->to('subcategoria.create-subcategoria');
    }
}
