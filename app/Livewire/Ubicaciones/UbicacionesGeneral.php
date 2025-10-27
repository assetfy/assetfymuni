<?php

namespace App\Livewire\Ubicaciones;

use App\Helpers\IdHelper;
use App\Models\EmpresasModel;
use App\Models\UbicacionesModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UbicacionesGeneral extends Component
{
    public $userId, $apoderado, $admin, $tipoEmpresa, $cantUbicacionesBienes, $cantUbicacionesSinBienes;
    public $cantUbicacionesDelegadas, $cantUbicacionesNoDelegadas; // Contador de ubicaciones delegadas
    public $tablaActual = ''; // Valor predeterminado

    public function mount()
    {
        $empresa = IdHelper::empresaActual();
        $this->userId =   $empresa->cuit;
        $this->tipoEmpresa =  $empresa->tipo;

        // Contadores Gestora
        $this->cantUbicacionesBienes = $this->cantUbicacionesBienes();
        $this->cantUbicacionesSinBienes = $this->cantUbicacionesBienesSinBienes();

        // Contadores Prestadora
        $this->cantUbicacionesDelegadas = $this->cantUbicacionesDelegadas();
        $this->cantUbicacionesNoDelegadas = $this->cantUbicacionesNoDelegadas();

        $this->cargarDatos();

        if ($this->tipoEmpresa == '2') {
            $this->tablaActual = 'ubicaciones-delegadas';
        } elseif ($this->tipoEmpresa == '1' ) {
            $this->tablaActual = 'ubicaciones';
        }
    }

    private function cargarDatos()
    {
        // 🔍 Verificar si el usuario es Apoderado
        $this->apoderado = \App\Models\UsuariosEmpresasModel::where('id_usuario', auth()->id())
            ->where('cuit', $this->userId)
            ->where('cargo', 'Apoderado')
            ->exists();

        $idsRol = \App\Models\RolesModel::where('nombre', 'Admin Empresa')
            ->orWhere('nombre', 'Admin Empresa Prestadora')
            ->pluck('id_rol');

        $this->admin = \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();
    }

    public function mostrarUbicaciones()
    {
        if ($this->tipoEmpresa == '2') {
            $this->tablaActual = 'ubicaciones-delegadas';
        } else {
            $this->tablaActual = 'ubicaciones';
        }
    }

    private function cantUbicacionesBienes()
    {
        return UbicacionesModel::where('cuit', '=', $this->userId)
            ->whereHas('activos')
            ->count();
    }

    public function mostrarUbicacionesSinBienes()
    {
        $this->tablaActual = 'ubicaciones-sin-bienes';
    }

    private function cantUbicacionesBienesSinBienes()
    {
        return UbicacionesModel::where('cuit', '=', $this->userId)
            ->whereDoesntHave('activos')
            ->count();
    }

    private function cantUbicacionesDelegadas()
    {
        return UbicacionesModel::where('cuit_empresa', '=', $this->userId)
            ->whereNotNull('cuit')
            ->where('propiedad', '=', 'Cliente')
            ->whereHas('activos')
            ->count();
    }

    private function cantUbicacionesNoDelegadas()
    {
        return UbicacionesModel::where('cuit_empresa', '=', $this->userId)
            ->whereNotNull('cuit')
            ->where('propiedad', '=', 'Cliente')
            ->whereDoesntHave('activos')
            ->count();
    }

    public function render()
    {
        return view('livewire.ubicaciones.ubicaciones-general');
    }
}
