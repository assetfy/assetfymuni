<?php

namespace App\Livewire\Actividad;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\EmpresasActividadesModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Models\EmpresasModel;

class ActividadesExtra extends LivewireTable
{
    protected string $model = EmpresasActividadesModel::class;
    public $title = 'Actividades'; // Nombre del encabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $cuitEmpresaSeleccionado, $empresa;
    protected bool $useSelection = false;

    public function asignar()
    {
        $this->cuitEmpresaSeleccionado = session()->get('cuitEmpresaSeleccionado');
        if ($this->cuitEmpresaSeleccionado == null) {
            $this->cuitEmpresaSeleccionado = Auth::user()->entidad;
        }
        $this->empresa = EmpresasModel::where('cuit', $this->cuitEmpresaSeleccionado)->value('tipo');
    }

    protected function query(): Builder
    {
        $this->asignar();
        if ($this->empresa == '3') {
            $query = $this->model()::query()->where('empresa_reguladora_autorizante', '=', $this->cuitEmpresaSeleccionado);
        } else {
            $query = $this->model()::query()->where('estado_autorizante', '=', $this->cuitEmpresaSeleccionado);
        }
        return $query; 
    } 

    protected function columns(): array
    {
        $this->asignar();

        return array_merge($this->commonColumns(), $this->empresa == '3' ? $this->specificColumnsEmpresa() : $this->specificColumnsEstado());
    }

    protected function commonColumns(): array
    {
        return [
            Column::make(__('Codigo Actividad'), 'cod_actividad'),
            Column::make(__('Cuit'), 'cuit')
                ->sortable()
                ->searchable(),
            Column::make(__('Ultima Habilitacion'), 'ultima_habilitacion'),
            Column::make(__('Actividad Economica'), 'actividadEconomica.nombre'),
            Column::make(__('Provincia'), 'provincia'),
            Column::make(__('Localidad'), 'localidad'),
            Column::make(__('Estado'), 'estado'),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'openModalActividadExtra\', { data: ' . $model->getKey() . ' })" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Editar
                                </button>';
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }

    protected function specificColumnsEmpresa(): array
    {
        return [
            Column::make(__('Autorizacion'), 'autorizacion_empresa_reg'),
        ];
    }

    protected function specificColumnsEstado(): array
    {
        return [
            Column::make(__('Autorizacion'), 'autorizacion_estado'),
        ];
    }
}
