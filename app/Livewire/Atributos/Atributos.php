<?php

namespace App\Livewire\Atributos;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use App\Models\AtributosModel;

class Atributos extends LivewireTable
{
    protected string $model = AtributosModel::class;
    public $title = 'Atributos'; // Nombre del emcabezado
    public $createForm = 'crearAtributos'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    // marca si ya lanzamos el modal como una bandera 
    protected bool $modalDispatched = false;

    public function hydrate(): void
    {
        if (! $this->modalDispatched) {
            $this->dispatch('openModal', [
                'Atributos.create-atributos',
                'atributos.editar-atributos'
            ]);
            $this->modalDispatched = true;
        }
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Tipo Campo'), 'tiposCampos.nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Unidad de Medida'), 'unidadMedida.nombre'),
            Column::make(__('Descripción'), 'descripcion'),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'openEditAtributos\', { data: ' . $model->getKey() . ' })" 
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
            SelectFilter::make(__('Tipo de Campo'), 'tipo_campo')
                ->options($this->getTipos()),
            SelectFilter::make(__('Unidad'), 'unidad_medida')
                ->options($this->getUnidad()),
        ];
    }

    protected function getTipos()
    {
        $tipos = AtributosModel::has('tiposCampos')->get();

        $options = $tipos->pluck('tiposCampos.nombre', 'tiposCampos.id_tipo_campo')->toArray();

        return $options;
    }

    protected function getUnidad()
    {
        $unidad = AtributosModel::has('unidadMedida')->get();

        $value = $unidad->pluck('unidadMedida.nombre', 'unidadMedida.id_unidad_medida')->toArray();

        return $value;
    }

    public function crearAtributos()
    {
        $this->dispatch('crearAtributos')->to('Atributos.create-atributos');
    }
}
