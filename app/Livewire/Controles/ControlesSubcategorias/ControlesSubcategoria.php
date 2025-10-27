<?php

namespace App\Livewire\Controles\ControlesSubcategorias;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\ControlesSubcategoriaModel;
use Illuminate\Database\Eloquent\Model;

class ControlesSubcategoria extends  LivewireTable
{
    protected string $model = ControlesSubcategoriaModel::class;
    public $title = 'Controles Subcategorias';
    public $createForm = 'CrearControlesSubcategorias'; 
    protected bool $useSelection = false;

    public function update($value)
    {
        $this->dispatch('openModal', ['controlesSubcategoriaId' => $value])->to('controles.controlessubcategorias.editar-controles-subcategoria');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Control'), 'controles.nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Obligatorio Carga Inicial'), 'obligatorio_carga_ini')
                ->sortable(),
            Column::make(__('Es periodico'), 'es_periodico'),
            Column::make(__('Frecuencia de Control'), 'frecuencia_control'),
            Column::make(__('Unico'), 'unico'),
            Column::make(__('Requiere Fotos'), 'req_foto'),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'openControlesSubcategoria\', { data: ' . $model->getKey() . ' })" 
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
            SelectFilter::make(__('Subcategoria'), 'categoria.id_categoria')
                ->options($this->getSubcategorias()),
        ];
    }

    protected function getSubcategorias()
    {
        $categorias = ControlesSubcategoriaModel::has('subcategoria')->get();

        $values = $categorias->pluck('subcategoria.nombre', 'subcategoria.id_tipo')->toArray();

        return $values;
    }

    public function CrearControlesSubcategorias(){
        $this->dispatch('CrearControlesSubcategorias')->to('controles.controlessubcategorias.create-controles-subcategoria');
    }
}
