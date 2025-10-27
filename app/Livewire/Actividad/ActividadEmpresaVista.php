<?php

namespace App\Livewire\Actividad;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\EmpresasActividadesModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Helpers\IdHelper;
class ActividadEmpresaVista extends LivewireTable
{
    protected string $model =  EmpresasActividadesModel::class;
    public $title = 'Actividades'; // Nombre del emcabezado
    public $createForm = 'CrearNuevaActividadEmpresa';
    public int $userId;
    protected bool $useSelection = false;

    protected function columns(): array
    {
        return [
            Column::make(__('Estado'),'estado'),
            Column::make(__('Código Actividad'), 'cod_actividad')
            ->sortable()
            ->searchable(),
            Column::make(__('Autoriza'), 'empresa.autoriza'),
            Column::make(__('Actividad Económica'), 'actividadEconomica.nombre'),
            Column::make(__('Acciones'), function (Model $model): string {
                return '<button wire:click="$dispatch(\'EditarNuevaActividadEmpresa\', { data: ' . $model->getKey() . ' })" 
                                class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                Editar
                                </button>';
            })
                ->clickable(false)
                ->asHtml(),
        ];  
    }

    public function asignar(){
        $this->userId = IdHelper::identificador();
    }

    protected function query(): Builder
    {
        $user = Auth::user()->entidad;
        $query = $this->model()->query()->where('act.empresas_actividades.cuit', '=', $user);
        return $query;
    }

    public function   CrearNuevaActividadEmpresa(){
        $this->dispatch('CrearNuevaActividadEmpresa')->to('actividad.actividad-nueva-actividad-empresa');
    }
}
