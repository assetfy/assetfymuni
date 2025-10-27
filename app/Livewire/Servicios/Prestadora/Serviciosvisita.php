<?php

namespace App\Livewire\Servicios\Prestadora;

use App\Models\ActivosAtributosModel;
use App\Models\ActivosModel;
use App\Models\ServiciosActivosModel;
use App\Models\SolicitudesServiciosModel;
use Livewire\Component;

class Serviciosvisita extends Component
{
    protected $listeners = ['vistarServicios'];

    public $prestadora, $servicios, $activos, $activeTab, $atributos, $serviciosActivos;
    public $open = false;

    public function vistarServicios($data)
    {
        $this->servicios = SolicitudesServiciosModel::where('id_solicitud', $data)->get();
        $this->activos = ActivosModel::where('id_activo', $this->servicios->pluck('id_activo'))->first();
        $this->atributos = ActivosAtributosModel::where('id_activo', $this->activos->id_activo)->get();
        $this->serviciosActivos =  ServiciosActivosModel::where('id_activo', $this->activos->id_activo)->get();
        $this->open = true;
    }

    public function render()
    {
        return view('livewire.servicios.prestadora.serviciosvisita');
    }
}
