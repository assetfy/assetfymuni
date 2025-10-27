<?php

namespace App\Livewire\Empresas;

use Livewire\Component;

class EmpresasOrganigrama extends Component
{
    public $tablaActual = 'CrearOrganizacion';

    public function verOrganizacion()
    {
        $this->tablaActual = 'verOrganizacion';
    }
    public function CrearOrganizacion()
    {
        $this->tablaActual = 'CrearOrganizacion';
    }

    public function render()
    {
        return view('livewire.empresas.empresas-organigrama');
    }
}
