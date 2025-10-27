<?php

namespace App\Livewire\Controles\ActivosControles;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Filters\DateFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\ActivosControlesModel;
use App\Models\ActivosModel;
use App\Helpers\IdHelper;

class ActivosControles extends LivewireTable
{
    protected string $model = ActivosControlesModel::class;
    public $title = 'LISTA GENERAL DE CONTROLES DE SUS ACTIVOS'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public int $value;
    protected bool $useSelection = false;

     protected function query(): Builder
     {
        $activosIds = $this->getActivos();
        return $this->model::whereIn('act.activos_control.id_activo', $activosIds);
     }

     protected function getActivos()
     {
        $this->getValue();
        
        $activosQuery = ActivosModel::query();

        $userId = $activosQuery->where('usuario_titular', '=', $this->value);

        // Si no se encuentra ningún resultado usando cuit, búsqueda por cuil
        if ($userId->count() === 0) {
            $userId = $activosQuery->where('empresa_titular', '=', $this->value);
        }

        return $userId->pluck('id_activo');
     }

     protected function getValue()
     {
        //Obtener el valor del ID
        $this->value = IdHelper::identificador();
     }

     protected function columns(): array
     {
         return [
             Column::make(__('Nombre del Activo'), 'activo.nombre')
             ->sortable()
             ->searchable(),
             Column::make(__('Control del Activo'), 'control.nombre')
             ->sortable()
             ->searchable(),
             DateColumn::make(__('Fecha Inicio del Control'), 'fecha_inicio')
             ->sortable()
             ->format('Y-m-d'),
             DateColumn::make(__('Fecha Final del Control'), 'fecha_fin')
             ->sortable()
             ->format('Y-m-d'),         
        ];  
     }

     protected function filters(): array
     {  
        return [
            DateFilter::make(__('Fecha Inicio'), 'fecha_inicio'),
            DateFilter::make(__('Fecha Final'), 'fecha_fin')
        ];
     }
}