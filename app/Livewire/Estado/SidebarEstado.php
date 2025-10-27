<?php

namespace App\Livewire\Estado;

use Illuminate\Support\Facades\Session;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Auth;
use App\Models\UbicacionesModel;
use App\Helpers\IdHelper;
use Livewire\Component;

class SidebarEstado extends Component
{
    public $ubicaciones,$tipo;

    public function mount()
    {
        $this->datosActualizado(); //datos del cuit 
        $this->cargarUbicaciones();
        $this->tipoUserEmpresa();
    }

    private function datosActualizado()
    {
        $cuitEmpresaSeleccionado = IdHelper::idEmpresa();
        Session::put('cuitEmpresaSeleccionado', $cuitEmpresaSeleccionado);
    }

    private function cargarUbicaciones()
    {
        $cuit = session()->get('cuitEmpresaSeleccionado');
        $this->ubicaciones = UbicacionesModel::where('cuit', $cuit)->get();
    }

     // Carga los tipos de usuarios de la empresa
     private function tipoUserEmpresa()
     {
         $user = Auth::user();
         $this->tipo = UsuariosEmpresasModel::where('id_usuario', $user->id)
                                             ->where('cuit', $user->entidad)->value('tipo_user');
     }

    public function render()
    {
        return view('livewire.estado.sidebar-estado', [
            'ubicaciones' => $this->ubicaciones
        ]);
    }
}
