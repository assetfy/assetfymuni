<?php

namespace App\Livewire\Servicios\Solicitudes;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Filters\DateFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use App\Models\SolicitudesServiciosModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB; // Asegúrate de importar DB
use App\Models\ActivosModel;
use App\Helpers\IdHelper;
use App\Models\ServiciosActivosModel;
use App\Models\CalificacionesModel;
use Illuminate\Database\Eloquent\Model;

class SolicitudesCerradas extends LivewireTable
{
    protected string $model = SolicitudesServiciosModel::class;
    public $title = 'Historial de Servicios'; // Nombre del encabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    public int $value;
    protected bool $useSelection = false;
    public $panel_usuario, $isDisabled;

    protected function getValue()
    {
        //Obtener el valor del ID
        $this->value = IdHelper::identificador();
    }

    protected function query(): Builder
    {
        $activosIds = $this->getActivos();
        $this->actualizarGarantias();
        $query = $this->model::whereIn('act.solicitudes_servicios.id_activo', $activosIds);

        return $this->filtro($query);
    }

    protected function getActivos()
    {
        // Obtener panel_usuario desde el usuario autenticado
        $this->panel_usuario = auth()->user()->panel_actual;

        $this->getValue();
        $activosQuery = ActivosModel::query();
        // Buscar por 'usuario_titular'
        $userId = $activosQuery->where('usuario_titular', '=', $this->value)->get();
        // Si no se encuentra ningún resultado usando cuit, búsqueda por cuil
        if ($userId->isEmpty()) {
            // Reinicia la consulta
            $activosQuery = ActivosModel::query();
            // Buscar por 'empresa_titular'
            $userId = $activosQuery->where('empresa_titular', '=', $this->value)->get();
        }
        return $userId->pluck('id_activo');
    }

    protected function columns(): array
    {
        $columns = [
            Column::make(__('Reseña'), function (Model $model): string {
                $prueba = SolicitudesServiciosModel::where('id_solicitud', $model->getKey())->first()->estado_presupuesto;

                $reseñaButton = '';
                $visualizarButton = '';

                if ($prueba == 'Servicio Realizado, Solicitud Cerrada') {
                    $servRealizado = ServiciosActivosModel::where('solicitud', $model->getKey())->first()->id_serviciosActivos;
                    $isDisabled = CalificacionesModel::where('id_serviciosActivos', $servRealizado)->first();

                    if (!$isDisabled) {
                        $reseñaButton = '<button wire:click="$dispatch(\'openDetalleCalificacion\', { data: ' . $model->getKey() . ' })" 
                class="bg-gray-400 hover:bg-gray-800 text-black font-bold py-1 px-4 rounded border border-black h-12" 
                title="Escribir Reseña">
                <i class="fas fa-pencil text-xl inline-block text-black"></i>                                        
            </button>';
                    } else {
                        $visualizarButton = '<button wire:click="$dispatch(\'openEditarCalificacion\', { data: ' . $model->getKey() . ' })" 
                class="bg-gray-400 hover:bg-gray-800 text-black font-bold py-1 px-4 rounded border border-black h-12" 
                title="Visualizar Reseña">
                <i class="fas fa-eye text-xl inline-block text-black"></i>                                        
            </button>';
                    }
                }

                return $reseñaButton . $visualizarButton;
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Acciones'), function (Model $model): string {
                return sprintf(
                    '<button 
                        wire:click="$dispatch(\'openDetalleServicio\', { data: %d })" 
                        style="background-color: #BFDBFE;"
                        class="text-blue-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                        title="Ver Detalle">
                        <i class="fa-solid fa-circle-info text-base"></i>
                    </button>',
                    $model->getKey()
                );
            })
                ->clickable(false)
                ->asHtml(),

            DateColumn::make(__('Fecha'), 'fechaHora')
                ->format('Y-m-d'),
            Column::make(__('Proveedor'), 'empresasPrestadora.razon_social'),
            Column::make(__('Nombre del Bien'), 'activos.nombre')
                ->searchable(),
            Column::make(__('Servicio'), 'servicios.nombre')
                ->searchable(),
            // Se omite "Empresa Solicitante" inicialmente
            Column::make(__('Estado'), 'estado'),
        ];
        // Agregar la columna "Garantía" utilizando el accesor
        array_splice($columns, 5, 0, [
            Column::make(__('Garantía'), 'garantia_display')
        ]);

        // Agregar la columna "Empresa Solicitante" solo si panel_usuario NO es 'Usuario'
        if ($this->panel_usuario !== 'Usuario') {
            // Insertar la columna en la posición deseada, por ejemplo después de "Empresa Prestadora"
            array_splice($columns, 5, 0, [
                Column::make(__('Empresa Solicitante'), 'empresasSolicitantes.razon_social')
            ]);
        }

        return $columns;
    }

    protected function filtro(Builder $query): Builder
    {
        // Obtener el filtro seleccionado desde los filtros aplicados
        $filtroSeleccionado = $this->filters['estado_presupuesto'] ?? null;

        if ($filtroSeleccionado) {
            // Si el usuario selecciona un filtro, aplicar esa condición
            $query->whereIn('estado_presupuesto', (array) $filtroSeleccionado);
        } else {
            // Si no hay un filtro seleccionado, mostrar solo los estados en curso
            $query->whereIn('estado_presupuesto', [
                'Servicio Realizado, Solicitud Cerrada',
            ]);
        }

        return $query;
    }

    // Funcion creada unicamente para utilizacion de filtros
    public function filters(): array
    {
        $activosIds = $this->getActivos();

        $estadosEnCurso = SolicitudesServiciosModel::whereIn('id_activo', $activosIds)
            ->whereIn('estado_presupuesto', [
                'confirmado por Cliente y esperando visita',
                'Esperando confirmación de prestadora',
                'Esperando confirmacion del Cliente'
            ])
            ->distinct()
            ->pluck('estado_presupuesto', 'estado_presupuesto')
            ->toArray();

        $estadosDeCierre = SolicitudesServiciosModel::whereIn('id_activo', $activosIds)
            ->whereIn('estado_presupuesto', [
                'Visita expirada',
                'Rechazado',
                'Cotizacion expirada',
                'Servicio Realizado, Solicitud Cerrada',
            ])
            ->distinct()
            ->pluck('estado_presupuesto', 'estado_presupuesto')
            ->toArray();

        // Renombrar las opciones de estados en curso
        $estadosEnCursoRenombrados = [
            'confirmado por Cliente y esperando visita' => 'Esperando visita',
            'Esperando confirmación de prestadora' => 'Esperando por prestadora',
            'Esperando confirmacion del Cliente' => 'Esperando por cliente'
        ];

        // Renombrar las opciones de estados de cierre
        $estadosDeCierreRenombrados = [
            'Visita expirada' => 'Visita expirada',
            'Rechazado' => 'Rechazado',
            'Cotizacion expirada' => 'Expirado',
            'Servicio Realizado, Solicitud Cerrada' => 'Cerrado'
        ];

        $estadosDeCierre = array_map(function ($estado) use ($estadosDeCierreRenombrados) {
            return $estadosDeCierreRenombrados[$estado] ?? $estado;
        }, $estadosDeCierre);

        $estadosEnCurso = array_map(function ($estado) use ($estadosEnCursoRenombrados) {
            return $estadosEnCursoRenombrados[$estado] ?? $estado;
        }, $estadosEnCurso);

        return [
            SelectFilter::make(__('Estados en Curso'), 'estado_presupuesto')
                ->multiple()
                ->options($estadosEnCurso),
            SelectFilter::make(__('Estados de cierre'), 'estado_presupuesto')
                ->multiple()
                ->options($estadosDeCierre),
            SelectFilter::make(__('Servicios'), 'id_servicio')
                ->options($this->getServicios()),
            DateFilter::make(__('Fecha'), 'fechaHora'),
        ];
    }

    protected function getServicios()
    {
        // Se obtenien nuevamente los Id de los activos
        $activosIds = $this->getActivos();

        // Para a partir de los activos, obtener los nombre de las solicitudes de servicios
        $solicitudes = SolicitudesServiciosModel::whereIn('id_activo', $activosIds)
            ->whereHas('servicios')
            ->with('servicios')
            ->get();

        // Crear un array asociativo: [id_servicio => nombre]
        $options = $solicitudes->mapWithKeys(function ($solicitud) {
            return [$solicitud->id_servicio => $solicitud->servicios->nombre ?? null];
        })->filter()->toArray();

        return $options;
    }

    public function actualizarGarantias()
    {
        $solicitudes = new SolicitudesServiciosModel();
        $solicitudes->actualizarGarantias();
    }
}
