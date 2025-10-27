<?php

namespace App\Livewire\Empresas\EmpresasUsuarios;

use App\Helpers\IdHelper;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Usuarios extends Component
{
    public $usuariosRegistrados, $usuariosActivos, $usuariosSinPermisos;
    public $tablaActual = 'usuarios-empresas'; // Valor predeterminado

    public function mount()
    {
        $this->usuariosRegistrados = $this->usuariosRegistradosCount();
        $this->usuariosActivos = $this->usuariosActivosCount();
        $this->usuariosSinPermisos = $this->usuariosSinPermisosCount();
    }

    public function mostrarUsuariosRegistrados()
    {
        $this->tablaActual = 'usuarios-empresas';
    }

    private function usuariosRegistradosCount()
    {
        return UsuariosEmpresasModel::where('cuit', IdHelper::idEmpresa())
            ->where('cargo', '=', 'Empleado')
            ->where('estado', '=', 'Aceptado')
            ->count();
    }

    public function mostrarUsuariosActivos()
    {
        $this->tablaActual = 'usuarios-activos';
    }

    private function usuariosActivosCount()
    {
        return UsuariosEmpresasModel::where('cuit', IdHelper::idEmpresa())
            ->where('cargo', 'Empleado')
            ->where('estado', 'Aceptado')
            ->whereHas('usuarios', function ($query) {
                $query->whereNotNull('email_verified_at')
                    ->where('estado', 1);
            })
            ->count();
    }

    public function mostrarUsuariosSinPermisos()
    {
        $this->tablaActual = 'usuarios-sin-permisos';
    }

    private function usuariosSinPermisosCount()
    {
        return UsuariosEmpresasModel::where('cuit', IdHelper::idEmpresa())
            ->where('cargo', 'Empleado')
            ->where('estado', 'Aceptado')
            ->whereDoesntHave('permisos')
            ->count();
    }

    public function render()
    {
        return view('livewire.empresas.empresas-usuarios.usuarios');
    }
}
