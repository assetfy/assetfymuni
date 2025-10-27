<?php

namespace App\Livewire\Estado;

use App\Models\EmpresasModel;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Helpers\IdHelper;

class TablaEstado extends LivewireTable
{
    protected string $model = EmpresasModel::class;
    public $createForm;
    public $title = ''; // Nombre del encabezado
    protected bool $useSelection = false;

    protected function query(): Builder
    {
        $panel = Auth::user()->panel_actual;
        $query = $this->model()->query();

        if ($panel === 'Estado') { // Usar comparación estricta
            $id = IdHelper::idEmpresa();
            $query->where('estado_autorizante', '=', $id);
        }else{
            $id = IdHelper::idEmpresa();
            $query->where('empresa_reguladora_autorizante', '=', $id);
        }

        // Aplicar el filtro de estado si está presente
        if ($this->filters['estado'] ?? false) {
            $query->where('estado', '=', $this->filters['estado']);
        }

        return $query;
    }

    protected function columns(): array
    {
        return [
            ImageColumn::make(__('Logo'), 'logo')
                ->size(75, 75),
            Column::make(__('Nombre'), 'razon_social')
                ->sortable()
                ->searchable(),
            Column::make(__('Autoriza'), 'autoriza'),
            Column::make(__('Estado'), 'estado'),
            Column::make(__('Detalles'), function (Model $model): string {
                $url = route('vista-solicitud-alta', ['solicitud' => $model->getKey()]);
                return '<a href="' . $url . '" class="text-blue-500 ajax-link no-underline">
                            <i class="fas fa-eye"></i> Detalle
                        </a>';
            })->clickable(false)->asHtml(),
        ];
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Estado'), 'estado')
                ->options([
                    '' => __('Todos'), // Opción para mostrar todos los estados
                    'En Revision' => __('En Revisión'),
                    'Aceptado' => __('Aceptado')
                ]),
        ];
    }
}
