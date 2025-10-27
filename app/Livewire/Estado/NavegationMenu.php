<?php

namespace App\Livewire\Estado;

use Livewire\Component;
use App\Models\EmpresasModel;
use App\Models\UbicacionesModel;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Session;
use App\Models\EmpresasActividadesModel;

class NavegationMenu extends Component
{
    public $panelActual,$estadoActual,$empresas,$empresa,
    $lista,$estado,$ubicaciones,$solicitudes,$actividad;
    protected $cuit,$cuitSeleccionado; 

    public function mount()
    {
        $this->loadData();
    }

    public function render()
    {
        return view('livewire.estado.navegation-menu');
    }

    private function loadData()
    {
        $user = $this->getCurrentUser(); //Obtiene el usuario actualmente autenticado.
        if (!$user) {
            return;
        }
        $this->cuit = $this->getCUIT($user); //Obtiene el CUIT asociado al usuario.
        $this->GuardarCuitSession($this->cuit); // Almacena el CUIT en la sesión.
        $this->loadUbicaciones();   //Carga las ubicaciones asociadas al CUIT.
        $this->loadEmpresa();   //Carga el tipo de empresa asociado al CUIT.
        $this->loadSolicitudes(); //Carga las solicitudes si el tipo de empresa es '4'.
        $this->loadSolicitudesActividad(); //Carga las actividades si el tipo de empresa es '4'.
    }

    private function getCurrentUser()
    {
        return auth()->user();
    }

    private function getCUIT($user)
    {
        $cuit = UsuariosEmpresasModel::where('id_usuario', $user->id)->value('cuit');
        // Verifica si $cuit es nulo
        $cuit = $cuit ?? auth()->user()->entidad;
        return $cuit;
    }

    private function GuardarCuitSession($cuit)
    {
        Session::put('cuitEmpresaSeleccionado', $cuit);
    }

    private function loadUbicaciones()
    {
        $this->ubicaciones = UbicacionesModel::where('cuit', $this->cuit)->get();
    }

    private function loadEmpresa()
    {
        $this->empresa = EmpresasModel::where('cuit', $this->cuit)->value('tipo');
    }

    private function loadSolicitudes()
    {
        if ($this->empresa == '4') {
            $this->solicitudes = EmpresasModel::where('estado_autorizante', $this->cuit)
                ->where(function ($query) {
                    $query->where('autoriza', 'estado')
                        ->orWhere('autoriza', 'entidad_y_estado');
                })
                ->whereNotIn('autorizacion_estado', [2, 1]) // Excluir los valores 2 y 1
                ->get();
        }          
    }

    private function loadSolicitudesActividad()
    {
        $query = EmpresasActividadesModel::query()
            ->where('estado', '!=', 'Aceptado')
            ->where('estado', '!=', 'Rechazado');
        if ($this->empresa == '4') {
            $query->where('estado_autorizante', $this->cuit);
        } elseif ($this->empresa == '3') {
            $query->where('empresa_reguladora_autorizante', $this->cuit);
        }
        $this->actividad = $query->get();
    }

}
