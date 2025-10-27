<?php

namespace App\Livewire\Controles\ActivosControles;

use App\Models\ActivosControlesModel;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Builder;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;


class ControlDetalle extends LivewireTable
{
    protected string $model = ActivosControlesModel::class;
    public $title = 'CONTROLES DEL ACTIVO'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $editForm = ''; // Nombre del componente de edición predeterminado
    public $control,$activo;

    public function asignar(){
        $this->control = Session::get('controlId');
        $this->activo = Session::get('id_activo');
    }
    protected function query(): Builder
    {
        $this->asignar();
        $query = $this->model()->query()
            ->where('act.activos_control.id_control', '=',  $this->control)
            ->where('act.activos_control.id_activo', '=', $this->activo);
        return $query;
    }

    protected function columns(): array
     {
         return [
             Column::make(__('Activo'), 'activo.nombre')
             ->searchable(),
             Column::make(__('Controles'), 'control.nombre'),
             DateColumn::make(__('Fecha Inicio'), 'fecha_inicio')
             ->format('Y-m-d'),
             DateColumn::make(__('Fecha Final'), 'fecha_fin')
             ->format('Y-m-d'), 
         ];  
     }

     public function isSelectable($row): bool
     {
         // Lógica específica de selección por fila
         return false;
     }
}
