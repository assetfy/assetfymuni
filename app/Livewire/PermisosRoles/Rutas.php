<?php

namespace App\Livewire\PermisosRoles;

use App\Livewire\Empresas\EmpresasUsuarios\UsuariosEmpresas;
use App\Models\RutasModel;
use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;

class Rutas extends LivewireTable
{
    protected string $model = RutasModel::class;
    public $title = 'Lista de Rutas en el sistema'; // Nombre del emcabezado
    public $createForm  = 'permisosRolescargarRutas'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    public $tipoUsuario;

    protected function columns(): array
    {
        $this->dispatch('openModal', ['permisos-roles.cargar-rutas', 'permisosroles.editar-rutas', 'permisosroles.configuracion-rutas']);
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                $id = $model->getKey();

                // Botón Editar – Índigo pastel
                $editar = sprintf(
                    '<button wire:click="$dispatch(\'EditarRutas\', { data: %d })"
                        style="background-color: #C7D2FE;"
                        class="text-indigo-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                        title="Editar Ruta">
                        <i class="fa-solid fa-pen-to-square text-base"></i>
                    </button>',
                    $id
                );
                // Botón Configuración – Verde pastel (solo si configurable == "si")
                $configuracion = '';
                if (strtolower($model->configurable) === 'si') {
                    $configuracion = sprintf(
                        '<button wire:click="$dispatch(\'EditarConfiguracionRutas\', { data: %d })"
                            style="background-color: #BBF7D0;"
                            class="text-green-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                            title="Configuración de Ruta">
                            <i class="fa-solid fa-cogs text-base"></i>
                        </button>',
                        $id
                    );
                }

                return '<div class="flex space-x-2 items-center">' . $editar . $configuracion . '</div>';
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Id Ruta'), 'id_ruta'),
            Column::make(__('Nombre de la ruta'), 'nombre')
                ->searchable(),
            Column::make(__('Url'), 'ruta'),
            Column::make(__('Configurable'), 'configurable'),

        ];
    }

    public function permisosRolescargarRutas()
    {
        $this->dispatch('permisosRolescargarRutas')->to('permisos-roles.cargar-rutas');
    }
}
