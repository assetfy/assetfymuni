<?php

namespace App\Livewire\Unidad;

use App\Models\UnidadModel;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;

class UnidadMedida extends LivewireTable
{
    protected string $model = UnidadModel::class;
    public $title = 'Unidad de Medida'; // Nombre del emcabezado
    public $createForm = 'crearUnidadMedida'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;

    public function crearUnidadMedida()
    {
        $this->dispatch('crearUnidadMedida')->to('unidad.create-unidad');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
        ];
    }
}
