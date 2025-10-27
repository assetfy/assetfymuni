<?php

namespace App\Livewire\Roles;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Models\AsignacionesRolesModel;
use App\Helpers\IdHelper;

class AsignacionesRoles extends LivewireTable
{
    protected string $model = AsignacionesRolesModel::class;
    public $title = 'Asignación de Licencias';
    public $createForm = 'CrearAsignacionesRoles';
    public $editForm = '';
    public $value;
    public $cuit;
    protected bool $useSelection = false;

    protected function query(): Builder
    {
        $roles = $this->getRoles();
        $this->dispatch('openModal', ['roles.create-asignaciones-roles', 'roles.edit-asignaciones-roles']);
        return $this->model::whereIn('act.asignaciones_roles.cuit', $roles);
    }

    protected function getRoles()
    {
        $this->value = IdHelper::idEmpresa();
        $user = AsignacionesRolesModel::where('cuit', '=', $this->value)->pluck('cuit');
        return $user;
    }

    public function update($value)
    {
        $this->dispatch('openModalAsignacion', ['asignacionesRolesId' => $value])->to('roles.edit-asignaciones-roles');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Licencias'), 'roles.nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Permiso'), 'permiso.nombre'),
            Column::make(__('Usuario'), 'user.name'),
            Column::make(__('Empresa'), 'empresa.razon_social'),
        ];
    }

    public function CrearAsignacionesRoles()
    {
        $this->dispatch('openModalAsignarRol')->to('roles.create-asignaciones-roles');
    }
}
