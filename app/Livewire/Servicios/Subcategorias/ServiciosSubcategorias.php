<?php

namespace App\Livewire\Servicios\Subcategorias;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\ServiciosSubcategoriasModel;
use Illuminate\Database\Eloquent\Model;

class ServiciosSubcategorias extends LivewireTable
{
    protected string $model = ServiciosSubcategoriasModel::class;
    public $title = 'Servicios Subcategoria'; // Nombre del emcabezado
    public $createForm = 'CreateServiciosSubcategoria'; // Nombre del componente de creación predeterminado
    public int $userId;
    protected bool $useSelection = false;

    public function CreateServiciosSubcategoria()
    {
        $this->dispatch('CreateServiciosSubcategoria')->to('servicios.subcategorias.create-servicios-subcategorias');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Servicio'),'servicios.nombre')
                                        ->sortable()
                                        ->searchable(),
            Column::make(__('Subcategoria'), 'subcategorias.nombre'),
            Column::make(__('Categoria'), 'categorias.nombre'),
            Column::make(__('Tipos'), 'tipos.nombre'),
            Column::make(__('Requiere Fotos'), 'req_fotos_carga_inicial'),
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
        $tiposConCategorias = ServiciosSubcategoriasModel::has('tipos')->get();

        $options = $tiposConCategorias->pluck('tipos.nombre', 'tipos.id_tipo')->toArray();

        return $options;
    }

    protected function getCategorias()
    {
        $categorias = ServiciosSubcategoriasModel::has('categorias')->get();

        $values = $categorias->pluck('categorias.nombre', 'categorias.id_tipo')->toArray();

        return $values;
    }
}
