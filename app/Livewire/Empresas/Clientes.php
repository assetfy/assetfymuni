<?php

namespace App\Livewire\Empresas;

use App\Helpers\IdHelper;
use App\Models\ClientesEmpresaModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;

class Clientes extends LivewireTable
{
    protected string $model = ClientesEmpresaModel::class;

    public $title = 'Clientes';              // Encabezado
    public $createForm = 'CargarClientes';   // Componente de creación
    public $editForm = '';                   // Componente de edición (si aplica)
    public $empresa;

    protected bool $useSelection = false;
    protected bool $modalDispatched = false;

    protected function query(): Builder
    {
        return $this->model()::query()
            ->where('empresa_cuit', '=', IdHelper::idEmpresa())
            ->with(['usuarios', 'empresa']); // evitar N+1 para mostrar columnas
    }

    public function hydrate(): void
    {
        if (! $this->modalDispatched) {
            $this->dispatch('openModal', ['empresas.cargar-clientes', 'empresas.editar-clientes']);
            $this->modalDispatched = true;
        }
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Nro Cliente'), 'numero_cliente')
                ->sortable()
                ->searchable(),

            // CLIENTE (muestra accessor display_usuario; busca y ordena por usuarios.name / empresa.razon_social)
            Column::make(__('Cliente'), 'usuarios.name')
                ->displayUsing(function (mixed $value, Model $model): string {
                    /** @var \App\Models\ClientesEmpresaModel $model */
                    return $model->display_usuario;
                })
                ->searchable(function (Builder $builder, mixed $search): void {
                    $builder
                        ->orWhere('usuarios.name', 'like', '%' . $search . '%')
                        ->orWhere('empresa.razon_social', 'like', '%' . $search . '%');
                })
                ->sortable(function (Builder $builder, $direction): void {
                    // Compat: enum Direction o string
                    $dir = is_string($direction) ? $direction : ($direction->value ?? 'asc');
                    $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';
                    $builder->orderByRaw("COALESCE(usuarios.name, empresa.razon_social) {$dir}");
                }),

            // CUIL / CUIT (muestra accessor display_cuil; busca y ordena por COALESCE(cuil, cliente_cuit))
            Column::make(__('Cuil/Cuit'), 'cuil')
                ->displayUsing(function (mixed $value, Model $model): string {
                    /** @var \App\Models\ClientesEmpresaModel $model */
                    return $model->display_cuil;
                })
                ->searchable(function (Builder $builder, mixed $search): void {
                    $m = $builder->getModel();
                    $cuil = $m->qualifyColumn('cuil');           // act.clientes_empresa.cuil
                    $cuit = $m->qualifyColumn('cliente_cuit');   // act.clientes_empresa.cliente_cuit

                    $builder
                        ->orWhere($cuil, 'like', '%' . $search . '%')
                        ->orWhere($cuit, 'like', '%' . $search . '%');
                })
                ->sortable(function (Builder $builder, $direction): void {
                    $m = $builder->getModel();
                    $cuil = $m->qualifyColumn('cuil');
                    $cuit = $m->qualifyColumn('cliente_cuit');

                    $dir = is_string($direction) ? $direction : ($direction->value ?? 'asc');
                    $dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

                    $builder->orderByRaw("COALESCE({$cuil}, {$cuit}) {$dir}");
                }),

            Column::make(__('Provincia'), 'empresa.provincia')
                ->sortable()
                ->searchable(),

            Column::make(__('Localidad'), 'empresa.localidad')
                ->sortable()
                ->searchable(),

            Column::make(__('Contratos'), 'contratos_html')
                ->asHtml(),

            Column::make(__('Acciones'), function (Model $model): string {
                if ($model->estado == 0) {
                    return <<<HTML
                    <button
                        wire:click="activarCliente({$model->getKey()})"
                        class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                    >
                        Activar
                    </button>
                    HTML;
                }

                $id = $model->getKey();

                return <<<HTML
                <button
                    wire:click="\$dispatch('openEditarCliente', { data: {$id} })"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-2"
                >
                    Editar
                </button>
                <button
                    wire:click="eliminarCliente({$id})"
                    class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                >
                    Desactivar
                </button>
                HTML;
            })
                ->clickable(false)
                ->asHtml(),
        ];
    }

    public function activarCliente($id): void
    {
        $cliente = ClientesEmpresaModel::find($id);

        if (! $cliente) {
            $this->dispatch('errorInfo', [
                'title'   => 'Error',
                'message' => 'Cliente no encontrado.',
            ]);
            return;
        }

        $cliente->estado = 1;
        $cliente->save();

        $this->dispatch('Exito', [
            'title'   => '¡Listo!',
            'message' => 'Cliente activado correctamente.',
        ]);

        $this->dispatch('refreshLivewireTable');
    }

    public function eliminarCliente($id): void
    {
        $cliente = ClientesEmpresaModel::find($id);

        if (! $cliente) {
            $this->dispatch('errorInfo', [
                'title'   => 'Error',
                'message' => 'Cliente no encontrado.',
            ]);
            return;
        }

        $cliente->estado = 0;
        $cliente->save();

        $this->dispatch('Exito', [
            'title'   => '¡Listo!',
            'message' => 'Cliente desactivado correctamente.',
        ]);

        $this->dispatch('refreshLivewireTable');
    }

    public function CargarClientes(): void
    {
        $this->dispatch('CargarClientes')->to('empresas.cargar-clientes');
    }
}
