<?php

namespace App\Livewire\Estado;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Filters\DateFilter;
use RamonRietdijk\LivewireTables\Actions\Action;
use RamonRietdijk\LivewireTables\Columns\Column;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;
use App\Models\ServiciosActivosModel;

class ServiciosPrestadoraTabla extends LivewireTable
{
    protected string $model = ServiciosActivosModel::class;
    public $title = 'Servicios Realizados por la prestadora'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $editForm = ''; // Nombre del componente de edición predeterminado
    public int $tipoId, $idPrestadora, $id_activo;


    public function asignar()
    {
        $this->idPrestadora = Session::get('idPrestadora');
    }

    protected function query(): Builder
    {
        $this->asignar();
        $query = $this->model()->query()->where('proveedor', '=',  $this->idPrestadora);
        // Si no se encuentra ningún resultado usando cuit, busca por cuil
        return $query;
    }

    protected function columns(): array
    {
        return [
            DateColumn::make(__('Fecha'), 'fecha')
            ->sortable()
            ->format('F jS, Y'),
            Column::make(__('Servicio'), 'servicios.nombre')
            ->sortable()
            ->searchable(),
            Column::make(__('Comentarios'), 'comentarios'),
            ImageColumn::make(__('Imagen'), 'foto'),
        ];
    }

    protected function filters(): array
    {
        return [
            DateFilter::make(__('Fecga'), 'fecha'),
        ];
    }

  

}
