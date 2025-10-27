<?php

namespace App\Livewire\PermisosRoles;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\PermisoPorEmpresaModel;

class PermisosPorTipo   extends LivewireTable
{
    protected string $model = PermisoPorEmpresaModel::class;
    public $title = 'Permiso por tipo de empresa'; // Nombre del emcabezado
    public $createForm  = 'permisosRolescargarRutas'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;

    protected function columns(): array
    {
        $this->dispatch('openModal', ['permisos-roles.crear-permiso-tipo-empresa']);
        return [
            Column::make(__('Nombre de Permiso'), 'permisos.nombre')
                ->searchable(),
            Column::make(__('Tipo de Empresa'), 'tipo_empresa'),
            Column::make(__('Ruta'), 'rutas.nombre'),
            Column::make(__('Direccion Url'), 'rutas.ruta'),
            Column::make(__('Configuracion cargada'), 'con_configuracion'),
        ];
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Tipo de Empres'), 'tipo_empresa')
                ->options([
                    1 => 'Empresa',
                    2 => 'Empresa Prestadora',
                    3 => 'Empresa reguladora',
                    4 => 'Estado',
                ]),
        ];
    }

    public function permisosRolescargarRutas()
    {
        $this->dispatch('permisosRolescargarRutas')->to('permisos-roles.crear-permiso-tipo-empresa');
    }
}
