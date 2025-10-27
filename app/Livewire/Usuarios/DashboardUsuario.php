<?php

namespace App\Livewire\Usuarios;

use Livewire\Component;
use App\Models\SolicitudesServiciosModel;
use App\Models\ServiciosActivosModel;
use App\Models\UsuariosEmpresasModel;
use App\Models\UbicacionesModel;
use App\Models\ActivosModel;
use App\Models\ActividadesEconomicasModel;
use App\Models\EmpresasModel;
use App\Helpers\IdHelper;
use App\Models\ActivosAsignacionModel;
use App\Models\NotificacionesModel;
use App\Models\User;

class DashboardUsuario extends Component
{
    public $datosDashboard, $open, $servicioId, $user, $empresa_titular, $asignacionesEmpresa, $activosEmpresa, $findAsignado, $findDelegado;

    public function mount()
    {
        $this->refreshDashboardData();
    }

    public function refreshDashboardData()
    {
        $this->user = auth()->user();
        $id = $this->getUserId(); // Obtiene el CUIL del usuario
        $activos = $this->getActivos($id); // Obtiene los activos del usuario
        $ubicaciones = $this->getUbicaciones($id); // Obtiene ubicaciones
        $servicios = $this->getServicios($id); // Obtiene servicios
        $notificaciones = $this->getNotificaciones($id);
        $activosCount = $this->countActivos($id); // Cuenta los activos del usuario
        $ubicacionesCount = $this->countUbicaciones($id); // Cuenta ubicaciones
        $serviciosCount = $this->countServicios();
        $serviciosSolicitudes = $this->activosServiciosSolicitud();
        $serviciosPendiente = $this->activosServiciosPendiente();
        $serviciosRealizar = $this->activosServiciosVisita();
        $empresa = $this->getEmpresas($servicios);
        $actividad = $this->getActividad($empresa);
        $empresasDatos = $this->empresasDatos();
        $lista_activos_normal  =  ActivosModel::getListaActivosNormal($id);
        $lista_activos_baja = ActivosModel::getListaActivosBaja($id);
        $cotizaciones = ActivosModel::getCotizaciones($id);
        $calificaciones = ActivosModel::getCalificaciones($id);
        $serviciosFinalizados = ActivosModel::getServicios($id);
        $calificacionesFaltantes = ActivosModel::getCalificacionesFaltante($id);

        // Verificar que el perfil desde el cual se ha logueado sea el de empresa
        if ($this->user->panel_actual == 'Empresa') {
            $this->datosEmpresa();
            $this->asignacionesEmpresa = $this->isEmpleado();
            $this->activosEmpresa = $this->obtenerActivosEmpresa();
            $this->findAsignado = $this->findAsignado($this->activosEmpresa);
            $this->findDelegado = $this->findGestionado($this->activosEmpresa);
            // dd($this->asignacionesEmpresa, $this->activosEmpresa, $this->findAsignado);
        }

        // Preparar ubicaciones como array asociativo
        $ubicacionesArray = $ubicaciones->map(function ($ubicacion) {
            return [
                'id_ubicacion' => $ubicacion->id_ubicacion,
                'tipo' => $ubicacion->tipo,
                'tipo_nombre' => $ubicacion->nombre,
                'direccion' => $ubicacion->calle . ' ' . $ubicacion->altura . ', ' . $ubicacion->ciudad . ', ' . $ubicacion->provincia,
                'tipo_propiedad' => $ubicacion->propiedad,
                'piso' => $ubicacion->piso,
                'departamento' => $ubicacion->depto,
                'lat' => $ubicacion->lat,
                'lng' => $ubicacion->long,
            ];
        });

        $this->datosDashboard = [
            'activosCount' => $activosCount,
            'ubicacionesCount' => $ubicacionesCount,
            'serviciosCount' => $serviciosCount,
            'activos' => $activos,
            'lista_activos_normal' =>   $lista_activos_normal,
            'lista_activos_baja' =>  $lista_activos_baja,
            'ubicaciones' => $ubicaciones,
            'servicios' => $servicios,
            'ubicacionesArray' => $ubicacionesArray, // Pasamos el array asociativo a la vista
            'notificaciones' => $notificaciones,
            'solicitudes' =>  $serviciosSolicitudes,
            'solicitudesPendientes' => $serviciosPendiente,
            'ServiciosVisita' =>   $serviciosRealizar,
            'empresa' => $empresa,
            'actividad' => $actividad,
            'empresasDatos' => $empresasDatos,
            'lista_cotizaciones_solicitadas' => $cotizaciones,
            'servicios_finalizados' => $serviciosFinalizados,
            'calificaciones' => $calificaciones,
            'calificaciones_pendientes' => $calificacionesFaltantes,
            'asignacionesEmpresa' => $this->asignacionesEmpresa,
            'isAsignado' => $this->findAsignado,
            'activosEmpresa' => $this->activosEmpresa,
            'isGestionado' => $this->findDelegado,
        ];
    }

    public function render()
    {
        return view('livewire.usuarios.dashboard-usuario', $this->datosDashboard);
    }

    private function countActivos($id)
    {
        return ActivosModel::where('usuario_titular', (int)$id)
            ->orWhere('empresa_titular', (int)$id)
            ->count();
    }

    private function countUbicaciones($id)
    {
        return UbicacionesModel::where('cuil', (int)$id)
            ->orWhere('cuit', (int)$id)
            ->count();
    }

    private function countServicios()
    {
        return SolicitudesServiciosModel::where('id_solicitante',   $this->user->id)
            ->where('estado_presupuesto', 'Esperando confirmacion del Cliente')->count();
    }

    private function getActivos($id)
    {
        return  ActivosModel::getListaActivos($id);
    }

    private function getUbicaciones($id)
    {
        return UbicacionesModel::where('cuil', (int)$id)
            ->orWhere('cuit', (int)$id)->get();
    }

    private function getNotificaciones($id)
    {
        return UsuariosEmpresasModel::where('id_usuario', auth()->user()->id)
            ->where('cargo', '!=', 'Apoderado')
            ->where('estado', '!=', 'Aceptado')
            ->where('estado', '!=', 'Baja')
            ->get();
    }

    private function getServicios($id)
    {
        return SolicitudesServiciosModel::where('id_solicitante', auth()->user()->id)
            ->where('estado_presupuesto', 'Esperando confirmacion del Cliente')
            ->get();
    }

    private function getUserId()
    {
        if (auth()->user()->panel_actual == 'Empresa') {
            $id = auth()->user()->entidad;
        } else {
            $id = auth()->user()->cuil;
        }
        return $id;
    }


    private function getEmpresas($datos)
    {
        if ($datos->isNotEmpty() && $datos->first()->empresa_prestadora) {
            $empresa = $datos->first()->empresa_prestadora;
            return EmpresasModel::where('cuit', $empresa)->get();
        } else {
            return collect(); // Retorna una colección vacía en lugar de null
        }
    }

    private function getActividad($value)
    {
        if ($value->isNotEmpty() && $value->first()->COD_ACTIVIDAD) {
            $value = $value->first()->COD_ACTIVIDAD;
            return ActividadesEconomicasModel::where('COD_ACTIVIDAD', $value)->get();
        } else {
            return collect(); // Retorna una colección vacía en lugar de null
        }
    }

    private function activosServiciosSolicitud()
    {
        $empresa = IdHelper::idEmpresa();
        return SolicitudesServiciosModel::with('activos.ubicacion')->where('empresa_prestadora', $empresa)
            ->where('estado_presupuesto', 'Esperando confirmacion de prestadora')->get();
    }

    private function activosServiciosPendiente()
    {
        $empresa = IdHelper::idEmpresa();
        return SolicitudesServiciosModel::with('activos.ubicacion')->where('empresa_prestadora', $empresa)
            ->where('estado_presupuesto', 'Esperando confirmacion de prestadora')->get();
    }

    private function activosServiciosVisita()
    {
        $empresa = IdHelper::idEmpresa();
        return SolicitudesServiciosModel::with('activos.ubicacion')->where('empresa_prestadora', $empresa)
            ->where('estado_presupuesto', 'Confirmado por Cliente y esperando visita')->get();
        $this->dispatch('lucky');
    }

    private function actualizarEstadoUsuarioEmpresa($nuevoEstado, $cuit)
    {
        $user = auth()->user();
        $usuarioEmpresa = UsuariosEmpresasModel::where('id_usuario', $user->id)
            ->where('cuit', $cuit)
            ->first();

        if ($usuarioEmpresa) {
            $usuarioEmpresa->estado = $nuevoEstado;
            $usuarioEmpresa->save();
        }
        $this->dispatch('lucky');
    }

    public function actualizarEstado($nuevoEstado, $cuit)
    {
        $this->actualizarEstadoUsuarioEmpresa($nuevoEstado, $cuit);
    }

    private function empresasDatos()
    {
        return EmpresasModel::all();
    }

    public function update($servicio)
    {
        $this->open = true;
        $this->dispatch('openModal', ['servicioId' => $servicio])->to('usuarios.usuarios-servicios-autorizacion');
    }

    // Inicializa los datos de la empresa
    public function datosEmpresa()
    {
        $id = IdHelper::identificadorParcial();
        $this->empresa_titular = $id['cuit'];
    }

    // Verifica si el usuario es empleado de la empresa de la cual se ha logueado
    private function isEmpleado()
    {
        return UsuariosEmpresasModel::where('id_usuario', $this->user->id)
                                    ->where('cuit', $this->empresa_titular)
                                    ->where('cargo', '=', 'Empleado')
                                    ->get();
    }

    // Obtiene los activos de la empresa de la cual se ha logueado
    private function obtenerActivosEmpresa()
    {
        return ActivosModel::where('empresa_titular', $this->empresa_titular)->get();
    }

    // Verifica si el usuario tiene activos asignados o gestionados
    private function findActivos($activos, $tipo = 'asignado_a')
    {
        // Obtener los IDs de los activos
        $ids = collect($activos)->pluck('id_activo')->toArray();

        // Obtener los activos según el tipo de asignación (asignado o gestionado)
        $asignados = ActivosAsignacionModel::whereIn('id_activo', $ids)
            ->where($tipo, $this->user->id)
            ->pluck('id_activo')
            ->unique()
            ->toArray();

        // Verificar si los activos están en la tabla de notificaciones
        return NotificacionesModel::whereIn('descripcion', $asignados)
            ->where([
                ['id_usuario', $this->user->id],
                ['cuit_empresa', $this->empresa_titular]
            ])
            ->pluck('descripcion') // La columna donde se almacena el id del activo
            ->unique()
            ->toArray();
    }

    // Envia por parametro a la funcion el valor de asignado_a
    public function findAsignado($activos)
    {
        return $this->findActivos($activos, 'asignado_a');
    }

    // Envia por parametro a la funcion el valor de gestionado_por
    public function findGestionado($activos)
    {
        return $this->findActivos($activos, 'gestionado_por');
    }

    public function visto($id)
    {
        // Eliminar de NotificacionesModel
        NotificacionesModel::where('descripcion', $id)
                            ->where('cuit_empresa', $this->empresa_titular)
                            ->where('id_usuario', $this->user->id)
                            ->delete();

        $this->dispatch('lucky');
    }
}
