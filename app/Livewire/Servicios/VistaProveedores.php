<?php

namespace App\Livewire\Servicios;

use App\Helpers\IdHelper;
use App\Models\MisProveedoresModel;
use Livewire\Component;

class VistaProveedores extends Component
{
    // Por defecto se muestra "Mis Proveedores"
    public $tablaActual = 'mis-provedores-favoritos';
    public $empresa, $cantProveedores;

    public function mount()
    {
        $this->empresa = IdHelper::idEmpresa();
        $this->cantProveedores = $this->contarProveedores();
    }

    private function contarProveedores()
    {
        return MisProveedoresModel::where('empresa', $this->empresa)
            ->where('id_usuario', Auth()->user()->id)
            ->count();
    }

    // Método para mostrar la tabla de "Mis Proveedores"
    public function mostrarMisProveedores()
    {
        $this->tablaActual = 'mis-provedores-favoritos';
    }

    // Método para mostrar la tabla de "Proveedores"
    public function mostrarProveedores()
    {
        $this->tablaActual = 'proveedores';
    }

    public function render()
    {
        return view('livewire.servicios.vista-proveedores');
    }
}
