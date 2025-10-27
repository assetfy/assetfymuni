<?php

namespace App\Livewire\Roles;

use App\Models\RolesModel;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\IdHelper;

class Roles extends LivewireTable
{
    protected string $model = RolesModel::class;
    public $title = 'Licencias';
    public $createForm = 'CrearRoles';
    public int $value;
    protected bool $useSelection = false;

    protected function query(): Builder
    {
        $roles = $this->getRoles();
        $this->dispatch('openModal', ['roles.create-roles']);
        return $this->model::where(function ($query) use ($roles) {
            $query->whereIn('act.roles.cuit', $roles)
                ->orWhereNull('act.roles.cuit');
        });
    }

    protected function getRoles()
    {
        $this->getValue();

        $rolesQuery = RolesModel::query();

        $user = $rolesQuery->where('cuit', '=', $this->value);

        return $user->pluck('cuit');
    }

    protected function getValue()
    {
        $this->value = IdHelper::idEmpresa();
    }

    public function update($value)
    {
        $this->dispatch('openModal', ['rolesId' => $value])->to('roles.editar-roles');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Empresa'), 'empresasparticulares.razon_social')
                ->sortable()
                ->searchable(),
        ];
    }

    public function CrearRoles()
    {
        $this->dispatch('CrearRoles')->to('roles.create-roles');
    }
}
