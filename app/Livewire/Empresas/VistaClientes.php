<?php

namespace App\Livewire\Empresas;

use App\Helpers\IdHelper;
use App\Models\ClientesEmpresaModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class VistaClientes extends Component
{
    public $datos, $userId, $admin, $clientes;
    public $tablaActual = 'mis-clientes';

    public function mount()
    {
        $this->userId = IdHelper::identificador();
        $this->cargarDatos();

        // Contador Prestadora
        $this->clientes = $this->misClientes()->count();
    }

    private function cargarDatos()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Admin Empresa Prestadora')
            ->pluck('id_rol');

        $this->admin = \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();
    }

    // Método para mostrar la tabla de tus clientes
    public function mostrarClientes()
    {
        $this->tablaActual = 'mis-clientes';
    }

    // Método para mostrar la tabla de Lista de clientes 
    public function buscarClientes()
    {
        $this->tablaActual = 'buscar-clientes';
    }

    private function misClientes()
    {
        return ClientesEmpresaModel::where('empresa_cuit', IdHelper::idEmpresa())
            ->get();
    }

    public function render()
    {
        return view('livewire.empresas.vista-clientes');
    }
}
