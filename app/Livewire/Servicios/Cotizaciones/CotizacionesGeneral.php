<?php

namespace App\Livewire\Servicios\Cotizaciones;

use Livewire\Component;
use App\Helpers\IdHelper;
use App\Models\ActivosModel;
use App\Models\CalificacionesModel;
use App\Models\EmpresasModel;
use App\Models\SolicitudesServiciosModel;
use Illuminate\Support\Facades\Auth;

class CotizacionesGeneral extends Component
{
    public $datos, $manager, $serviciosGestora, $serviciosRealizados, $gestora, $realizadosGestora, $tipoEmpresa, $serviciosSinAsignar, $serviciosPrestadora,
        $realizadosPrestadora, $prestadora, $tecnicos;
    public $tablaActual = 'usuarios-servicios'; // Valor predeterminado

    public function mount()
    {
        $this->cargarDatos();
        if ($this->tipoEmpresa == 1) {
            $this->tablaActual = 'usuarios-servicios';
        } else {
            $this->tablaActual = 'dashboard-prestadora';
        }
    }

    private function cargarDatos()
    {
        $id = $this->getUserId(); // Obtiene el ID del usuario
        $calificaciones = ActivosModel::getCalificaciones($id);
        $serviciosFinalizados = ActivosModel::getServicios($id);
        $serviciosIds = $serviciosFinalizados->pluck('id_serviciosActivos')->toArray();
        $calificacionesFaltantes = ActivosModel::getCalificacionesFaltante($id);
        $this->tipoEmpresa = $this->tipoEmpresa();
        $this->manager = $this->rolGestor();
        $this->prestadora = $this->rolManager();
        $this->tecnicos = $this->rolTecnico();

        // Obtener los servicios con reseña
        $serviciosConResenia = CalificacionesModel::whereIn('id_serviciosActivos', $serviciosIds)->get();

        // Obtener los servicios sin reseña
        $serviciosSinResenia = $serviciosFinalizados->whereNotIn('id_serviciosActivos', $serviciosConResenia->pluck('id_serviciosActivos')->toArray());

        // Contador Gestora
        $this->serviciosGestora = $this->ServiciosPendientesGestora()->count();
        $this->serviciosRealizados = $this->ServiciosRealizadosGestora()->count();
        // Gestora Gestor
        $this->gestora = $this->ServiciosGestora()->count();
        $this->realizadosGestora = $this->RealizadosGestora()->count();

        // Prestadora 
        $this->serviciosSinAsignar = $this->SinAsignar()->count();
        $this->serviciosPrestadora = $this->ServiciosPrestadora()->count();
        $this->realizadosPrestadora = $this->ServiciosPrestadoraRealizados()->count();

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

    public function mostrarCotizaciones()
    {
        if ($this->tipoEmpresa == 1) {
            $this->tablaActual = 'usuarios-servicios';
        } else {
            $this->tablaActual = 'dashboard-prestadora';
        }
    }

    public function mostrarAdjudicaciones()
    {
        if ($this->tipoEmpresa == 1) {
            $this->tablaActual = 'cotizaciones-adjudicadas';
        } else {
            $this->tablaActual = 'cotizaciones-servicios';
        }
    }

    public function mostrarAsignaciones()
    {
        $this->tablaActual = 'cotizaciones-sin-asignar';
    }

    private function rolGestor()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Admin Empresa Prestadora')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();
    }

    private function rolManager()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Prestadora - Manager')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();
    }

    private function rolTecnico()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Usuario Tecnico Empresa Prestadora')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::whereIn('id_rol', $idsRol)
            ->where('usuario_empresa', Auth::user()->id)
            ->where('cuit', IdHelper::idEmpresa())
            ->get();
    }

    private function ServiciosPendientesGestora()
    {
        return SolicitudesServiciosModel::where('empresa_solicitante', (int) auth()->user()->entidad)
            ->orWhere('empresa_prestadora', (int) auth()->user()->entidad)
            ->whereNotIn('estado_presupuesto', ['Servicio Realizado, Solicitud Cerrada', 'Aceptado'])->get();
    }

    private function ServiciosPrestadora()
    {
        return SolicitudesServiciosModel::where('empresa_prestadora', (int) auth()->user()->entidad)
            ->whereIn('estado_presupuesto', [
                'Esperando confirmación de prestadora',
                'Confirmado por Cliente y esperando visita',
                'Aceptado',
                'Rechazado por Cliente',
            ]);
    }

    private function ServiciosPrestadoraRealizados()
    {
        return SolicitudesServiciosModel::where('empresa_prestadora', (int) auth()->user()->entidad)
            ->where(
                'estado_presupuesto',
                'Servicio Realizado, Solicitud Cerrada'
            );
    }

    private function ServiciosRealizadosGestora()
    {
        return SolicitudesServiciosModel::where('empresa_solicitante', (int) auth()->user()->entidad)
            ->orWhere('empresa_prestadora', (int) auth()->user()->entidad)
            ->where('estado_presupuesto', 'Aceptado')->get();
    }

    private function ServiciosGestora()
    {
        return SolicitudesServiciosModel::where('empresa_solicitante', (int) auth()->user()->entidad)
            ->where('id_solicitante', Auth::user()->id)
            ->whereNot('estado_presupuesto', 'Servicio Realizado, Solicitud Cerrada')->get();
    }

    private function RealizadosGestora()
    {
        return SolicitudesServiciosModel::where('empresa_solicitante', (int) auth()->user()->entidad)
            ->where('id_solicitante', Auth::user()->id)
            ->where('estado_presupuesto', 'Aceptado')->get();
    }

    private function tipoEmpresa()
    {
        return EmpresasModel::where('cuit', (int) auth()->user()->entidad)
            ->pluck('tipo')
            ->first();
    }

    private function SinAsignar()
    {
        return SolicitudesServiciosModel::where('empresa_prestadora', (int) auth()->user()->entidad)
            ->where('estado_presupuesto', 'Aceptado')
            ->whereNull('tecnico_id')->get();
    }

    public function render()
    {
        return view('livewire.servicios.cotizaciones.cotizaciones-general');
    }
}
