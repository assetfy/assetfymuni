<?php

namespace App\Livewire\Empresas\Contratos;

use App\Helpers\IdHelper;
use Illuminate\Database\Eloquent\Model;
use App\Models\ContratoModel;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;

class ContratosEmpresas extends LivewireTable
{
    protected string $model = ContratoModel::class;
    public $title = 'Contratos'; // Nombre del emcabezado
    public $createForm = 'GeneralContrato'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    public $empresa, $clientes_empresas, $key, $usuario;
    protected bool $modalDispatched = false;

    public function asignar()
    {
        $this->empresa = IdHelper::empresaActual();
    }

    public function hydrate(): void
    {
        if (! $this->modalDispatched) {
            $this->dispatch('openModal', [
                'empresas.contratos.editar-contrato'
            ]);
            $this->modalDispatched = true;
        }
    }


    protected function query(): Builder
    {
        $this->asignar();
        return $this->model()->query()->where('cuit_cliente', '=', $this->empresa->cuit);
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                $id = $model->getKey();

                return '
                    <button type="button"
                        wire:click="$dispatch(\'openEditContrato\', { data: ' . $id . ' })"
                        class="inline-flex items-center justify-center h-9 w-9 rounded-lg border border-gray-300 bg-white
                            text-gray-700 hover:bg-gray-50 hover:border-gray-400
                            focus:outline-none focus:ring-2 focus:ring-blue-400 focus:ring-offset-1 shadow-sm"
                        title="Editar" aria-label="Editar">
                        <i class="fa-regular fa-pen-to-square text-[18px]"></i>
                    </button>';
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Nro Contrato'), 'nro_contrato')
                ->sortable()
                ->asHtml(),
            Column::make(__('Nombre'), 'nombre')
                ->sortable()
                ->asHtml(),
            Column::make(__('Prestadora'), 'prestadora')
                ->sortable()
                ->asHtml(),
            Column::make(__('Estado'), 'tiposContratos.nombre_estado')
                ->sortable()
                ->asHtml(),
            Column::make(__('Fecha de inicio'), 'fecha_inicio')
                ->sortable()
                ->asHtml(),
            Column::make(__('Fecha de fin'), 'fecha_fin')
                ->sortable()
                ->asHtml(),
        ];
    }

    public function GeneralContrato()
    {
        return redirect()->route('formulario-contratos');
    }
}
