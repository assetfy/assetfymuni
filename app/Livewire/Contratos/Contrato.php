<?php

namespace App\Livewire\Contratos;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContratoModel;

class Contrato extends LivewireTable
{
    protected string $model = ContratoModel::class;
    public $title = 'Contratos'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    protected bool $modalDispatched = false;

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'))
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Número de contrato'), 'nro_contrato')
                ->sortable()
                ->searchable(),
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Prestadora'), 'prestadora')
                ->sortable()
                ->searchable(),
            Column::make(__('Estado'), 'estadoContrato.nombreEstado')
                ->sortable()
                ->searchable(),
            Column::make(__('Fecha Inicio'), 'fecha_inicio')
                ->sortable()
                ->searchable(),
            Column::make(__('Fecha Fin'), 'fecha_fin')
                ->sortable()
                ->searchable(),
        ];
    }

    // protected function filters(): array
    // {
    //     return [
    //      
    //     ];
    // }
}
