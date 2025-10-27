<?php

namespace App\Livewire\Usuarios;

use App\Models\UsuariosEmpresasModel;
use App\Models\AuditoriasModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\EmpresasModel;
use Livewire\Component;

class Notificaciones extends Component
{
    public $user, $notificaciones, $auditorias, $empresas, $solicitudes, $prestadoras, $selectedSection;

    public function mount()
    {
        $this->user = auth()->user();
        $this->notificaciones = $this->notificaciones();
        if ($this->notificaciones->count() > 0) {
            $this->empresas = EmpresasModel::whereIn('cuit', $this->notificaciones->pluck('cuit'))->get();
            $this->notifyUser("Tienes nuevas notificaciones de empresas.");
        }
        $this->auditorias = $this->notificacionesAuditoria();
        $this->solicitudes = $this->notificacionessolicitudServicios();
        if ($this->solicitudes->count() > 0) {
            $this->prestadoras = EmpresasModel::whereIn('cuit', $this->solicitudes->pluck('empresa_prestadora'))->get();
            $this->notifyUser("Tienes nuevas solicitudes de servicios.");
        }
        $this->selectedSection = 'todos'; // Sección seleccionada por defecto
    }

    public function render()
    {
        return view('livewire.usuarios.notificaciones', [
            'notificaciones' => $this->notificaciones,
            'auditorias' => $this->auditorias->unique('id'),
            'solicitudes' => $this->solicitudes,
            'empresas' => $this->empresas,
            'prestadoras' => $this->prestadoras,
        ]);
    }

    private function notificacionesAuditoria()
    {
        return AuditoriasModel::where('id_usuario', $this->user->id)->get();
    }

    private function notificaciones()
    {
        return UsuariosEmpresasModel::where('id_usuario', $this->user->id)
            ->where('cargo', 'Empleado')
            ->where('estado', 'En Revision')
            ->get();
    }

    private function notificacionessolicitudServicios()
    {
        return SolicitudesServiciosModel::where('id_solicitante', $this->user->id)
                                        ->where('estado_presupuesto','Esperando confirmacion del Cliente')->get();
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
    }

    public function actualizarEstado($nuevoEstado, $cuit)
    {
        $this->actualizarEstadoUsuarioEmpresa($nuevoEstado, $cuit);
        $this->notificaciones = $this->notificaciones();
    }

    public function cambiarSeccion($seccion)
    {
        $this->selectedSection = $seccion;
    }

    private function notifyUser($message)
    {
        echo "<script>notifyUser('$message');</script>";
    }
}
