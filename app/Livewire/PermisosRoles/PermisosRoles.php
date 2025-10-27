<?php

namespace App\Livewire\PermisosRoles;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\PermisosRolesModel;
use App\Helpers\IdHelper;

class PermisosRoles extends LivewireTable
{
    protected string $model = PermisosRolesModel::class;
    public $title = 'Permisos y Licencias'; // Nombre del emcabezado
    public $createForm = 'CrearPermisosRoles'; // Nombre del componente de creación predeterminado  
    public $empresa, $user, $permisosDelUsuario;
    protected bool $useSelection = false;

    public function asignar()
    {
        $this->permisosDelUsuario = PermisosRolesModel::where('cuit_empresa', IdHelper::idEmpresa())->get();
    }

    protected function query(): Builder
    {
        $this->asignar();

        $this->dispatch('openModal', ['permisosRoles.crear-permisos-roles']);

        $permisosIds = $this->permisosDelUsuario->pluck('id_permiso')->toArray();

        $table = $this->model()->getTable();

        $query = $this->model()->query()
            ->whereIn("{$table}.id_permiso", $permisosIds);

        return $query;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Id permiso'), 'permisos.nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Licencia'), 'roles.nombre')
                ->sortable()
                ->searchable(),
        ];
    }


    public function CrearPermisosRoles()
    {
        $this->dispatch('CrearPermisosRoles')->to('permisosRoles.crear-permisos-roles');
    }
}
