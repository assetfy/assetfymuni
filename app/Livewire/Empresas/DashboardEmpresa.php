<?php

namespace App\Livewire\Empresas;

use Livewire\Component;
use App\Helpers\IdHelper;
use App\Models\ActivosAsignacionModel;
use App\Models\ActivosModel;
use App\Models\OrdenesModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Auth;

class DashboardEmpresa extends Component
{
    public $empresa, $id_usuario, $ubicaciones, $bienes, $logo, $users, $tipoUser, $activos, $ordenesActivas, $activosSinUbicacion, $ordenesTrabajos, $ServiciosPendienteCotizacion,
        $esperandoVisita, $usuariosSinConfirmar, $cuit, $ordenesClientes, $tipoEmpresa, $responsable, $ordenesGestora, $ordenesGestoraCount, $gestor,
        $bienesTercerosDelegados, $userId, $notificacion, $serviciosGestora, $rol, $apoderado, $manager, $rutas, $ordenesTrabajo, $ordenesServicio, $tecnico,
        $tecnicos, $tecnicoCount, $cantTecnicos, $ordenesCerradas, $usuario, $idsRolTecnico;
    public $search = '';

    // Propiedades para los contadores
    public $bienesCount = 0;
    public $ordenesActivasCount = 0;
    public $tareasPendientes = 0;

    public function mount()
    {
        $this->dispatch('openModal', null);
        $this->empresa = IdHelper::empresaActual()->load('ubicaciones');
        $this->ubicaciones =  $this->empresa->ubicaciones;
        $this->usuario =  IdHelper::usuarioEmpresaActual();
        $this->logo =  $this->empresa->logo;
        $this->rol = $this->rolUsuario();
        $this->tipoEmpresa = $this->empresa->tipo;
        $this->apoderado = $this->esApoderado();
        $this->manager = $this->rolGestor();
        $this->tecnico = $this->rolManager();
        $this->tecnicos = $this->tecnicos();

        // Bienes Empresas
        $this->bienesCount = $this->activosRegistrado();
        // Usuarios Registrados
        $this->users = $this->getUsersEmpresas();
        // Ordenes en curso
        $this->ordenesGestora = $this->ordenesGestora();
        // Servicios en curso
        $this->serviciosGestora = $this->ServiciosPendientesGestora();
        // Empresa Prestadora
        $this->notificaciones();
        $this->tecnicoCount = $this->cantTecnicos->count();
    }

    private function notificaciones()
    {
        $this->activosSinUbicacion = ActivosModel::where('empresa_titular',   $this->empresa->cuit)
            ->whereNull('id_ubicacion')
            ->get();
        $this->ServiciosPendienteCotizacion = SolicitudesServiciosModel::where('empresa_prestadora', $this->empresa->cuit)
            ->where('estado_presupuesto', 'Esperando confirmación de prestadora')->get()->count();
        $this->ordenesTrabajo = OrdenesModel::where('proveedor',   $this->empresa->cuit)
            ->where('estado_orden', 'Realizado')
            ->whereNotNull('cuit_Cliente')->get()->count();
        $this->ordenesServicio = OrdenesModel::where('proveedor',  $this->empresa->cuit)
            ->whereNotNull("cuit_cliente")
            ->where('estado_vigencia', 'Activo')->get()->count();

        $this->idsRolTecnico = \App\Models\RolesModel::where('nombre', 'Usuario Tecnico Empresa Prestadora')
            ->pluck('id_rol');

        $this->cantTecnicos = \App\Models\AsignacionesRolesModel::whereIn('id_rol', $this->idsRolTecnico)
            ->where('usuario_empresa', Auth::user()->id)
            ->where('cuit',  $this->empresa->cuit)
            ->get();
    }

    private function esApoderado(): bool
    {
        return isset($this->usuario)
            && $this->usuario->cargo === 'Apoderado';
    }

    private function rolUsuario()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Admin Empresa')
            ->orWhere('nombre', 'Admin Empresa Prestadora')
            ->pluck('id_rol');


        return \App\Models\AsignacionesRolesModel::where('usuario_empresa',  $this->usuario->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', $this->empresa->cuit)
            ->exists();
    }

    private function tecnicos()
    {
        return \App\Models\AsignacionesRolesModel::where('usuario_empresa',  $this->usuario->id)
            ->where('id_rol', $this->idsRolTecnico)
            ->where('cuit', $this->empresa->cuit)
            ->exists();
    }

    private function rolGestor()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Gestor Empresa')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::where('usuario_empresa', $this->usuario->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', $this->empresa->cuit)
            ->exists();
    }

    private function rolManager()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Prestadora - Manager')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::where('usuario_empresa',  $this->usuario->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', $this->empresa->cuit)
            ->exists();
    }

    private function activosRegistrado()
    {
        return ActivosModel::where('empresa_titular', $this->empresa->cuit)->count();
    }

    private function getUsersEmpresas()
    {
        return UsuariosEmpresasModel::where('cuit', $this->empresa->cuit)
            ->where('estado', 'Aceptado')
            ->where('cargo', '=', 'Empleado')
            ->count();
    }

    private function ordenesGestora()
    {
        return OrdenesModel::where('cuit_Cliente',  $this->empresa->cuit)
            ->where('id_usuario', $this->usuario->id)
            ->where('estado_vigencia', 'Activo')->count();
    }

    private function ServiciosPendientesGestora()
    {
        return SolicitudesServiciosModel::where('empresa_solicitante', $this->empresa->cuit)
            ->where('estado_presupuesto', 'Aceptado')->count();
    }

    // private function ordenesClientes()
    // {
    //     return OrdenesModel::where('proveedor', $this->cuit)
    //         ->whereNotNull("cuit_cliente")
    //         ->where('estado_vigencia', 'Activo')->get();
    // }

    // private function ServiciosPendientes()
    // {
    //     return SolicitudesServiciosModel::where('empresa_prestadora', $this->cuit)
    //         ->where('estado_presupuesto', 'Aceptado')->get();
    // }

    // private function responsable()
    // {
    //     return ActivosAsignacionModel::where('empresa_empleados', $this->cuit)
    //         ->where('responsable', Auth::user()->id)
    //         ->where('estado_asignacion', 'Aceptado')
    //         ->whereNull('fecha_fin_asignacion')
    //         ->get();
    // }

    private function gestor()
    {
        return ActivosAsignacionModel::where('empresa_empleados', $this->cuit)
            ->where('gestionado_por', Auth::user()->id)
            ->where('estado_asignacion', 'Aceptado')
            ->whereNull('fecha_fin_asignacion')
            ->get();
    }

    public function abrirModalActividad()
    {
        $this->dispatch('editarActividad')->to('perfil.empresas.editar-actividad');
    }

    public function render()
    {
        // Prepara las ubicaciones como array asociativo
        $ubicacionesArray = $this->ubicaciones->map(function ($ubicacion) {
            return [
                'id_ubicacion'   => $ubicacion->id_ubicacion,
                'tipo'           => $ubicacion->tipo,
                'tipo_nombre'    => $ubicacion->nombre,
                'direccion'      => $ubicacion->calle . ' ' . $ubicacion->altura . ', ' . $ubicacion->ciudad . ', ' . $ubicacion->provincia,
                'tipo_propiedad' => $ubicacion->propiedad,
                'piso'           => $ubicacion->piso,
                'departamento'   => $ubicacion->depto,
                'lat'            => $ubicacion->lat,
                'lng'            => $ubicacion->long,
            ];
        })->toArray();

        return view('livewire.empresas.dashboard-empresa', [
            'usuarioEmpresa'   => $this->id_usuario,
            'activos'          => $this->activos,
            'users'            => $this->users,
            'ubicacionesArray' => $ubicacionesArray,
        ]);
    }
}
