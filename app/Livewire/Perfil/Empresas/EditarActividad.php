<?php

namespace App\Livewire\Perfil\Empresas;

use App\Helpers\IdHelper;
use App\Livewire\Servicios\ActividadesEconomicas\ServiciosActividadesEconomicas;
use App\Models\ActividadesEconomicasModel;
use App\Models\EmpresasModel;
use App\Models\MisProveedoresModel;
use App\Models\ServiciosActividadesEconomicasModel;
use Livewire\Component;

class EditarActividad extends Component
{
    public $open = false;
    public $codActividad;
    public $empresa;
    public $actividades;

    protected $listeners = ['editarActividad'];

    protected $rules = [
        'codActividad' => 'required',
    ];

    public function editarActividad()
    {
        $this->loadDatos();
        $this->open = true;
    }

    private function loadDatos()
    {
        $cuit = IdHelper::idEmpresa();
        $this->empresa = EmpresasModel::findOrFail($cuit);

        // 1) Sacamos sólo los códigos de actividad que existen en el pivot
        $cods = ServiciosActividadesEconomicasModel::distinct()
            ->pluck('cod_actividad');

        // 2) Traemos únicamente esas actividades
        $this->actividades = ActividadesEconomicasModel::whereIn('COD_ACTIVIDAD', $cods) // opcional, filtrar por activas
            ->get();

        // Si no hay código, queda en null y el select muestra el placeholder
        $this->codActividad = $this->empresa->COD_ACTIVIDAD;
    }

    public function save()
    {
        $this->validate();

        // Actualizamos sólo el campo COD_ACTIVIDAD
        $this->empresa->update([
            'COD_ACTIVIDAD' => $this->codActividad,
            'estado' => 'Aceptado',
        ]);

        MisProveedoresModel::where('cuit', $this->empresa->cuit)
            ->update([
                'existe_en_la_plataforma' => 'Si',
            ]);

        $this->dispatch('lucky');

        $this->open = false;

        return redirect()->route('dashboard-empresa');
    }

    public function render()
    {
        return view('livewire.perfil.empresas.editar-actividad');
    }
}
