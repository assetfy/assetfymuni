<?php

namespace App\Livewire\Empresas;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Models\EmpresasModel;

class Autorizacion extends LivewireTable
{
    protected string $model = EmpresasModel::class;
    public $title = 'Solicitudes'; // Nombre del encabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $editForm = 'actividad.actividad-autorizacion'; // Nombre del componente de edición predeterminado
    public $cuitEmpresaSeleccionado;
    public $empresa;

    private function asignar()
    {
        $this->cuitEmpresaSeleccionado = session()->get('cuitEmpresaSeleccionado');
        if($this->cuitEmpresaSeleccionado == null){
            $this->cuitEmpresaSeleccionado  = Auth::user()->entidad;
        }
        $this->empresa = EmpresasModel::where('cuit', $this->cuitEmpresaSeleccionado)->value('tipo');
    }

    protected function query(): Builder
    {
        $this->asignar();
        if($this->empresa == '3' ){
            $query = $this->model()::query()->where('empresa_reguladora_autorizante', '=', $this->cuitEmpresaSeleccionado);
        } else {
            $query = $this->model()::query()->where('estado_autorizante', '=', $this->cuitEmpresaSeleccionado);
        }
        return $query; 
    }    

    protected function columns(): array
    {
        if($this->empresa == '3'){
            return [
                Column::make(__('Cuit'), 'cuit')
                    ->sortable()
                    ->searchable(),
                Column::make(__('Razon Social'), 'razon_social'),
                Column::make(__('Actividad'), 'actividades.nombre'),
                Column::make(__('Estado'), 'estado'),
            ];  
        }else{
            return [
                Column::make(__('Cuit'), 'cuit')
                ->sortable()
                ->searchable(),
                Column::make(__('Razon Social'), 'razon_social'),
                Column::make(__('Actividad'), 'actividades.nombre'),
                Column::make(__('Estado'), 'estado'),
            ]; 
        }
    }

    public function isSelectable($row): bool
    {
        // Lógica específica de selección por fila
        return true;
    }
}
