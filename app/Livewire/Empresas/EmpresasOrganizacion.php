<?php

namespace App\Livewire\Empresas;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\OrganizacionUnidadesModel;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\IdHelper;
use Illuminate\Database\Eloquent\Model;

class EmpresasOrganizacion extends LivewireTable
{
    protected string $model = OrganizacionUnidadesModel::class;
    public $title = 'Nivel'; // Nombre del encabezado
    public $createForm = 'createNivel'; // Inicialmente vacío
    public $cuit_empresa;
    protected bool $useSelection = false;

    protected $listeners = ['removerNivel', 'tablarefrescar'];

    public function asignar()
    {
        $this->cuit_empresa = IdHelper::idEmpresa();
    }

    public function tablarefrescar()
    {
        $this->dispatch('refreshLivewireTable');
    }

    protected function query(): Builder
    {
        $this->asignar();
        $this->dispatch('openModal', ['empresas.crear-niveles-organizacion', 'empresas.editar-organizacion']);
        $query = $this->model()::query()
            ->with('padre')
            ->where('act.OrganizacionUnidades.CuitEmpresa', '=', $this->cuit_empresa);
        return $query;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                $id = $model->getKey();

                $editarBtn = sprintf(
                    '<button
                        wire:click="$dispatch(\'editarOrganizacion\', { data: %d })"
                        style="background-color: #C7D2FE;"
                        class="text-indigo-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                        title="Editar">
                        <i class="fa-solid fa-pen-to-square text-base"></i>
                    </button>',
                    $id
                );
                $eliminarBtn = sprintf(
                    '<button
                        wire:click="eliminar(%d)"
                        style="background-color: #FECACA;"
                        class="text-red-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                        title="Eliminar">
                        <i class="fa-solid fa-trash text-base"></i>
                    </button>',
                    $id
                );

                return '<div class="flex space-x-2 items-center">' . $editarBtn . $eliminarBtn . '</div>';
            })
                ->clickable(false)
                ->asHtml(),

            Column::make(__('Creador'), 'Creador.name',)
                ->sortable()
                ->searchable(),
            Column::make(__('Depende de'), 'Padre.Nombre'),
            Column::make(__('Nombre'),  'Nombre'),
        ];
    }

    public function eliminar($id)
    {
        $this->dispatch('eliminarNivel', ['id' => $id]);
    }

    public function removerNivel($id)
    {
        if ($nivel = OrganizacionUnidadesModel::find($id)) {
            $nivel->delete(); // dispara deleting/deleted + cascada de hijos
        }

        $this->dispatch('refreshLivewireTable');
    }

    public function createNivel()
    {
        $this->dispatch('crearNivelOrganizacion');
    }
}
