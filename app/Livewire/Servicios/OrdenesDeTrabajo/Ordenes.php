<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;

use Livewire\Component;
use App\Helpers\IdHelper;
use App\Models\OrdenesModel;

class Ordenes extends Component
{
    public $datos, $esApoderado, $apoderado, $tipoEmpresa, $cantOrdenesGestora, $cantOrdenesPendientes, $rol;
    public $ordenesTrabajo, $ordenesNoAsignadas, $rolManager, $rolTecnico, $usuarioEmpresa, $Empresa;
    public $tablaActual = ''; // Valor predeterminado

    public function mount()
    {
        $this->Empresa = IdHelper::empresaActual();
        $this->usuarioEmpresa = IdHelper::usuarioEmpresaActual();
        $this->apoderado = $this->cargarDatos();
        $this->rol = $this->rolUsuario();

        // Contadores Gestora
        $this->cantOrdenesGestora = $this->cantOrdenesGestora();
        $this->cantOrdenesPendientes = $this->cantOrdenesGestoraPendientes();

        // Contadores Prestadora
        $this->ordenesTrabajo = $this->ordenesGeneradas();
        $this->ordenesNoAsignadas = $this->ordenesNoAsignadas();
        $this->rolManager = $this->rolManager();
        $this->rolTecnico = $this->rolTecnico();

        if ($this->Empresa->tipo == '2') {
            $this->tablaActual = 'mostrarMisOrdenes';
        } elseif ($this->Empresa->tipo == '1' && ($this->apoderado || $this->rol)) {
            $this->tablaActual = 'misOrdenesSolicitadas';
        }
    }

    private function cargarDatos(): bool
    {
        // Si no se cargó ningún registro, devolvemos false
        if (! $this->usuarioEmpresa) {
            return false;
        }
        // Comprobamos los dos casos sobre el mismo objeto
        return $this->usuarioEmpresa->cargo === 'Apoderado'
            || $this->usuarioEmpresa->tipo_user === '2';
    }

    private function rolUsuario()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Admin Empresa')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::where('usuario_empresa',  $this->usuarioEmpresa->id_usuario)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', $this->Empresa->cuit)
            ->exists();
    }

    private function rolManager()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Prestadora - Manager')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::where('usuario_empresa', $this->usuarioEmpresa->id_usuario)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', $this->Empresa->cuit)
            ->exists();
    }

    private function rolTecnico()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Usuario Tecnico Empresa Prestadora')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::where('usuario_empresa', $this->usuarioEmpresa->id_usuario)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', $this->Empresa->cuit)
            ->exists();
    }

    public function misOrdenesSolicitadas()
    {
        $this->tablaActual = 'misOrdenesSolicitadas';
    }

    private function cantOrdenesGestora()
    {
        return OrdenesModel::where('cuit_Cliente', $this->Empresa->cuit)
            ->where('estado_vigencia', 'Cerrado')
            ->count();
    }

    public function misOrdenesPendientes()
    {
        $this->tablaActual = 'misOrdenesPendientes';
    }

    private function cantOrdenesGestoraPendientes()
    {
        return OrdenesModel::where('cuit_Cliente', $this->Empresa->cuit)
            ->where('estado_vigencia', 'Activo')
            ->count();
    }

    public function mostrarMisOrdenes()
    {
        $this->tablaActual = 'mostrarMisOrdenes';
    }

    public function OrdenesSinAsignar()
    {
        $this->tablaActual = 'OrdenesSinAsignar';
    }

    public function  OrdenesClientes()
    {
        $this->tablaActual = 'OrdenesClientes';
    }

    public function mostrarOrdenesGeneradas()
    {
        $this->tablaActual = 'mostrarOrdenesGeneradas';
    }

    private function ordenesGeneradas()
    {
        return OrdenesModel::where('proveedor', $this->Empresa->cuit)->count();
    }

    private function ordenesNoAsignadas()
    {
        return OrdenesModel::where('proveedor', $this->Empresa->cuit)
            ->whereNull('representante_tecnico')->count();
    }

    public function render()
    {
        return view('livewire.servicios.ordenes-de-trabajo.ordenes');
    }
}
