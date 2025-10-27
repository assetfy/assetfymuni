<?php

namespace App\Livewire\Calificaciones;

use App\Helpers\IdHelper;
use App\Livewire\Servicios\SolicitudServicios;
use Livewire\Component;
use App\Models\ActivosModel;
use App\Models\CalificacionesModel;
use App\Models\SolicitudesServiciosModel;
use Illuminate\Support\Facades\Auth;

class CalificacionGeneral extends Component
{
    public $datos, $manager, $serviciosGestora;
    public $tablaActual = 'solicitud-servicios'; // Valor predeterminado

    public function mount()
    {
        $this->cargarDatos();
    }
    private function cargarDatos()
    {
        $id = $this->getUserId(); // Obtiene el ID del usuario
        $calificaciones = ActivosModel::getCalificaciones($id);
        $serviciosFinalizados = ActivosModel::getServicios($id);
        $serviciosIds = $serviciosFinalizados->pluck('id_serviciosActivos')->toArray();
        $calificacionesFaltantes = ActivosModel::getCalificacionesFaltante($id);
        $this->manager = $this->rolGestor();

        // Obtener los servicios con reseña
        $serviciosConResenia = CalificacionesModel::whereIn('id_serviciosActivos', $serviciosIds)->get();

        // Obtener los servicios sin reseña
        $serviciosSinResenia = $serviciosFinalizados->whereNotIn('id_serviciosActivos', $serviciosConResenia->pluck('id_serviciosActivos')->toArray());

        $this->serviciosGestora = $this->ServiciosPendientesGestora()->count();

        $this->datos = [
            'servicios_finalizados' => $serviciosFinalizados,
            'calificaciones' => $calificaciones,
            'calificaciones_pendientes' => $calificacionesFaltantes,
            'servicios_con_resenia' => $serviciosConResenia,
            'servicios_sin_resenia' => $serviciosSinResenia,
        ];
    }

    private function getUserId()
    {
        if (auth()->user()->panel_actual == 'Empresa' || auth()->user()->panel_actual == 'Prestadora') {
            $id = auth()->user()->entidad;
        } else {
            $id = auth()->user()->cuil;
        }
        return $id;
    }

    public function mostrarServicios()
    {
        $this->tablaActual = 'solicitud-servicios';
    }

    public function mostrarResenias()
    {
        $this->tablaActual = 'resenia-efectuadas';
    }

    public function solicitudesCerradas()
    {
        $this->tablaActual = 'solicitudes-cerradas';
    }

    public function reseniasFaltantes()
    {
        $this->tablaActual = 'resenias-faltantes';
    }

    private function rolGestor()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Gestor Empresa')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();
    }

    private function ServiciosPendientesGestora()
    {
        return SolicitudesServiciosModel::where('empresa_solicitante', (int) auth()->user()->entidad)
            ->where('id_solicitante', Auth::user()->id)
            ->where('estado_presupuesto', 'Servicio Realizado, Solicitud Cerrada')->get();
    }

    public function render()
    {
        return view('livewire.calificaciones.calificacion-general', $this->datos);
    }
}
