<?php

namespace App\Livewire\Empresas;

use App\Http\Controllers\afipController;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EmpresaLoadActividad extends Component
{
    public $codActividad, $Afip, $mensaje, $razon_social, $Actividadprovincia, $cuitEmpresaSeleccionado, $empresa;

    public function render()
    {
        return view('livewire.empresas.empresa-load-actividad');
    }

    public function mount()
    {
        $this->verificarCuitAfip();
    }

    public function verificarCuitAfip()
    {
        $this->codActividad = [];

        $this->asignar();

        if (empty($this->cuitEmpresaSeleccionado)) {
            $this->resetearVariables();
            return;
        }

        $afipController = new afipController();
        $this->Afip = $afipController->verificarAfip($this->cuitEmpresaSeleccionado);

        if ($this->esErrorAfip()) {
            $this->procesarErrorAfip();
        } else {
            $this->procesarDatosAfip();
        }
    }

    private function resetearVariables()
    {
        $this->mensaje = null;
        $this->razon_social = null;
        $this->codActividad = [];
    }

    private function esErrorAfip()
    {
        return empty($this->Afip);
    }

    private function procesarErrorAfip()
    {
        $this->mensaje = $this->Afip;
        $this->razon_social = null;
    }

    private function procesarDatosAfip()
    {
        if (!empty($this->Afip->datosGenerales)) {
            $this->mensaje = $this->Afip->datosGenerales->estadoClave;
            $this->razon_social = $this->Afip->datosGenerales->razonSocial;
            $this->Actividadprovincia = $this->Afip->datosGenerales->domicilioFiscal->descripcionProvincia;

            if ($this->deberiaProcesarActividad()) {
                $this->procesarActividad();
            }
        }
    }

    public function asignar()
    {
        $this->cuitEmpresaSeleccionado = session()->get('cuitEmpresaSeleccionado');
        if ($this->cuitEmpresaSeleccionado == null) {
            $this->cuitEmpresaSeleccionado  = Auth::user()->entidad;
        }
    }
}
