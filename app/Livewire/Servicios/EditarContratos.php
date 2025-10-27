<?php

namespace App\Livewire\Servicios;

use App\Helpers\IdHelper;
use App\Models\EmpresasModel;
use App\Models\MisProveedoresModel;
use App\Models\provedoresContratosModel;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditarContratos extends Component
{
    protected $listeners = ['openEditContrato'];
    public $open = false;
    public $proveedor, $nombreEmpresa, $cuitEmpresa, $numeroContrato, $fecha;
    // Reglas de validación para los campos de contrato
    protected function rules()
    {
        return [
            'numeroContrato' => 'required|string|max:10',
            'fecha'            => 'required|date',
        ];
    }

    public function openEditContrato($data)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->datos($data);
            $this->open = true;
        }
    }

    public function actualizarContrato()
    {
        // Validar todos los datos, incluidos los campos de contrato cuando corresponda.
        $this->validate();

        DB::beginTransaction();
        try {
            // Preparamos los datos para el registro del proveedor.
            $data = [
                'existe_en_la_plataforma' => 'Si',
                'razon_social'            => $this->proveedor->razon_social,
                'cuit'                    => $this->proveedor->cuit,
                'localidad'               => $this->proveedor->localidad,
                'provincia'               => $this->proveedor->provincia,
                'id_usuario'              => auth()->id(),
                'email'                   => $this->proveedor->email ?? null,
                'url'                     => $this->proveedor->url ?? null, // Corrige si antes había un typo con "provedor"
                'empresa'                 => IdHelper::idEmpresa(),
            ];

            // Buscar si ya existe el proveedor en favoritos (por cuit y empresa)
            $nuevoProveedor = MisProveedoresModel::where('cuit', $this->proveedor->cuit)
                ->where('empresa', IdHelper::idEmpresa())
                ->first();
            // Si el registro no existe, se crea
            if (!$nuevoProveedor) {
                $nuevoProveedor = MisProveedoresModel::create($data);
            }

            $contratoData = [
                'numero' => $this->numeroContrato,
                'fecha'  => $this->fecha,
            ];

            provedoresContratosModel::updateOrCreate(
                ['id_mis_proveedor' => $nuevoProveedor->id],
                $contratoData
            );

            $this->dispatch('lucky');
            DB::commit();
            $this->cerrar();
            $this->dispatch('refreshLivewireTable');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('errorInfo', [
                'title'   => 'Error',
                'message' => $e->getMessage(),
            ]);
            $this->cerrar();
        }
    }


    private function cerrar()
    {
        $this->open = false;
        $this->reset(['proveedor', 'nombreEmpresa', 'cuitEmpresa', 'numeroContrato', 'fecha']);
    }

    private function datos($data)
    {
        // Si $data es un array, extraemos el cuit; de lo contrario, asumimos que $data es el cuit
        $cuit = is_array($data) ? ($data['cuit'] ?? null) : $data;
        $this->proveedor = EmpresasModel::where('cuit', $cuit)->first();
        $this->nombreEmpresa = $this->proveedor->razon_social;
        $this->cuitEmpresa   = $this->proveedor->cuit;
    }


    public function render()
    {
        return view('livewire.servicios.editar-contratos');
    }
}
