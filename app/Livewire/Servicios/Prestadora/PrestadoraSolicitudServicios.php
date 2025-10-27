<?php

namespace App\Livewire\Servicios\Prestadora;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\SolicitudesServiciosModel;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\IdHelper;

class PrestadoraSolicitudServicios extends LivewireTable
{
    protected string $model = SolicitudesServiciosModel::class;
    public $title = 'SERVICIOS SOLICITADOS'; // Nombre del encabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public $editForm = 'servicios.prestadora.prestadora-servicios-solicitudes-editar-estado'; // Nombre del componente de edición predeterminado
    public $cuit, $municipio;

    public function update($value)
    {
        $this->dispatch('openModaltabla', ['$data' => $value]);
    }

    protected function query(): Builder
    {
        $servicios = $this->getServicios();
        return $this->model::whereIn('act.solicitudes_servicios.id_solicitud', $servicios)
                        ->whereNotIn('estado_presupuesto', ['confirmado por Cliente y esperando visita']);
    }

    protected function getServicios()
    {
        $this->getValue();
        
        $consulta = SolicitudesServiciosModel::query();

        $servicioSolicitados = $consulta->where('empresa_prestadora', $this->cuit)->get();

        return $servicioSolicitados->pluck('id_solicitud');
    }

    protected function getValue()
    {
        $this->cuit = IdHelper::idEmpresa();
    }

    protected function columns(): array
    {
        return [
            ImageColumn::make(__('Foto'), 'foto')
                ->size(75, 75),
            Column::make(__('Servicio'), 'servicios.nombre'),
            Column::make(__('Tipo'), 'tipos.nombre'),
            Column::make(__('Categoria'), 'categorias.nombre'),
            Column::make(__('Subcategoria'), 'subcategorias.nombre'),
            Column::make(__('Empresa Solicitante'), 'empresasSolicitantes.razon_social'),
            Column::make(__('Empresa Prestadora'), 'empresasPrestadora.razon_social'),
            Column::make(__('Solicitante'), 'users.name'),
            DateColumn::make(__('Fecha'), 'fechaHora')
                ->format('Y-m-d'),
            Column::make(__('Descripcion'), 'descripcion'),
            Column::make(__('Estado'), 'estado'),
            Column::make(__('Precio'), 'precio'),
            Column::make(__('Presupuesto'), 'presupuesto'),
            Column::make(__('Estado Presupuesto'), 'estado_presupuesto'),
        ];  
    }

    public function canSelect(): bool
    {
        // Se mantiene la lógica general de selección
        return $this->useSelection && !$this->isReordering();
    }

    public function isSelectable($row): bool
    {
        // Lógica específica de selección por fila
        return $row->precio == null;
    }
}
