<?php

namespace App\Livewire\Estado;

use App\Models\UsuariosEmpresasModel;
use App\Models\ServiciosActivosModel;
use App\Models\EmpresasModel;
use App\Models\SolicitudesServiciosModel;
use Livewire\Component;
use App\Helpers\IdHelper;
use Carbon\Carbon;

class DashboardEstado extends Component
{
    public $solicitudesAceptadas, $solicitudPendientes, $viewType, $search, $servicios, $peticiones, $empresa, $logo;
    public $monthlyData;

    public function mount()
    {
        $this->viewType = 'all'; 
        $this->search = ''; 
        $this->cargarDatos();
        $this->logo = $this->logoEmpresa(); // Obtiene el logo de la empresa
    }

    public function render()
    {
        $this->calculateMonthlyData();

        return view('livewire.estado.dashboard-estado', [
            'monthlyData' => $this->monthlyData,
            'empresa' => $this->empresa // Pasar la variable empresa a la vista
        ]);
    }

    public function cargarDatos()
    {
        $user = auth()->user();
        $empresaUser = UsuariosEmpresasModel::where('id_usuario', $user->id)->first();

        if ($empresaUser) {
            $this->empresa = EmpresasModel::where('cuit', $empresaUser->cuit)->first();
            if ($this->empresa) {
                $this->solicitudesReguladora($this->empresa);
                $this->mostrarSolicitudes($this->empresa);
                $this->mostrarPeticiones($this->empresa);
            } else {
                $this->solicitudPendientes = collect();
                $this->solicitudesAceptadas = collect();
                $this->servicios = 0;
                $this->peticiones = 0;
            }
        } else {
            $this->solicitudPendientes = collect();
            $this->solicitudesAceptadas = collect();
            $this->servicios = 0;
            $this->peticiones = 0;
        }
    }

    public function pollingRefresh()
    {
        $user = auth()->user();
        $empresaUser = UsuariosEmpresasModel::where('id_usuario', $user->id)->first();
        // Actualiza los datos relevantes cada vez que se llama el polling
        $this-> solicitudesReguladora($empresaUser);
    }

    public function showAll()
    {
        $this->viewType = 'all';
        $this->cargarDatos();
    }

    public function showPendientes()
    {
        $this->viewType = 'pendientes';
        $this->cargarDatos();
    }

    public function showAprobados()
    {
        $this->viewType = 'aprobados';
        $this->cargarDatos();
    }

    private function solicitudesReguladora($empresa)
    {
        $panel = auth()->user()->panel_actual;      
        if($panel == 'Estado'){
            $baseQuery = EmpresasModel::where('estado_autorizante', $empresa->cuit);
            $variable = 'autorizacion_estado';
   
        }else{
            $baseQuery = EmpresasModel::where('empresa_reguladora_autorizante', $empresa->cuit);
            $variable = 'autorizacion_empresa_reg';

        }
        switch ($this->viewType) {
            case 'pendientes':
                $this->solicitudPendientes = $baseQuery->where($variable, '0')
                    ->when($this->search, function($q) {
                        $q->where('razon_social', 'like', '%' . $this->search . '%');
                    })->get();
                break;

            case 'aprobados':
                $this->solicitudesAceptadas = $baseQuery->where($variable, '1')
                    ->when($this->search, function($q) {
                        $q->where('razon_social', 'like', '%' . $this->search . '%');
                    })->get();
                break;

            default: // 'all'
                $this->solicitudPendientes = $baseQuery->clone()->where($variable, '0')
                    ->when($this->search, function($q) {
                        $q->where('razon_social', 'like', '%' . $this->search . '%');
                    })->get();

                $this->solicitudesAceptadas = $baseQuery->clone()->where($variable, '1')
                    ->when($this->search, function($q) {
                        $q->where('razon_social', 'like', '%' . $this->search . '%');
                    })->get();
                break;
        }
    }

    private function mostrarSolicitudes($empresa)
    {
        $this->servicios = ServiciosActivosModel::whereYear('fecha', Carbon::now()->year)
            ->whereMonth('fecha', Carbon::now()->month)
            ->count(); // Contar el número de servicios en el mes actual
    }

    private function mostrarPeticiones($empresa)
    {
        $this->peticiones = SolicitudesServiciosModel::whereYear('fechaHora', Carbon::now()->year)
            ->whereMonth('fechaHora', Carbon::now()->month)
            ->count(); // Contar el número de peticiones en el mes actual
    }

    private function logoEmpresa()
    {
        $cuit = IdHelper::idEmpresa();
        return EmpresasModel::where('cuit', $cuit)->first();
    }

    private function calculateMonthlyData()
    {
        $solicitudesPendientesCount = $this->solicitudPendientes->count();
        $solicitudesAprobadasCount = $this->solicitudesAceptadas->count();
        $serviciosCount = $this->servicios;
        $peticionesCount = $this->peticiones;

        $this->monthlyData = [
            'solicitudesPendientesCount' => $solicitudesPendientesCount,
            'solicitudesAprobadasCount' => $solicitudesAprobadasCount,
            'serviciosCount' => $serviciosCount,
            'peticionesCount' => $peticionesCount,
            'currentMonth' => Carbon::now()->format('F'),
        ];
    }
}
