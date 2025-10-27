<?php

namespace App\Livewire\Grupos;

use App\Helpers\IdHelper;
use App\Models\GruposModel;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class GruposEmpresa extends LivewireTable
{
    protected string $model = GruposModel::class;
    public $title = 'Grupos'; // Nombre del emcabezado
    public $createForm  = 'crearGrupos'; // Nombre del componente de creación predeterminado
    public $cuit;
    protected bool $useSelection = false;

    public function asignar()
    {
        $this->cuit = IdHelper::idEmpresa();
    }

    protected function query(): Builder
    {
        $this->asignar();
        $this->dispatch('openModal', ['grupos.crear-grupos-empresa', 'grupos.asignar-permisos-grupo', 'grupos.editar-usuarios-grupo']);
        return $this->model::where('cuit', $this->cuit);
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                // Botón "Editar Usuarios" – Rojo pastel
                $configuracion = sprintf(
                    '<button wire:click="$dispatch(\'EditarGrupo\', { data: %d })"
                            style="background-color: #FECACA;"
                            class="text-red-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                            title="Editar Usuarios">
                            <i class="fa-solid fa-users-gear text-base"></i>
                    </button>',
                    $model->getKey()
                );

                // Botón "Asignar Rol" – Azul pastel
                $asignarRol = sprintf(
                    '<button wire:click="$dispatch(\'AsignarRol\', { data: %d })"
                            style="background-color: #BFDBFE;"
                            class="text-blue-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                            title="Asignar Rol">
                            <i class="fa-solid fa-user-tag text-base"></i>
                    </button>',
                    $model->getKey()
                );

                return '<div class="flex space-x-2">' . $configuracion . $asignarRol . '</div>';
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Nombre del Grupo'), 'nombre'),
            Column::make(__('Descripcion del grupo'), 'descripcion'),

        ];
    }

    public function crearGrupos()
    {
        $this->dispatch('crearGrupos')->to('grupos.crear-grupos-empresa');
    }
}
