<?php

namespace App\Livewire\Servicios;

use App\Models\SolicitudesServiciosModel;
use Illuminate\Support\Facades\Storage;
use App\Models\ServiciosModel;
use App\Models\EmpresasModel;
use App\Models\FotosServicioModel;
use App\Models\ServiciosActivosModel;
use Livewire\Component;
use App\Models\User;
use Carbon\Carbon;

class ServiciosSolicitadosDetalle extends Component
{
    public $servicio, $trabajo, $diasGarantia, $servicioSolicitado, $prestadora, $fechaSolicitada, $fotoServicio, $precio, $estado, $motivoCancelacion, $descripcion, $tieneGarantia, $solicitante, $empresaSolicitante, $fechaRealizacion, $documentoPresupuesto;
    public  $open = false;

    public $activeTab = 'Detalle';
    // Añadidas las propiedades para manejar el estado de los botones
    public $botonGarantiaDeshabilitado = true;
    public $botonPresupuestoDeshabilitado = true;
    public $fotosTrabajo = [];

    protected $listeners = ['openDetalleServicio', 'cancelarServicio'];

    public function openDetalleServicio($data)
    {
        $this->servicio = SolicitudesServiciosModel::find($data);
        if ($this->servicio) {
            $this->activeTab = 'Detalle';
            $this->servicioSolicitado = ServiciosModel::where('id_servicio', $this->servicio->id_servicio)->first();
            $this->prestadora = EmpresasModel::where('cuit', $this->servicio->empresa_prestadora)->value('razon_social');
            $this->fechaSolicitada = $this->servicio->fechaHora;
            $this->solicitante = $this->Solicitante($this->servicio);
            $this->estadoServicio($this->servicio);
            $this->cargarImagenesTrabajo();
            $this->datosDeTrabajo();
            $this->open = true;
        }
    }

    public function datosDeTrabajo()
    {
        $this->trabajo = ServiciosActivosModel::where('id_servicio', $this->servicio->id_servicio)
            ->where('id_activo', $this->servicio->id_activo)->first();
    }


    private function cargarImagenesTrabajo()
    {
        $this->fotosTrabajo = FotosServicioModel::where('id_solicitud', $this->servicio->id_solicitud)
            ->pluck('fotos')       // columna donde guardas "$path"
            ->map(function ($path) {
                return Storage::disk('s3')
                    ->temporaryUrl($path, now()->addMinutes(10));
            })
            ->toArray();
    }


    private function estadoServicio($servicios)
    {
        switch ($servicios->estado_presupuesto) {
            case 'Rechazado':
                $this->estado = 'El servicio fue rechazado.';
                $this->motivoCancelacion = $servicios->motivo_cancelacion;
                $this->botonGarantiaDeshabilitado = true;
                $this->botonPresupuestoDeshabilitado = true;
                break;
            case 'Cotizacion expirada':
                $this->estado = 'La cotización ha expirado.';
                $this->motivoCancelacion = null;
                $this->botonGarantiaDeshabilitado = true;
                $this->botonPresupuestoDeshabilitado = true;
                break;
            case 'Esperando confirmación de prestadora':
                $this->estado = 'Esperando confirmación de la prestadora.';
                $this->motivoCancelacion = null;
                $this->botonGarantiaDeshabilitado = true;
                $this->botonPresupuestoDeshabilitado = true;
                break;

            case 'Servicio Realizado, Solicitud Cerrada':
                $this->estado = 'El servicio ha sido realizado.';
                $this->garantia($servicios);
                $this->presupuesto($servicios);
                $this->botonPresupuestoDeshabilitado = false;
                $this->botonGarantiaDeshabilitado = false;
                $this->fechaRealizacion = Carbon::parse($servicios->fecha_finalizacion ?? $this->servicio->fechaHora)->format('Y-m-d H:i');
                break;

            case 'Esperando confirmacion del Cliente':
                $this->estado = 'Esperando confirmación del cliente.';
                $this->garantia($servicios);
                $this->presupuesto($servicios);
                $this->botonGarantiaDeshabilitado =  false;
                $this->botonPresupuestoDeshabilitado = false;
                $this->fechaRealizacion = Carbon::parse($servicios->fecha_finalizacion ?? $this->servicio->fechaHora)->format('Y-m-d H:i');
                break;

            case 'Rechazado por Cliente':
                $this->estado = 'El cliente ha rechazado la solicitud.';
                $this->motivoCancelacion = $servicios->motivo_cancelacion;
                $this->presupuesto($servicios);
                $this->botonGarantiaDeshabilitado = true;
                $this->botonPresupuestoDeshabilitado = false;
                $this->fechaRealizacion = Carbon::parse($servicios->fecha_finalizacion ?? $this->servicio->fechaHora)->format('Y-m-d H:i');
                break;

            case 'Confirmado por Cliente y esperando visita':
                $this->estado = 'Confirmado por el cliente. En espera de visita.';
                $this->garantia($servicios);
                $this->presupuesto($servicios);
                $this->botonGarantiaDeshabilitado = false;
                $this->botonPresupuestoDeshabilitado = false;
                $this->fechaRealizacion = Carbon::parse($servicios->fecha_finalizacion ?? $this->servicio->fechaHora)->format('Y-m-d H:i');
                break;

            default:
                $this->estado = 'Estado no definido.';
                $this->motivoCancelacion = null;
                $this->botonGarantiaDeshabilitado = true;
                $this->botonPresupuestoDeshabilitado = true;
                break;
        }
    }

    private function presupuesto($servicios)
    {
        $this->precio = $servicios->precio;
        $this->documentoPresupuesto = $servicios->presupuesto;
    }

    private function garantia($servicios)
    {
        $this->tieneGarantia = $servicios->garantia ?? 'No';

        if ($this->tieneGarantia === 'Si') {
            $this->diasGarantia = $servicios->dias_garantia ?? 0;
        }
    }

    private function Solicitante($servicios)
    {
        if (auth()->user()->panel_actual == 'Usuario') {
            $usuario = User::find($servicios->id_solicitante)->name ?? 'N/A';
        } else {
            $usuario = EmpresasModel::find($servicios->empresa_solicitante)->razon_social ?? 'N/A';
        }

        return $usuario;
    }

    public function cerrar()
    {
        $this->open = false;
    }


    public function render()
    {
        return view('livewire.servicios.servicios-solicitados-detalle');
    }

    public function update($servicio)
    {
        $this->open =  false;
        $this->dispatch('openModal', ['servicioId' => $servicio])->to('usuarios.usuarios-servicios-autorizacion');
    }

    public function cancelarEvento($servicio)
    {
        $this->open =  false;
        $this->dispatch('rechazar', ['servicioId' => $servicio])->to('servicios.servicio-motivo-rechazo');
    }
}
