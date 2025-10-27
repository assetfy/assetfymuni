<?php

namespace App\Livewire\Ubicaciones;

use App\Models\AuditoriaUbicacionActivoModel;
use App\Services\MiddlewareInvoker;
use App\Models\UbicacionesModel;
use App\Models\ActivosModel;
use App\Helpers\IdHelper;
use Livewire\Component;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Exception;

class CambiarUbicacion extends Component
{
    public $open, $id_ubicacion, $activo, $ubicacionActual, $ubicacionesDisponibles, $userId;
    protected $listeners = ['openModalCambiarUbicacion'];

    public function mount()
    {
        // Inicializar las propiedades
        $this->open = false;
        $this->activo = null;
        $this->ubicacionActual = null;
        $this->ubicacionesDisponibles = collect();
    }

    public function openModalCambiarUbicacion($data)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        // Obtener el ID directamente
        $activoId = $data['activo'] ?? null;

        if ($activoId) {
            $this->openModal($activoId);
        }
    }

    public function openModal($activoId)
    {

        // Buscar el modelo ActivosModel con el ID proporcionado
        $this->activo = ActivosModel::find($activoId);
        if ($this->activo) {
            $this->userId = IdHelper::identificador();
            if ($this->activo->id_ubicacion) {
                // Asignar la ubicación actual del activo
                $this->ubicacionActual = UbicacionesModel::find($this->activo->id_ubicacion);
                // Obtener las ubicaciones disponibles
                $this->ubicacionesDisponibles = $this->obtenerUbicacionesDisponibles($this->ubicacionActual,  $this->userId);
            } else {
                // Si el activo no tiene ubicación asignada
                $this->ubicacionActual = null; // Indicar que no tiene ubicación actual
                $this->ubicacionesDisponibles = $this->obtenerTodasUbicaciones($this->userId);
            }
        }
        $this->open = true;
    }

    private function obtenerTodasUbicaciones($userId)
    {
        return UbicacionesModel::where(function ($query) use ($userId) {
            $query->where('cuit', $userId)
                ->orWhere('cuil', $userId);
        })->get();
    }

    private function obtenerUbicacionesDisponibles($ubicacionActual, $userId)
    {
        return UbicacionesModel::where(function ($query) use ($userId) {
            $query->where('cuit', $userId)
                ->orWhere('cuil', $userId);
        })
            ->when($ubicacionActual, function ($query) use ($ubicacionActual) {
                $query->where('id_ubicacion', '!=', $ubicacionActual->id_ubicacion);
            })
            ->get();
    }

    public function render()
    {
        return view('livewire.ubicaciones.cambiar-ubicacion');
    }

    public function actualizar()
    {
        DB::beginTransaction();
        try {
            if ($this->activo) {
                // Actualizar la ubicación del activo
                $this->activo->id_ubicacion = $this->id_ubicacion;
                $this->activo->save();
                $this->auditoria();
            }
            DB::commit();

            // Reiniciar propiedades y emitir eventos
            $this->reset(['id_ubicacion', 'activo', 'ubicacionActual']);
            $this->dispatch('render');
            $this->dispatch('lucky');
            $this->open = false;

            // Verificar si se debe abrir el modal de servicios
            $cacheData = Cache::pull('abrirModalServicio'); // Obtener y eliminar el valor de la cache
            if (!empty($cacheData) && $cacheData['estado'] === true) {
                $this->dispatch('openModal', ['activoId' => $cacheData['activoCompleto']])
                    ->to('servicios.activos.crear-solicitud-servicio');
            }
        } catch (Exception $e) {
            DB::rollBack();
            $this->reset(['id_ubicacion', 'activo', 'ubicacionActual']);
            $this->dispatch('warning', ['message' => 'Ocurrió un error al actualizar los datos del activo.']);
            $this->open = false;
        }
    }



    private function auditoria()
    {
        AuditoriaUbicacionActivoModel::create([
            'ubicacion_actual' => $this->activo->id_ubicacion,
            'id_activo' => $this->activo->id_activo,
            'trasladado' => $this->id_ubicacion,
            'fecha' => today(),
            'id_usuario' => $this->userId
        ]);
    }

    public function crearubicacion()
    {
        $this->open = false;
        $this->dispatch('crearUbicacion')->to('ubicaciones.crear-ubicaciones');
    }
}
