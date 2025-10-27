<?php

namespace App\Livewire\Actividad;

use App\Models\EmpresasActividadesModel;
use Livewire\Component;

class ActividadExtraAutorizacion extends Component
{
    public $empresa,$cuit,$estado,$selectedEstado,$cod,$value;
    public $open = false;

    protected $listeners = ['openModalActividadExtra'];


    public function openModalActividadExtra($data)
    {
        $this->value = EmpresasActividadesModel::find($data);
        if ( $this->value ) {
            $this->mount($this->value);
            $this->open = true;
        }
    }

    public function render()
    {
        return view('livewire.actividad.actividad-extra-autorizacion');
    }

    public function mount(EmpresasActividadesModel $value){
        $this->empresa = EmpresasActividadesModel::where('cod_actividad', $value->cod_actividad)->first();    
    }
    
    public function setSelectedEstado($estado)
    {
        $this->selectedEstado = $estado;
    }

    public function updateEstado()
    {
        $cod =   $this->empresa->cod_actividad;
        $estado = $this->selectedEstado;
        // Actualizar el estado en la base de datos
        $this->updateEstadoEmpresa($cod, $estado);
        // Cerrar el modal después de actualizar el estado
        $this->dispatch('dataUpdated');
        $this->dispatch('refreshLivewireTable');
        $this->open = false;
    }

    private function updateEstadoEmpresa($cod, $estado)
    {
        // Encuentra la empresa por su CUIT
        $empresa = EmpresasActividadesModel::where('cod_actividad', $cod)->first();
        // Verifica si se encontró la empresa
        if ($empresa) {
            // Actualiza el estado de la empresa
            $empresa->estado = $estado;
            $empresa->save();
        }
    }
}
