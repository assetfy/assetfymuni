<?php

namespace App\Livewire\Usuarios;

use App\Models\SolicitudesServiciosModel;
use Carbon\Carbon;
use Livewire\Component;

class Agenda extends Component
{
    public $activos = [];
    public $servicios = [];

    public function mount()
    {
        // Obtener solicitudes filtradas por estado y agregar relaciones necesarias
        $this->servicios = SolicitudesServiciosModel::where('id_solicitante', auth()->id())
            ->where('estado_presupuesto', 'Confirmado por Cliente y esperando visita')
            ->with('activos', 'servicios')
            ->get();
        // Preparar la lista de eventos para el calendario
        $events = [];
        foreach ($this->servicios as $servicio) {
            // Asegurarse de que las relaciones existan antes de acceder a sus propiedades
            $activoNombre = $servicio->activos->nombre ?? 'Activo desconocido';
            $servicioNombre = $servicio->servicios->nombre ?? 'Servicio desconocido';

            // Convertir la fecha al formato ISO 8601 usando Carbon
            $fechaInicioISO = Carbon::parse($servicio->fechaHora)->toIso8601String();

            $events[] = [
                'title' => "{$activoNombre} - {$servicioNombre}",
                'start' => $fechaInicioISO,
                // Si tienes una fecha de fin, conviértela también:
                // 'end' => Carbon::parse($servicio->fechaHora_fin)->toIso8601String(),
            ];
        }

        $this->activos = $events;
    }

    public function render()
    {
        return view('livewire.usuarios.agenda');
    }
}
