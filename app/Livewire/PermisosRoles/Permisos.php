<?php

namespace App\Livewire\PermisosRoles;

use App\Helpers\IdHelper;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use App\Models\PermisosModel;
use Illuminate\Database\Eloquent\Builder;

class Permisos extends LivewireTable
{
    protected string $model = PermisosModel::class;
    public $title = 'Permisos'; // Nombre del emcabezado
    public $createForm = 'CrearPermisos'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    public $userId;

    protected function query(): Builder
    {
        $this->userId = IdHelper::identificador();
        $this->dispatch('openModal', ['permisos-roles.crear-permisos', 'permisosRoles.editar-permisos']);
        return PermisosModel::query()
            ->where('cuit_empresa', $this->userId)
            ->orderBy('nombre', 'asc');
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                return sprintf(
                    '<button 
                        wire:click="$dispatch(\'openEditarPermisos\', { data: %d })" 
                        style="background-color: #C7D2FE;"
                        class="text-indigo-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                        title="Editar Permisos">
                        <i class="fa-solid fa-pen-to-square text-base"></i>
                    </button>',
                    $model->getKey()
                );
            })
                ->clickable(false)
                ->asHtml(),

            Column::make(__('Nombre'), 'nombre'),
        ];
    }

    public function CrearPermisos()
    {
        $this->dispatch('CrearPermisos')->to('permisos-roles.crear-permisos');
    }
}
