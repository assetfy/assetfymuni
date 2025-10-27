<?php

namespace App\Livewire\Usuarios;

use App\Models\ActivosModel;
use App\Models\AuditoriasModel;
use App\Models\UbicacionesModel;
use Livewire\Component;

class SidebarUsuario extends Component
{
    public $ubicaciones;
    public $auditorias;
    public $numeroActivo;

    public $openMenu = null;
    public $openParametrizaciones = null;

    public function mount()
    {
        $user = auth()->user();
        $this->loadData($user);
    }

    public function render()
    {
        return view('livewire.usuarios.sidebar-usuario');
    }

    private function loadData($user)
    {
        $this->loadUbicaciones($user);
        $this->loadAuditorias($user);
        $this->cantidadActivo($user);
    }

    private function cantidadActivo($user)
    {
        $this->numeroActivo = ActivosModel::where('usuario_titular', $user->cuil)->count();
    }

    private function loadAuditorias($user)
    {
        $this->auditorias = AuditoriasModel::where('id_usuario', $user->id)->get();
    }

    private function loadUbicaciones($user)
    {
        $this->ubicaciones = UbicacionesModel::where('cuil', $user->cuil)->get();
    }

    public function refreshData()
    {
        $this->loadData(auth()->user());
    }

    public function maybeRefreshData()
    {
        if ($this->openMenu === null && $this->openParametrizaciones === null) {
            $this->refreshData();
        }
    }

    // Crear bienes
    public function crearActivos()
    {
        $this->dispatch('createActivos')->to('activos.create-activos');
    }
}
