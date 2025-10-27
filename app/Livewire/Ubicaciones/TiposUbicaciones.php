<?php

namespace App\Livewire\Ubicaciones;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\TiposUbicacionesModel;


class TiposUbicaciones extends LivewireTable
{
   
    protected string $model = TiposUbicacionesModel::class;
    public $title = 'Tipos de Ubicaciones'; // Nombre del emcabezado
    public $createForm =  'crearubicacion'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
 
     protected function columns(): array
     {
         return [
             Column::make(__('Nombre'), 'nombre')
             ->sortable()
             ->searchable(),
         ];  
     }

     public function crearubicacion(){
        $this->dispatch('crearTipoUbicacion')->to('ubicaciones.crear-tipos-ubicaciones');
    }
}



