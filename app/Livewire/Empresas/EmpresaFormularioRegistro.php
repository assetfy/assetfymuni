<?php

namespace App\Livewire\Empresas;

use App\Models\TiposEmpresaModel;
use App\Models\ActividadesEconomicasModel;
use App\Http\Controllers\afipController;
use Livewire\Component;
use Livewire\WithFileUploads;

class EmpresaFormularioRegistro extends Component
{
    use WithFileUploads;

    public $tiposEmpresas;
    public $selectedTipoEmpresa = null;
    public $step = 1;
    public $cuit, $razon_social, $constancia_afip, $localidad, $domicilio, $codigo_postal, $cod, $logo, $descripcion,$Afip;
    public $actividad = null, $selectedActividad = null, $mensaje = null;

    protected $rules = [
        'cuit' => 'required|numeric|digits_between:1,20',
        'razon_social' => 'required|string|max:255',
        'domicilio' => 'required|string|max:255',
        'codigo_postal' => 'required|string|max:10',
        'localidad' => 'required|string|max:100',
        'constancia_afip' => 'required|file|mimes:pdf,jpeg,png,svg',
        'selectedActividad' => 'required',
    ];

    public function mount()
    {
        $this->tiposEmpresas = TiposEmpresaModel::all();
    }

    public function selectTipoEmpresa($tipoEmpresaId)
    {
        $this->selectedTipoEmpresa = $tipoEmpresaId;
        $this->step = 2;
    }

    public function goBack()
    {
        $this->step = 1;
    }

    public function save()
    {
        $this->validate();
    }

    public function BuscarActividad()
    {
        $this->actividad = ActividadesEconomicasModel::where('nombre', 'like', '%' . $this->cod . '%')->where('estado', 1)->get();
    }

    public function verificarCuitAfip()
    {
        $afipController = new afipController();
        $this->Afip = $afipController->verificarAfip($this->cuit);

        if (is_string($this->Afip)) {
            $this->mensaje = $this->Afip;
            $this->razon_social = null;
        } else {
            $this->mensaje = $this->Afip->datosGenerales->estadoClave;
            $this->razon_social = $this->Afip->datosGenerales->razonSocial;
        }
    }

    public function render()
    {
        return view('livewire.empresas.empresa-formulario-registro');
    }
}
