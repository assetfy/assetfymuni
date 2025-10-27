<?php

namespace App\Livewire\TiposCampo;

use App\Models\TiposCamposModel;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;

class TiposCampos extends LivewireTable
{
    protected string $model = TiposCamposModel::class;
    public $title = 'TIPOS CAMPOS'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
  
      protected function columns(): array
      {
          return [
              Column::make(__('Nombre'), 'nombre')
              ->sortable()
              ->searchable(),
          ];  
      }
}