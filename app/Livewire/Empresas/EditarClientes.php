<?php

namespace App\Livewire\Empresas;

use App\Models\ClientesEmpresaModel;
use App\Models\ContratoClienteModel;
use App\Models\EmpresasModel;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditarClientes extends Component
{
    protected $listeners = ['openEditarCliente'];
    public $open = false;
    public $clientes;
    public $nombreCliente;
    public $contratos = [];           // Cada elemento: ['id_contrato' => int|null, 'contrato' => string]
    public $deletedContratoIds = [];  // IDs de contratos a borrar

    public function openEditarCliente($data)
    {
        $clienteId = $data;
        $this->clientes = ClientesEmpresaModel::find($clienteId);
        if (! $this->clientes) return;

        // Nombre del cliente
        if ($this->clientes->verificado === 'Si') {
            $this->nombreCliente = $this->clientes->display_usuario;
        } else {
            $campo = $this->clientes->cuil ? 'cuil' : 'cliente_cuit';
            $valor = $this->clientes->$campo;
            $this->nombreCliente = EmpresasModel::where('cuit', $valor)->value('razon_social')
                ?: User::where('cuil', $valor)->value('name');
        }

        // Cargamos todos los contratos existentes
        $this->contratos = ContratoClienteModel::where('id_clientes_empresa', $clienteId)
            ->get(['id_contrato', 'contrato'])
            ->map(fn($c) => [
                'id_contrato' => $c->id_contrato,
                'contrato'    => $c->contrato,
            ])
            ->toArray();

        $this->deletedContratoIds = [];
        $this->open = true;
    }

    public function agregarContrato()
    {
        $this->contratos[] = ['id_contrato' => null, 'contrato' => ''];
    }

    public function eliminarContrato(int $idContrato)
    {
        if ($idContrato) {
            $this->deletedContratoIds[] = $idContrato;
        }
        // Filtramos el array para quitarlo de la UI
        $this->contratos = array_filter(
            $this->contratos,
            fn($c) => $c['id_contrato'] !== $idContrato
        );
    }

    public function actualizarCliente()
    {
        // Validaciones
        $rules = ['nombreCliente' => 'required|string|min:3'];
        foreach ($this->contratos as $i => $c) {
            $rules["contratos.{$i}.contrato"] = 'required|string|max:100';
        }
        $this->validate($rules);

        DB::transaction(function () {
            // 1) Actualizar nombre si no está verificado
            if ($this->clientes->verificado === 'No') {
                $campo = $this->clientes->cuil ? 'cuil' : 'cliente_cuit';
                $valor = $this->clientes->$campo;
                if (EmpresasModel::where('cuit', $valor)->exists()) {
                    EmpresasModel::where('cuit', $valor)
                        ->update(['razon_social' => $this->nombreCliente]);
                } elseif (User::where('cuil', $valor)->exists()) {
                    User::where('cuil', $valor)
                        ->update(['name' => $this->nombreCliente]);
                }
            }

            // 2) Borrar contratos marcados
            if (! empty($this->deletedContratoIds)) {
                ContratoClienteModel::whereIn('id_contrato', $this->deletedContratoIds)
                    ->delete();
            }

            // 3) Crear o actualizar los contratos restantes
            foreach ($this->contratos as $c) {
                if ($c['id_contrato']) {
                    ContratoClienteModel::where('id_contrato', $c['id_contrato'])
                        ->update(['contrato' => $c['contrato']]);
                } else {
                    ContratoClienteModel::create([
                        'id_clientes_empresa' => $this->clientes->id_clientes_empresa,
                        'contrato'           => $c['contrato'],
                    ]);
                }
            }
        });

        $this->dispatch('Exito', [
            'title'   => '¡Listo!',
            'message' => 'Datos actualizados correctamente.',
        ]);

        $this->open = false;
        $this->dispatch('refreshLivewireTable');
    }

    public function render()
    {
        return view('livewire.empresas.editar-clientes');
    }
}
