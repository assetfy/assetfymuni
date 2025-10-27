<?php

namespace App\Livewire\Actividad;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\ActividadesEconomicasModel;
use Illuminate\Database\Eloquent\Model;


class EstadoActividad  extends LivewireTable
{
    protected string $model = ActividadesEconomicasModel::class;
    protected bool $useSelection = false;
    public $title = 'Actividades'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado

    protected function columns(): array
    {
        return [
            ImageColumn::make(__('Imagen'), 'logo')
            ->size(75, 75),
            Column::make(__('Codigo de actividad'), 'COD_ACTIVIDAD')
                ->sortable()
                ->searchable(),
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'EditarEstadoActividad\', { data: ' . $model->getKey() . ' })" 
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
            SelectFilter::make(__('Estado'), 'estado')
                ->options([
                    1 => 'Activo',
                    0 => 'Inactivo',
                ]),
        ];
    }
}
