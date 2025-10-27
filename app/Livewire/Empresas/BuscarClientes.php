<?php

namespace App\Livewire\Empresas;

use App\Helpers\IdHelper;
use App\Models\ClientesEmpresaModel;
use App\Models\EmpresasModel;
use Illuminate\Support\Facades\DB;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;


class BuscarClientes extends LivewireTable
{
    protected string $model = EmpresasModel::class;
    public $title = 'Clientes en la Plataforma'; // Nombre del encabezado
    public $createForm = 'CargarClientes'; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    public $defaultEmpresaCuit;
    public $MisClientes;

    private function misClientes()
    {
        // Convertimos el identificador a array
        $identificadores = [IdHelper::identificador()];

        $this->defaultEmpresaCuit = $identificadores;

        $this->MisClientes = ClientesEmpresaModel::whereIn('empresa_cuit', $identificadores)->get();
    }

    protected function query(): Builder
    {
        $this->misClientes();
        // Obtenemos todos los CUIT de los clientes
        $clientesCuits = $this->MisClientes
            ->pluck('cliente_cuit')
            ->filter() // remueve los nulos y otros valores falsy
            ->toArray();
        $this->dispatch('openModal', ['empresas.cargar-clientes']);
        return EmpresasModel::query()
            ->where('tipo', '1')
            ->whereNotIn('cuit', $clientesCuits);
    }


    protected function columns(): array
    {
        return [
            Column::make(__('Razón Social'), 'razon_social')
                ->sortable()
                ->searchable(),
            Column::make(__('CUIT'), 'cuit')
                ->sortable()
                ->searchable(),
            Column::make(__('Localidad'), 'localidad')
                ->sortable()
                ->searchable(),
            Column::make(__('Provincia'), 'provincia')
                ->sortable()
                ->searchable(),
            Column::make(__('Actividad'), function ($model) {
                return $model->actividades ? $model->actividades->nombre : 'Sin actividad';
            })
                ->sortable()
                ->searchable(),
            Column::make(__('Acciones'), function (Model $model) {
                $botonAgregar = '
                        <button 
                            class="w-10 h-10 p-2 rounded-md bg-blue-500 hover:bg-blue-600 text-white 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            wire:click="agregarCliente(\'' . $model->cuit . '\')"
                            title="Agregar Cliente">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    ';

                $botonDetalles = '
                        <button 
                            wire:click="$dispatch(\'PrestadoraDetalle\', { data: ' . $model->getKey() . ' })" 
                            title="Detalles del Proveedor" 
                            class="w-10 h-10 p-0 rounded-md bg-transparent text-gray-700 
                                   focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 flex items-center justify-center">
                            <img 
                                src="' . asset('storage/logos/libreta-de-contactos.png') . '" 
                                alt="Detalles" 
                                class="w-8 h-8 object-cover">
                        </button>
                    ';

                return '
                        <div class="flex space-x-2">
                            ' . $botonAgregar . '
                            ' . $botonDetalles . '
                        </div>
                    ';
            })->asHtml(),

        ];
    }

    public function agregarCliente($cuit)
    {
        try {
            DB::transaction(function () use ($cuit) {
                // Convertir el defaultEmpresaCuit a string si es array
                $empresaCuit = is_array($this->defaultEmpresaCuit)
                    ? implode(',', $this->defaultEmpresaCuit)
                    : $this->defaultEmpresaCuit;

                $numero_contrato = $empresaCuit . '-' . uniqid();
                $empresa = EmpresasModel::find($cuit);
                if (!$empresa) {
                    throw new \Exception("Empresa no encontrada");
                }

                $verificado = ($empresa->estado === 'Aceptado') ? 'Si' : 'No';

                ClientesEmpresaModel::create([
                    'empresa_cuit'    => $empresaCuit,
                    'verificado'      => $verificado,
                    'cliente_cuit'    => $empresa->cuit,
                    'cuil'            => null,
                    'numero_contrato' => $numero_contrato,
                ]);
            });

            // Si la transacción es exitosa, emitimos el evento "Exito"
            $this->dispatch('Exito', [
                'title'   => 'Éxito',
                'message' => 'Cliente agregado exitosamente.'
            ]);
        } catch (\Exception $e) {
            // Si ocurre algún error, emitimos el evento "errorInfo" con el mensaje de error
            $this->dispatch('errorInfo', [
                'title'   => 'Error',
                'message' => $e->getMessage()
            ]);
        }
    }



    public function CargarClientes()
    {
        $this->dispatch('CargarClientes')->to('empresas.cargar-clientes');
    }
}
