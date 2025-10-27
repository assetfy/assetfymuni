<?php

namespace App\Livewire\Empresas;

use App\Models\AuditoriasModel;
use App\Models\EmpresasModel;
use App\Models\NotificacionesModel;
use Livewire\Component;
use App\Traits\SortableTrait;

class DetalleEstado extends Component
{
    use SortableTrait;
    public $cuit,$registro,$empresas,$descripcion,$emisora;
    public $open = 'false';

    public function render()
    {
        return view('livewire.empresas.detalle-estado');
    }

    public function mount(AuditoriasModel $value){
        $this->registro = NotificacionesModel::where('cuit_empresa', $value->cuit)->first();
        if(  $this->registro){
            $this->cuit = $this->registro->cuit_empresa;
            $this->emisora = $this->registro->emisora;
            $this->descripcion = $this->registro->descripcion;
        }
        $this->empresas = EmpresasModel::all();
    }

    public function visto()
    {
        // Eliminar de AuditoriasModel
        AuditoriasModel::where('cuit', $this->cuit)->delete();
        // Eliminar de NotificacionesModel
        NotificacionesModel::where('cuit_empresa', $this->cuit)->delete();
        // Redireccionar o hacer cualquier otra acción necesaria
        $this->eventos();
    }
    
}
