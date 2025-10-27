<?php

namespace App\Livewire\Empresas;

use App\Helpers\IdHelper;
use App\Models\ActivosCompartidosModel;
use App\Models\User;
use Livewire\Component;
use App\Models\EmpresasModel;
use App\Models\UbicacionesModel;
use Illuminate\Support\Facades\Auth;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Session;
use App\Models\EmpresasActividadesModel;
use App\Models\NotificacionesModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\TiposEmpresaModel;
use Illuminate\Notifications\Events\NotificationSending;

class NavegationMenu extends Component
{
    public $panelActual, $estadoActual, $tipoMenu = 'empresas'; // Por defecto, muestra el menú de empresa
    public $empresas, $empresa, $lista, $estado, $ubicaciones, $solicitudes, $cuitSeleccionado, $actividad, $tipo, $servicios, $tipoEmpresa, $cuitEmpresaSeleccionado, $user, $logo,
        $userId, $nombreEmpresa, $notificaciones;

    public function mount()
    {
        $this->panelActual =  auth()->user()->panel_actual;
        $this->datosActualizado(); // Actualiza los datos del CUIT seleccionado

        $this->loadData(); // Carga los datos necesarios para el panel
        $this->loadSolicitudesActividad(); // Carga las actividades si el tipo de empresa es '4'
        $this->loadSolicitudesServicios(); // Carga las solicitudes de servicios
        $this->tipoUserEmpresa(); // Determina el tipo de usuario en la empresa
        $this->tipoEmpresa(); // Determina el tipo de empresa
        //$this->logo = $this->logoEmpresa(); // Obtiene el logo de la empresa
        $this->notificaciones = $this->notificaciones();
    }

    private function datosActualizado()
    {
        $cuitEmpresaSeleccionado = Session::get('cuitEmpresaSeleccionado') ?? (Auth::check() ? Auth::user()->entidad : null);
        Session::put('cuitEmpresaSeleccionado', $cuitEmpresaSeleccionado);
    }

    private function loadData()
    {
        $user = auth()->user();
        $this->panelActual = $user->panel_actual;
        $this->nombreEmpresa = EmpresasModel::where('cuit', $user->entidad)->value('razon_social');
        $this->empresas = UsuariosEmpresasModel::where('id_usuario', $user->id)->pluck('cuit');
        $this->estado = EmpresasModel::whereIn('cuit', $this->empresas)
            ->whereIn('estado', ['Aceptado', 'Rechazado'])
            ->get();
        $this->listaEmpresa();
        $this->CargarEmpresaDatos();
    }
    /**
     *  private function logoEmpresa()
     *  {
     *      $cuit = IdHelper::idEmpresa();
     *     return EmpresasModel::where('cuit', $cuit)->first();
     * }
     */

    private function listaEmpresa()
    {
        $this->lista = EmpresasModel::whereIn('cuit', $this->empresas)
            ->where('estado', 'Aceptado')
            ->get();
    }

    private function CargarEmpresaDatos()
    {
        $cuitEmpresaSeleccionado = Session::get('cuitEmpresaSeleccionado');
        $this->empresa = EmpresasModel::where('cuit', $cuitEmpresaSeleccionado)
            ->whereIn('estado', ['Aceptado'])
            ->pluck('tipo')
            ->first();
        if ($this->empresa == '3') {
            $this->CargarSolicitudes($cuitEmpresaSeleccionado);
        }
    }

    private function CargarSolicitudes($cuitEmpresaSeleccionado)
    {
        $this->solicitudes = EmpresasModel::where('empresa_reguladora_autorizante', $cuitEmpresaSeleccionado)
            ->where(function ($query) {
                $query->where('autoriza', 'admin')
                    ->orWhere('autoriza', 'entidad_y_estado');
            })->where('autorizacion_empresa_reg', '!=', 1)
            ->get();
    }

    private function loadSolicitudesActividad()
    {
        $cuit = session()->get('cuitEmpresaSeleccionado');
        $column = ($this->empresa == '4') ? 'estado_autorizante' : 'empresa_reguladora_autorizante';

        $this->actividad = EmpresasActividadesModel::where($column, $cuit)
            ->whereNotIn('estado', ['Aceptado', 'Rechazado'])
            ->get();
    }

    private function loadSolicitudesServicios()
    {
        $cuit =  session()->get('cuitEmpresaSeleccionado');
        $this->servicios = SolicitudesServiciosModel::where('empresa_prestadora', $cuit)
            ->whereNull('precio')->get();
    }

    private function tipoUserEmpresa()
    {
        $user = Auth::user();
        $this->tipo = UsuariosEmpresasModel::where('id_usuario', $user->id)
            ->where('cuit', $user->entidad)->value('tipo_user');
    }

    private function tipoEmpresa()
    {
        $this->tipoEmpresa = $this->empresa;
    }

    public function render()
    {
        $this->cargarUbicaciones();
        $view = 'livewire.' . $this->tipoMenu . '.navegation-menu';
        return view($view, ['estadoActual' => $this->panelActual, 'ubicaciones' => $this->ubicaciones]);
    }

    private function cargarUbicaciones()
    {
        $cuit = session()->get('cuitEmpresaSeleccionado');
        $this->ubicaciones = UbicacionesModel::where('cuit', $cuit)->get();
    }


    private function actualizarDatosSession($cuitSeleccionado)
    {
        Session::put('cuitEmpresaSeleccionado', $cuitSeleccionado);
    }

    private function loadDataForUsuarios($user)
    {
        $this->loadData();
        $this->tipoMenu = 'usuarios';
        $this->panelActual = $user->panel_actual;
        Session::put('cuitEmpresaSeleccionado', null);
    }

    public function notificaciones()
    {
        $this->userId = IdHelper::identificador();

        return NotificacionesModel::where('cuit_empresa', $this->userId)
            ->where('id_usuario', Auth::user()->id)
            ->orderByDesc('id_notificacion')
            ->get();
    }

    public function verNotificacion($id)
    {
        $notificacion = NotificacionesModel::find($id);

        if (!$notificacion) return;

        $descripcion = $notificacion->descripcion;
        $notificacion->delete();

        if (str_contains($descripcion, 'orden')) {
            return redirect()->route('ordenes');
        }

        if (str_contains($descripcion, 'cerrada')) {
            return redirect()->route('ordenes');
        }

        if (str_contains($descripcion, 'bien')) {
            return redirect()->route('bienes-empresa');
        }

        return redirect()->route('dashboard-empresa');
    }

    // Menu constante creado
    // Crear bienes
    public function crearActivos()
    {
        $this->dispatch('createActivos')->to('activos.create-activos');
    }
}
