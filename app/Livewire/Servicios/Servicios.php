<?php

namespace App\Livewire\Servicios;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\ServiciosModel;


class Servicios  extends LivewireTable
{
    protected string $model = ServiciosModel::class;
    public $title = 'Servicios'; // Nombre del emcabezado
    public $createForm = 'crearServicio'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;

    public function update($value)
    {
        $this->dispatch('openModal', ['serviciosId' => $value])->to('servicios.editar-servicios');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Descripcion'), 'descripcion'),
        ];
    }
    
    public function crearServicio(){
        $this->dispatch('crearServicios')->to('servicios.create-servicios');
    }
}
