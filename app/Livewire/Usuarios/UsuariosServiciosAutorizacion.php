<?php

namespace App\Livewire\Usuarios;

use App\Models\ActivosModel;
use App\Models\EmpresasModel;
use App\Models\ServiciosModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\UbicacionesModel;
use App\Services\MiddlewareInvoker;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class UsuariosServiciosAutorizacion extends Component
{
    public $open = false;
    public $servicios;
    public $presupuesto, $precio, $activo, $ubicacion, $servicio, $aceptacion, $rutaDocumento, $fechaHora, $empresa, $descripcion, $mensaje, $diasDuracion, $estadoSolicitud, $editar;
    public $fechaModificada = false;

    protected $listeners = ['openModaltabla', 'update', 'openModalServiciosSolicitados'];

    const MENSAJE_FECHA_MODIFICADA = 'La fecha fue modificada';
    const mensajeAceptacion = 'Confirmado por Cliente y esperando visita';
    const mensajeRechazo = 'Rechazado por Cliente';

    public function openModal($data)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }

        $servicioId =  $data['servicioId']['id_solicitud'];
        $this->servicios = SolicitudesServiciosModel::find($servicioId);
        if ($this->servicios) {
            $this->fechaModificacion();
            $this->cargarDatos();
            $this->estadoSolicitud =  $this->servicios->estado_presupuesto;
            $this->open = true;
        }
    }

    public function openModalServiciosSolicitados($data)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->openModaltabla($data);
        }
    }

    public function openModaltabla($value)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }

        $this->servicios = SolicitudesServiciosModel::where('id_solicitud', $value)->first();
        if ($this->servicios) {
            $this->fechaModificacion();
            $this->cargarDatos();
            $this->MensajeEdicion();
            $this->open = true;
        }
    }

    public function update($servicioId)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }

        $this->servicios = SolicitudesServiciosModel::find($servicioId);
        if ($this->servicios) {
            $this->fechaModificacion();
            $this->cargarDatos();
            $this->MensajeEdicion();
            $this->open = true;
        }
    }

    private function MensajeEdicion()
    {
        if ($this->servicio) {
            $this->estadoSolicitud = $this->servicios->estado_presupuesto;
            switch ($this->estadoSolicitud) {
                case 'Esperando confirmación de prestadora':
                    $this->estadoSolicitud = 'Esperando Confirmacion';
                    $this->editar = false;
                    break;
                case 'Esperando confirmacion del Cliente':
                    $this->estadoSolicitud = 'Esperando Confirmacion';
                    $this->editar = true;
                    break; // <-- Agrega el break aquí
                case 'Rechazado por Prestadora':
                    $this->estadoSolicitud = 'Rechazado';
                    $this->editar = false;
                    break;
                case 'Rechazado por Cliente':
                    $this->estadoSolicitud = 'Rechazaste esta cotizacion';
                    $this->editar = false;
                    break;
                default:
                    // Opcional: asignar valores por defecto si se requiere
            }
        }
    }


    private function fechaModificacion()
    {
        $this->fechaHora = $this->servicios->fecha_modificada !== null ? $this->servicios->fecha_modificada : $this->servicios->fechaHora;
        $this->mensaje = $this->servicios->fecha_modificada !== null ? self::MENSAJE_FECHA_MODIFICADA : $this->mensaje;
    }

    private function cargarDatos()
    {
        $this->activo = ActivosModel::find($this->servicios->id_activo);
        $this->ubicacion = UbicacionesModel::find($this->activo->id_ubicacion);
        $this->servicio = ServiciosModel::find($this->servicios->id_servicio);
        $this->empresa = EmpresasModel::where('cuit', $this->servicios->empresa_prestadora)->first();
        // Calcular duración del servicio
        $this->calcularDuracionServicio();
    }

    public function actualizar()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }

        $this->guardarDatos();
        $this->dispatch('lucky');
        $this->close();
    }

    private function guardarDatos()
    {
        if ($this->aceptacion === 'Si') {
            $this->servicios->fechaHora = $this->formatFechaHora($this->fechaHora);
            if ($this->fechaModificada == true) {
                $this->servicios->estado_presupuesto = 'Esperando confirmación de prestadora';
                $this->servicios->fecha_finalizacion  = null;
            } else {
                $this->servicios->estado_presupuesto = 'Aceptado';
            }
        } else {
            $this->servicios->motivo_cancelacion = $this->descripcion;
            $this->servicios->estado_presupuesto = self::mensajeRechazo;
            $this->actualizarEstadoActivo();
        }
        $this->servicios->estado = $this->aceptacion;
        $this->servicios->save();
    }


    private function actualizarEstadoActivo()
    {
        $this->activo->id_estado_sit_general = '1';
        $this->activo->save();
    }

    private function formatFechaHora($fechaHora)
    {
        return date('Y-m-d H:i:s', strtotime($fechaHora));
    }

    public function close()
    {
        $this->reset(['presupuesto', 'precio', 'aceptacion']);
        $this->dispatch('refreshLivewireTable');
        $this->open = false;
    }

    public function render()
    {
        $servicios = Auth::user();
        $solicitudesServicios = SolicitudesServiciosModel::where('id_solicitante', $servicios->id)
            ->whereNotNull('precio')
            ->where('estado_presupuesto', 'Esperando confirmacion del Cliente');

        $isDashboard = request()->routeIs('usuarios.dashboard-usuario');

        return view('livewire.usuarios.usuarios-servicios-autorizacion', [
            'solicitudesServicios' => $solicitudesServicios,
            'isDashboard' => $isDashboard,
        ]);
    }

    private function calcularDuracionServicio()
    {
        if ($this->servicios->fecha_finalizacion) {
            // Calcular diferencia de días entre la fecha de inicio y la fecha de finalización
            $inicio = new \Carbon\Carbon($this->fechaHora);
            $finalizacion = new \Carbon\Carbon($this->servicios->fecha_finalizacion);

            $this->diasDuracion = $inicio->diffInDays($finalizacion);
        } else {
            // Si no hay fecha de finalización, el servicio es de un solo día
            $this->diasDuracion = 0;
        }
    }

    // Método para detectar cambios en la fechaHora
    public function updatedFechaHora($value)
    {
        if ($this->servicios->fechaHora !== $value) {
            $this->fechaModificada = true;
        }
    }
}
