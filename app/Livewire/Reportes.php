<?php

namespace App\Livewire;

use Livewire\Component;
use App\Helpers\IdHelper;
use App\Models\ActivosAsignacionModel;
use App\Models\ActivosModel;
use Illuminate\Support\Collection;

class Reportes extends Component
{
    public $userId;
    public Collection $lista_activos, $lista_ubicaciones, $lista_activos_normal, $lista_activos_baja, $cotizaciones, $servicios;
    public  $lista_activos_revision, $lista_activos_alquilado, $lista_activos_prestamo, $asignacionesReportes;

    public function mount()
    {
        $this->asignar();
    }

    public function asignar()
    {
        $this->userId = IdHelper::idEmpresa();
        // dd($this->userId);
        $this->cargarDatos();
        // $this->cargarActivos();
        $this->graficosReportes();
    }


    public function cargarDatos()
    {
        // Permite realizar calculos estadisticos respecto de los activos
        $this->lista_activos_normal  =  ActivosModel::getListaActivosNormal($this->userId);
        $this->lista_activos_baja = ActivosModel::getListaActivosBaja($this->userId);
        $this->lista_activos = ActivosModel::getListaActivos($this->userId);
        // Permite realizar calculos estadisticos respecto de las cotizaciones
        $this->cotizaciones = ActivosModel::getCotizaciones($this->userId);
        // Permite realizar calculos estadisticos respecto de los servicios efectuados
        $this->servicios =  ActivosModel::getServicios($this->userId);
        // Permite realizar calculos estadisticos respecto de las ubicaciones
        $this->lista_ubicaciones =  ActivosModel::getListaUbicaciones($this->userId);
        $this->lista_activos_revision = ActivosModel::where('Empresa_titular', $this->userId)
            ->where('id_estado_sit_general', '3')->count();
        $idsActivos = ActivosModel::where('Empresa_titular', $this->userId)
            ->pluck('id_activo');

        $this->asignacionesReportes = ActivosAsignacionModel::with(['gestor:id,name', 'responsable:id,name'])
            ->whereIn('id_activo', $idsActivos)
            ->get();
    }

    private function graficosReportes()
    {
        $total      = ActivosModel::getListaActivos($this->userId)->count();
        $normal     = $this->lista_activos_normal->count();
        $baja       = $this->lista_activos_baja->count();
        $reparacion = $this->lista_activos_revision;
        $servicios  = $this->servicios->count();

        $datosTipos = $this->lista_activos
            ->groupBy('tipo')
            ->map(fn($grupo, $tipo) => [
                'tipo'     => $tipo,
                'cantidad' => $grupo->count(),
            ])
            ->values()   // reindexa los elementos
            ->toArray();
        $labels = array_column($datosTipos, 'tipo');
        $series = array_column($datosTipos, 'cantidad');

        // 3) Disparas evento con los datos listos para graficar
        $this->dispatch('chart-types-data', [
            'labels' => $labels,
            'series' => $series,
        ]);
        // 1) Estados



        $this->dispatch('chart-states-data', [
            'series' => [$normal, $baja, $reparacion],
            'labels' => ['Normal', 'Baja', 'Reparacion'],
        ]);


        // 2) Ubicaciones
        // si ya tienes $this->lista_activos como Collection:
        $datos = $this->lista_activos
            ->groupBy('ubicacion')
            ->map(function ($grupo, $ubicacion) {
                return [
                    'ubicacion' => $ubicacion,
                    'activos'   => $grupo->count(),
                ];
            })
            ->values()    // reindexa de 0…n
            ->toArray();

        // Livewire 3: dispatch envía un CustomEvent con tu array
        $this->dispatch('locations-chart-data', $datos);

        $gestorCounts =  $this->asignacionesReportes
            ->groupBy(fn($a) => $a->gestor?->name ?: 'Sin gestor')
            ->map(fn($grupo, $name) => [
                'label' => $name,
                'count' => $grupo->count(),
            ])
            ->values()
            ->toArray();
        // 3) Prepara labels y series
        $labelsGestor = array_column($gestorCounts, 'label');
        $seriesGestor = array_column($gestorCounts, 'count');


        $this->dispatch('chart-gestores-data', [
            'labels' => $labelsGestor,
            'series' => $seriesGestor,
        ]);
    }

    public function render()
    {

        return view('livewire.reportes', [
            'lista_activos' => $this->lista_activos,
            'lista_activos_normal' => $this->lista_activos_normal,
            'lista_activos_baja' => $this->lista_activos_baja,
            'lista_ubicaciones' => $this->lista_ubicaciones,
            'lista_cotizaciones_solicitadas' => $this->cotizaciones,
            'lista_servicios_efectuados' => $this->servicios,
        ]);
    }
}
