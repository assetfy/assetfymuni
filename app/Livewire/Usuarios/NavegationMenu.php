<?php

namespace App\Livewire\Usuarios;

use App\Models\SolicitudesServiciosModel;
use Illuminate\Support\Facades\Session;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Auth;
use App\Models\EmpresasModel;
use App\Models\TiposEmpresaModel;
use Livewire\Component;
use App\Models\User;
use App\Models\ActivosModel;

class NavegationMenu extends Component
{
    public $title = 'Bienes'; // Nombre del encabezado
    public $createForm = ' crearActivos'; // Nombre de la función que llama al evento

    public $empresa, $auditorias, $notificaciones, $lista, $cuitSeleccionado, $tipoMenu = 'usuarios',
        $estadoActual, $panelActual, $empresas, $estado, $ubicaciones, $solicitudes, $hasEmpresas, $notificacionesCount, $user,
        $calificacionesFaltantes, $calificacionesRealizadas;

    public function mount()
    {
        $this->user = auth()->user();
        $user = auth()->user();
        $this->empresas = EmpresasModel::all();
        $this->panelActual =  auth()->user()->panel_actual;
        $this->loadEstado($user);
        $this->loadNotificaciones($user);
        $this->loadSolicitudes($user);
        $id = $this->getUserId();
        $this->calificacionesFaltantes = ActivosModel::getCalificacionesFaltante($id);
        $this->calificacionesRealizadas = ActivosModel::getCalificaciones($id);
    }

    public function render()
    {
        $view = 'livewire.' . $this->tipoMenu . '.navegation-menu';
        return view($view, [
            'estadoActual' => $this->panelActual,
            'estados' => $this->estado,
            'auditorias' => $this->auditorias,
            'ubicaciones' => $this->ubicaciones,
            'notificacionesCount' => $this->notificacionesCount,
            'calificaciones' => $this->calificacionesFaltantes,
            'calificacionesRealizadas' => $this->calificacionesRealizadas,
        ]);
    }

    public function refreshData()
    {
        // Refresca los datos necesarios
        $user = auth()->user();
        $this->loadEstado($user);
        $this->loadNotificaciones($user);
        $this->loadSolicitudes($user);
    }

    public function updateTipo($cuitSeleccionado)
    {
        $user = Auth::user();
        $panel_actual = $this->getPanelActual($cuitSeleccionado);
        $verificacion = User::find($cuitSeleccionado);
        $this->ActualizarUserYentidad($user, $panel_actual, $cuitSeleccionado);
        $this->redirigirUser($cuitSeleccionado, $user, $verificacion);
    }

    private function ActualizarUserYentidad($user, $panel_actual, $cuitSeleccionado)
    {
        if ($user) {
            $user->panel_actual = $panel_actual;
            $user->entidad = $cuitSeleccionado;
            $user->save();
        }
    }

    public function actualizarEstado($nuevoEstado, $cuit)
    {
        $this->actualizarEstadoUsuarioEmpresa($nuevoEstado, $cuit);
        $this->reloadNotificaciones();
    }

    private function loadEstado($user)
    {
        $this->estadoActual = $user->estado;
    }

    private function loadSolicitudes($user)
    {
        $this->solicitudes = SolicitudesServiciosModel::where('id_solicitante', $user->id)
            ->whereNotNull('precio')
            ->whereNull('motivo_cancelacion')
            ->where('estado_presupuesto', 'Esperando confirmacion del Cliente')
            ->get();
    }

    private function loadNotificaciones($user)
    {
        $this->notificaciones = UsuariosEmpresasModel::where('id_usuario', $user->id)
            ->where('cargo', '!=', 'Apoderado')
            ->where('estado', '!=', 'Aceptado')
            ->get();

        $this->notificacionesCount = $this->notificaciones->count(); // Contador de notificaciones
    }

    private function getPanelActual($cuitSeleccionado)
    {
        $verificacion = User::find($cuitSeleccionado);

        switch (true) {
            case $verificacion == null:
                $empresa = EmpresasModel::where('cuit', $cuitSeleccionado)->first();

                if ($empresa) {
                    $tipoEmpresa = TiposEmpresaModel::where('id_tipo_empresa', $empresa->tipo)->value('id_tipo_empresa');
                    switch ($tipoEmpresa) {
                        case '1':
                            return 'Empresa';
                            break;
                        case '2':
                            return 'Prestadora';
                            break;
                        case '3':
                            return 'Controladora';
                            break;
                        case '4':
                            return 'Estado';
                            break;
                        default:
                            return 'Usuario';
                            break;
                    }
                }
        }
    }

    private function reloadNotificaciones()
    {
        $this->loadNotificaciones(auth()->user());
    }

    private function getUserId()
    {
        if (auth()->user()->panel_actul == 'Empresa') {
            $id = auth()->user()->entidad;
        } else {
            $id = auth()->user()->cuil;
        }
        return $id;
    }

    // Menu constante creado
    // Crear bienes
    public function crearActivos()
    {
        $this->dispatch('createActivos')->to('activos.create-activos');
    }
}
