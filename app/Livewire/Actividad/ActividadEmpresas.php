<?php

namespace App\Livewire\Actividad;

use App\Models\EmpresasActividadesModel;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;

class ActividadEmpresas extends LivewireTable
{
    protected string $model = EmpresasActividadesModel::class;
    public $title = 'Actividades'; // Nombre del emcabezado
    public $createForm = 'CargarReguladora'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;

    protected function columns(): array
    {
        return [
            Column::make(__('Empresa'), 'empresa.razon_social'),
            Column::make(__('Código Actividad'), 'cod_actividad')
            ->sortable()
            ->searchable(),
            Column::make(__('Actividad Económica'), 'actividadEconomica.nombre'),
            Column::make(__('Autoriza'), 'autoriza'),
            Column::make(__('Estado'), 'estado'),
        ];  
    }

    public function CargarReguladora(){
        $this->dispatch('CargarReguladora')->to('actividad.cargar-reguladora');
    }
}
