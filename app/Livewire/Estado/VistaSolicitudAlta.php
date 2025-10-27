<?php

namespace App\Livewire\Estado;

use App\Models\SolicitudesServiciosModel;
use Illuminate\Support\Facades\Session;
use App\Models\ServiciosActivosModel;
use Illuminate\Support\Facades\Auth;
use App\Models\EmpresasModel;
use Livewire\Component;
use Carbon\Carbon;

class VistaSolicitudAlta extends Component
{
    public $empresa;
    public $lat, $long;
    public $monthlyData;
    public $serviciosRealizados;
    public $serviciosPendienteVisita;
    public $serviciosPendientesCotizacion;
    public $serviciosCotizadosyEsperando;

    protected $listeners = ['autorizar','noAutorizar'];

    public function mount($solicitud)
    {
        Session::put('idPrestadora', $solicitud);
        $this->empresa = EmpresasModel::where('cuit', $solicitud)->first();
        $this->serviciosRealizados = 0;
        $this->serviciosPendienteVisita = 0;
        $this->serviciosPendientesCotizacion = 0;
        $this->serviciosCotizadosyEsperando = 0;

        if ($this->empresa && $this->empresa->estado == 'Aceptado') {
            $this->calcularServicios($solicitud);
        }

        $this->ubicacionDatos($this->empresa);

        // Preparar datos para los gráficos
        $this->monthlyData = [
            'serviciosRealizados' => $this->serviciosRealizados,
            'serviciosPendienteVisita' => $this->serviciosPendienteVisita,
            'serviciosPendientesCotizacion' => $this->serviciosPendientesCotizacion,
            'serviciosCotizadosyEsperando' => $this->serviciosCotizadosyEsperando,
            'currentMonth' => Carbon::now()->subMonth()->format('F')
        ];
    }

    private function calcularServicios($solicitud)
    {
        $this->serviciosRealizados = ServiciosActivosModel::where('proveedor', $solicitud)
            ->whereYear('fecha', Carbon::now()->subMonth()->year)
            ->whereMonth('fecha',  Carbon::now()->month)
            ->count();
        $this->serviciosPendienteVisita = SolicitudesServiciosModel::where('empresa_prestadora', $solicitud)
            ->where('estado_presupuesto', 'Confirmado por Cliente, esperando visita')
            ->whereYear('fechaHora', Carbon::now()->subMonth()->year)
            ->whereMonth('fechaHora',  Carbon::now()->month)
            ->count();

        $this->serviciosPendientesCotizacion = SolicitudesServiciosModel::where('empresa_prestadora', $solicitud)
            ->where('estado_presupuesto', 'Esperando Aprobacion de presupuesto del servicio')
            ->whereYear('fechaHora', Carbon::now()->subMonth()->year)
            ->whereMonth('fechaHora',  Carbon::now()->month)
            ->count();

        $this->serviciosCotizadosyEsperando = SolicitudesServiciosModel::where('empresa_prestadora', $solicitud)
            ->where('estado_presupuesto', 'Esperando confirmacion de prestadora')
            ->whereYear('fechaHora', Carbon::now()->subMonth()->year)
            ->whereMonth('fechaHora', Carbon::now()->month)
            ->count();

    }

    private function ubicacionDatos($empresa)
    {
        if ($empresa) {
            $this->lat = $empresa->lat;
            $this->long = $empresa->long;
        }
    }

    public function aceptado(){
        $this->dispatch('aprobar');
    }

    public function rechazado(){
        $this->dispatch('rechazar');
    }
    
    public function autorizar(){
        $panel = Auth::user()->panel_actual;
        if($panel == 'Estado'){
            $this->empresa->autorizacion_estado = '1';
        }else{
            $this->empresa->autorizacion_empresa_reg = '1';
        }
        $this->empresa->save();   
        $this->dispatch('confirmada');
    }

    public function noAutorizar(){
        $panel = Auth::user()->panel_actual;
        if($panel == 'Estado'){
            $this->empresa->autorizacion_estado = '2';
        }else{
            $this->empresa->autorizacion_empresa_reg = '2';
        }
        $this->empresa->save();   
        $this->dispatch('denegado');
    }
    public function render()
    {
        // Datos para el gráfico Donut
        $monthlyData = [
            'serviciosRealizados' => $this->serviciosRealizados,
            'serviciosPendienteVisita' => $this->serviciosPendienteVisita,
            'serviciosPendientesCotizacion' => $this->serviciosPendientesCotizacion,
            'serviciosCotizadosyEsperando' => $this->serviciosCotizadosyEsperando
        ];
        
    
        return view('livewire.estado.vista-solicitud-alta', [
            'lat' => $this->lat,
            'long' => $this->long,
            'monthlyData' => $monthlyData,
        ])->with('monthlyDataJS', json_encode($monthlyData));
    }
    
    
}
