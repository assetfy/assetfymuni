<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\SolicitudesServiciosModel;
use Carbon\Carbon;

class Calendar extends Component
{
    public $events = [];

    public function mount()
    {
        $this->fetchEvents();
    }

    public function fetchEvents()
    {
        $userId = auth()->id();
        $servicios = SolicitudesServiciosModel::where('id_solicitante', $userId)
            ->orWhere('empresa_solicitante', $userId)
            ->where('estado_presupuesto', 'Confirmado por Cliente, esperando visita')
            ->get();

        foreach ($servicios as $servicio) {
            $date = $servicio->fecha_modificada ? Carbon::parse($servicio->fecha_modificada) : Carbon::parse($servicio->fechaHora);
            $this->events[] = [
                'title' => $servicio->descripcion,
                'start' => $date->format('Y-m-d H:i:s'),
            ];
        }
    }

    public function render()
    {
        return view('livewire.calendar', ['events' => $this->events]);
    }
}
