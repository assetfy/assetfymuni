<?php

namespace App\Livewire\Activos\Bienes;

use Livewire\Component;
use App\Helpers\IdHelper;

class BienesTerceros extends Component
{
    public $datos, $userId, $esApoderado, $bienesAceptados, $bienesPendientes, $adminPrestadora;
    public $tablaActual = 'bienes-aceptados-terceros'; // Valor predeterminado

    public function mount()
    {
        $this->userId = IdHelper::idEmpresa();
        $this->cargarDatos();
    }

    private function cargarDatos()
    {
        // 🔍 Verificar si el usuario es Apoderado
        $this->esApoderado = \App\Models\UsuariosEmpresasModel::where('id_usuario', auth()->id())
            ->where('cuit', $this->userId)
            ->where('cargo', 'Apoderado')
            ->exists();

        $idsRol = \App\Models\RolesModel::where('nombre', 'Admin Empresa Prestadora')
            ->pluck('id_rol');

        $this->adminPrestadora = \App\Models\AsignacionesRolesModel::where('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->where('usuario_empresa', auth()->id())
            ->exists();

        $this->bienesAceptados = \App\Models\ActivosCompartidosModel::where('estado_asignacion', 'Aceptado')
            ->where('empresa_proveedora', $this->userId)
            ->whereNull('fecha_fin')
            ->count();

        $this->bienesPendientes = \App\Models\ActivosCompartidosModel::where('estado_asignacion', 'En Revisión')
            ->where('empresa_proveedora', $this->userId)
            ->whereNull('fecha_fin')
            ->count();
    }

    public function mostrarBienesTercerosAceptados()
    {
        $this->tablaActual = 'bienes-aceptados-terceros';
    }

    public function mostrarBienesTercerosPendientes()
    {
        $this->tablaActual = 'bienes-pendientes-terceros';
    }

    public function render()
    {
        return view('livewire.activos.bienes.bienes-terceros', [
            'apoderado' => $this->esApoderado,
            'bienesAceptados' => $this->bienesAceptados,
            'bienesPendientes' => $this->bienesPendientes
        ]);
    }
}
