<?php

namespace App\Livewire\Subcategoria\AtributosSubcategorias;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\AtributosSubcategoriaModel;
use Illuminate\Database\Eloquent\Model;

class AtributosSubcategorias extends LivewireTable
{
    protected string $model = AtributosSubcategoriaModel::class;
    public $title = 'Atributos Subcategorias'; // Nombre del emcabezado
    public $createForm = 'crearAtributoSubcategoria'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    protected bool $modalDispatched = false;

    public function hydrate(): void
    {
        if (! $this->modalDispatched) {
            $this->dispatch('openModal', [
                'subcategoria.atributossubcategorias.create-atributo-subcategoria',
                'subcategoria.atributossubcategorias.edit-atributos-subcategoria'
            ]);
            $this->modalDispatched = true;
        }
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Atributo'), 'atributo.nombre')
                ->qualifyUsingAlias()
                ->sortable()
                ->searchable(),
            Column::make(__('Obligatorio Carga Inicial'), 'obligatorio_carga_ini'),
            Column::make(__('Unico'), 'unico'),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'atributoSubcategoria\', { data: ' . $model->getKey() . ' })" 
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
            SelectFilter::make(__('Subcategoria'), 'id_subcategoria')
                ->options($this->getSubcategoria())
        ];
    }

    protected function getSubcategoria()
    {
        $value = AtributosSubcategoriaModel::has('subcategoria')->get();

        $options = $value->pluck('subcategoria.nombre', 'subcategoria.id_subcategoria')->toArray();

        return $options;
    }

    public function crearAtributoSubcategoria()
    {
        $this->dispatch('crearAtributoSubcategoria')->to('subcategoria.atributossubcategorias.create-atributo-subcategoria');
    }
}
