<?php

namespace App\Livewire\Empresas;

use app\Models\EmpresasModel;
use App\Traits\SortableTrait; 
use Livewire\Component;

class UpdateAutorizacion extends Component
{
    use SortableTrait; 
    public $cuit,$updateAutorizacion,$empresa,$razon_social,$documento,$autorizacion_empresa_reg,$tipo;
    public $open = false;

    protected $rules = [
        'updateAutorizacion' => 'required',
    ];

    public function mount(EmpresasModel $value){
       $this->empresa = $value;
       $this->razon_social = $value->razon_social;
       $this->cuit = $value->cuit;
       $this->documento = $value->constancia_afip;
       $cuit = session()->get('cuitEmpresaSeleccionado');
       $this->tipo = EmpresasModel::where('cuit', $cuit)->value('tipo');
    }

    public function actualizarAutorizacion()
    {
        $this->validate();
        if($this->tipo == '3'){
            $this->empresa->autorizacion_empresa_reg = $this->updateAutorizacion;
        }else{
            $this->empresa->autorizacion_estado = $this->updateAutorizacion;
        }
        $this->empresa->save();
        // Cerrar el modal después de la actualización
        $this->dispatch('dataUpdated');
        $this->dispatch('refreshLivewireTable');
        $this->eventos();
    }

    public function render()
    {
        return view('livewire.empresas.update-autorizacion');
    }
}
