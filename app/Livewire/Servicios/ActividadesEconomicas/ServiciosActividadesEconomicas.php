<?php

namespace App\Livewire\Servicios\ActividadesEconomicas;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use App\Models\ServiciosActividadesEconomicasModel;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Support\Facades\Auth;
use App\Models\EmpresasModel;
class ServiciosActividadesEconomicas extends LivewireTable
{
    protected string $model = ServiciosActividadesEconomicasModel::class;
    public $title = 'Servicios Actividades Economicas'; // Nombre del emcabezado
    public $createForm = 'CrearServicioActividadEconomica'; // Nombre del componente de creación predeterminado
    public $municipio, $cuit;
    protected bool $useSelection = false;

    public function asignar()
    {
        $this->cuit = session()->get('cuitEmpresaSeleccionado');
        if($this->cuit == null){
            $this->cuit = Auth::user()->entidad;
        }

        $this->municipio = EmpresasModel::where('cuit', $this->cuit)->value('tipo');
    }

    protected function columns(): array
    {
        $this->asignar();

        return $this->municipio == '4' ? $this->columnsForMunicipio4() : $this->columnsForOtherMunicipios();
    }

    protected function columnsForMunicipio4(): array
    {
        return array_merge($this->commonColumns(), [
            Column::make(__('Localidad'), 'localidad'),
            Column::make(__('Vencimiento'), 'tiene_vencimiento'),
            Column::make(__('Tiempo Vencimiento'), 'mensual_o_x_dias'),
            Column::make(__('Tiempo Estimado'), 'cantidad_dias_o_meses'),
            Column::make(__('Act. Regulada'), 'es_regulada'),
        ]);
    }

    protected function commonColumns(): array
    {
        return [
            Column::make(__('Servicio'), 'servicios.nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Actividades'), 'actividadesEconomicas.nombre'),
        ];
    }

    protected function columnsForOtherMunicipios(): array
    {
        return $this->commonColumns();
    }

    public function CrearServicioActividadEconomica(){
        $this->dispatch('CrearServicioActividadEconomica')->to('servicios.actividadeseconomicas.create-servicios-actividades-economicas');
    }
}
