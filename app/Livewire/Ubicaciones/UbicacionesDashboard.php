<?php

namespace App\Livewire\Ubicaciones;

use App\Helpers\IdHelper;
use App\Models\ActivosModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\TiposUbicacionesModel;
use App\Models\UbicacionesModel;
use Livewire\Component;
use Carbon\Carbon;
use Livewire\WithPagination;

class UbicacionesDashboard extends Component
{
    use WithPagination;

    public $id, $ubicaciones;
    public $tiposUbicacion;
    public $servicios;
    public $serviceDates = [];
    public $currentMonth;
    public $currentYear;
    public $search = ''; // Añadir la propiedad de búsqueda

    protected $listeners = ['goToPreviousMonth', 'goToNextMonth'];

    // Agregamos la propiedad para configurar el tema de la paginación
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage(); // Resetea la paginación cuando cambie la búsqueda
    }

    public function mount($id_ubicacion)
    {
        $this->id = $id_ubicacion;
        $userId = IdHelper::identificador();
        $this->tiposUbicacion = $this->fetchTiposUbicaciones();
        $this->servicios = $this->fetchServiciosUbicaciones($userId);
        $this->serviceDates = $this->fetchServiceDates();
        $this->ubicaciones = $this->fetchUbicaciones();
        // Set current month and year to the month and year of the first service date
        if (!empty($this->serviceDates)) {
            $firstServiceDate = Carbon::parse($this->serviceDates[0]);
            $this->currentMonth = $firstServiceDate->month;
            $this->currentYear = $firstServiceDate->year;
        } else {
            $this->currentMonth = now()->month;
            $this->currentYear = now()->year;
        }
    }

    public function render()
    {
        $userId = IdHelper::identificador();
        $activos = $this->fetchActivosUbicaciones($this->id, $userId);
        return view('livewire.ubicaciones.ubicaciones-dashboard', [
            'tiposUbicacion' => $this->tiposUbicacion,
            'activos' => $activos,
            'servicios' => $this->servicios,
            'serviceDates' => $this->serviceDates,
            'ubicaciones' => $this->ubicaciones,
            'currentMonth' => $this->currentMonth,
            'currentYear' => $this->currentYear,
        ]);
    }

    private function fetchTiposUbicaciones()
    {
        return TiposUbicacionesModel::all();
    }

    private function fetchUbicaciones()
    {
        return UbicacionesModel::all();
    }

    private function fetchActivosUbicaciones($id, $userId)
    {
        $query = ActivosModel::where('id_ubicacion', $id)
            ->where(function ($query) use ($userId) {
                $query->where('usuario_titular', $userId)
                    ->orWhere('empresa_titular', $userId);
            });

        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%');
        }

        return $query->paginate(6);
    }

    private function fetchServiciosUbicaciones($userId)
    {
        $servicios = SolicitudesServiciosModel::where(function ($query) use ($userId) {
            $query->where('id_solicitante', $userId)
                ->orWhere('empresa_solicitante', $userId);
        })
            ->where('estado_presupuesto', 'Confirmado por Cliente, esperando visita')
            ->get();
        if ($servicios->isEmpty()) {
            $user = auth()->user();
            $servicios = SolicitudesServiciosModel::where('id_solicitante', $user->id)
                ->where('estado_presupuesto', 'Confirmado por Cliente, esperando visita')
                ->get();
        }
        return $servicios;
    }

    private function fetchServiceDates()
    {
        return $this->servicios->map(function ($servicio) {
            $date = $servicio->fecha_modificada ? Carbon::parse($servicio->fecha_modificada) : Carbon::parse($servicio->fechaHora);
            return $date->format('Y-m-d');
        })->toArray();
    }

}

