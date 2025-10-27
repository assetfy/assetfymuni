<?php

namespace App\Livewire\Actividad;

use Illuminate\Support\Facades\Session;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Auth;
use App\Models\NotificacionesModel;
use App\Models\EmpresasModel;
use Livewire\Component;
use App\Models\User;

class ActividadAutorizacion extends Component
{
    public $empresa,$cuit,$estado,$selectedEstado,$descripcionRechazo,$apoderado;
    public $open = false;

    public function mount(EmpresasModel $value){
        $this->empresa = EmpresasModel::where('cuit', $value->cuit)->first();  
        // Inicializa $selectedEstado con el estado actual de la empresa
        $this->selectedEstado =  $this->empresa->estado;   
    }
    
    public function setSelectedEstado($estado)
    {
        $this->selectedEstado = $estado;
    }

    public function updateEstado()
    {
        $cuit =   $this->empresa->cuit;
        $estado = $this->selectedEstado;
        if($estado == 2){
            $this->cargaApoderado($cuit);
            $this->panel($cuit);
        }
       
        // Actualizar el estado en la base de datos
        $this->updateEstadoEmpresa($cuit, $estado);
        // Cerrar el modal después de actualizar el estado
        $this->dispatch('dataUpdated');
        $this->dispatch('refreshLivewireTable');
        $this->open = false;
    }
     //Actualiza el panel del user en caso de ser Rechazado
     private function panel($cuit){
            $usuarios = UsuariosEmpresasModel::where('cuit',$cuit)->pluck('id_usuario');
            $users = User::whereIn('id',$usuarios)->get();
            foreach ($users as $user) {
                $user->panel_actual = 'Usuario';
                $user->save();
            }
    }  
    //Busca el Apoderado de la empresa
    private function cargaApoderado($cuit){
        $apoderado = UsuariosEmpresasModel::where('cuit',$cuit)
                                                ->where('cargo','Apoderado')->value('id_usuario');
        $this->notificacion($cuit,$apoderado);
    }
    //Crea una notificacion en caso de que el estado sea rechazado
    private function notificacion($cuit,$apoderado){
        $empresa = Session::get('cuitEmpresaSeleccionado') ?? (Auth::check() ? Auth::user()->entidad : null);
        NotificacionesModel::create([
            'emisora' =>  $empresa,
            'cuit_empresa' =>  $cuit,
            'id_usuario' => $apoderado,
            'descripcion' => $this->descripcionRechazo,
            'cargo' => 'Apoderado',
        ]);
    }

    private function updateEstadoEmpresa($cuit, $estado)
    {
        $user = Auth::user();
        // Encuentra la empresa por su CUIT
        $empresa = EmpresasModel::where('cuit', $cuit)->first();
        // Verifica si se encontró la empresa
        if($user->panel_actual == 'Estado'){
            $this->AutorizaEstado($empresa,$estado,$cuit);
        }else{
            $this->AutorizacionEmpresa($empresa,$estado,$cuit);
        }
    }

    private function AutorizaEstado($empresa,$estado,$cuit){
        if ($empresa) {
            // Actualiza el estado de la empresa
            $empresa->autorizacion_estado = $estado;
            $empresa->save();
        }
        if($estado == '1'){
            $this->actualizarEstadoUsuarioEmpresa($estado, $cuit);
        }
    }

    private function AutorizacionEmpresa($empresa,$estado,$cuit){
        if ($empresa) {
            // Actualiza el estado de la empresa
            $empresa->autorizacion_empresa_reg = $estado;
            $empresa->save();
        }
        if($estado == '1'){
            $this->actualizarEstadoUsuarioEmpresa($estado, $cuit);
        }
    }

    private function actualizarEstadoUsuarioEmpresa($nuevoEstado, $cuit)
    {
        $usuarioEmpresa = UsuariosEmpresasModel::where('cuit', $cuit)
                                                ->where('cargo', 'Apoderado')
                                                ->first();
        if ($usuarioEmpresa) {
            $usuarioEmpresa->estado = 'Aceptado';
            $usuarioEmpresa->save();
        }
    }

    public function render()
    {
        return view('livewire.actividad.actividad-autorizacion');
    }
}
