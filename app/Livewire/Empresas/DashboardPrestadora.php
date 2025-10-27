<?php

namespace App\Livewire\Empresas;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Filters\DateFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Actions\Action;
use App\Exports\SolicitudesServiciosExport;
use Illuminate\Database\Eloquent\Builder;
use App\Models\SolicitudesServiciosModel;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Enumerable;
use App\Helpers\IdHelper;
use App\Models\UsuariosEmpresasModel;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardPrestadora extends LivewireTable
{
    protected string $model = SolicitudesServiciosModel::class;
    public $title = 'Cotizaciones';
    public $createForm = '';
    protected bool $useSelection = false;
    public $id, $diasDuracion, $usuario_empresa, $admin;


    protected $listeners = ['visitar', 'cancelarVisita'];

    public function asignar()
    {
        $this->id = IdHelper::idEmpresa();
        $this->usuario_empresa = UsuariosEmpresasModel::where('id_usuario', auth()->user()->id)
            ->where('cuit',  $this->id)->first();
    }

    protected function query(): Builder
    {
                $this->dispatch('openModal', ['servicios.prestadora.prestadora-servicios-solicitudes-editar-estado']);

        $this->asignar();
        $query = $this->model()->query()->where('empresa_prestadora', '=', $this->id);

        $idsRol = \App\Models\RolesModel::where('nombre', 'Admin Empresa Prestadora')
            ->orWhere('nombre', 'Prestadora - Manager')
            ->pluck('id_rol');

        $this->admin = \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();

        $filtroEstado = $this->filters['estado_presupuesto'] ?? null;
        $filtroFecha = $this->filters['fechaHora'] ?? null;

        // Obtener el usuario actual
        $usuario = optional($this->usuario_empresa);
        $userId = $usuario->id_usuario ?? null;

        // Si $filtroFecha es un array, extraer la fecha
        if (is_array($filtroFecha)) {
            $filtroFecha = $filtroFecha['date'] ?? reset($filtroFecha);
        }

        if ($filtroFecha && is_string($filtroFecha)) {
            $fechaInicio = Carbon::parse($filtroFecha)->startOfDay();
            $fechaFin = Carbon::parse($filtroFecha)->endOfDay();
            $query->whereBetween('fechaHora', [$fechaInicio, $fechaFin]);
        } else {
            // Si NO hay filtros aplicados
            if (is_null($filtroEstado) && is_null($filtroFecha)) {
                // ✅ Si el usuario es tipo 2, ve estos 3 estados
                if ($this->admin || $usuario->tipo_user == '2') {
                    $query->whereIn('estado_presupuesto', [
                        'Esperando confirmación de prestadora',
                        'Confirmado por Cliente y esperando visita',
                        'Aceptado',
                        'Rechazado por Cliente',
                    ]);
                }
                // ✅ Si es representante técnico, ve estos estados y filtra por su ID
                elseif ($usuario->es_representante_tecnico == 'Si') {
                    $query->whereIn('estado_presupuesto', [
                        'Confirmado por Cliente y esperando visita',
                        'Aceptado'
                    ])->where(function ($q) use ($userId) {
                        $q->where('tecnico_id', $userId)
                            ->orWhereNull('tecnico_id'); // ✅ Si tecnico_id es NULL, también lo mostrará
                    });
                }
            }
        }

        // Si hay un filtro de estado pero no de fecha, aplicamos el filtro de estado
        if ($filtroEstado && !$filtroFecha) {
            $query->where('estado_presupuesto', $filtroEstado);
        }

        return $query;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                $estado = $model->estado_presupuesto;

                // === CASO: DETALLE SOLO
                if (in_array($estado, ['Rechazado por Cliente', 'Cerrada', 'Servicio Realizado, Solicitud Cerrada',])) {
                    return '
                <div style="width: 240px">
                    <button 
                        wire:click="$dispatch(\'openModalServiciosSolicitados\', { data: ' . $model->getKey() . ' })"
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center gap-2 shadow-md transition h-12 w-full"
                        title="Ver Detalle">
                        <i class="fa-solid fa-circle-info"></i>
                        <span class="truncate">Detalle</span>
                    </button>
                </div>';
                }

                // === OTROS ESTADOS
                $html = '<div style="width: 240px" class="flex items-center justify-between">';

                $btnBase = 'text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center gap-2 shadow-md transition h-12';

                // === VISITAR
                if ($estado === 'Aceptado') {
                    $html .= '
                <div style="width: 120px">
                    <button
                        wire:click="$dispatch(\'visitar\', { data: ' . $model->id_solicitud . ' })"
                        class="bg-green-500 hover:bg-green-600 ' . $btnBase . ' w-full"
                        title="Visitar">
                        <i class="fa-solid fa-sign-in-alt"></i>
                        <span class="truncate">Visitar</span>
                    </button>
                </div>';
                }

                // === COTIZAR
                if ($estado === 'Esperando confirmación de prestadora' && optional($this->usuario_empresa)->tipo_user == '2') {
                    $html .= '
                <div style="width: 240px">
                    <button
                        wire:click="$dispatch(\'cotizar\', { data: ' . $model->id_solicitud . ' })"
                        class="bg-blue-500 hover:bg-blue-600 ' . $btnBase . ' w-full"
                        title="Cotizar">
                        <i class="fa-solid fa-handshake"></i>
                        <span class="truncate">Cotizar</span>
                    </button>
                </div>';
                }

                // === ENCARGADO Y CANCELAR
                if (optional($this->usuario_empresa)->tipo_user == '2' && $estado === 'Aceptado') {
                    $html .= '
                <div style="width: 50px">
                    <button
                        wire:click="asignarEncargado(' . $model->id_solicitud . ')" 
                        class="bg-gray-500 hover:bg-gray-600 ' . $btnBase . ' w-full"
                        title="Asignar Técnico Encargado">
                        <i class="fa-solid fa-hard-hat"></i>
                    </button>
                </div>
                <div style="width: 50px">
                    <button
                        wire:click="$dispatch(\'cancelarVisita\', { data: ' . $model->id_solicitud . ' })"
                        class="bg-red-500 hover:bg-red-600 ' . $btnBase . ' w-full"
                        title="Cancelar Visita">
                        <i class="fa fa-times"></i>
                    </button>
                </div>';
                }

                $html .= '</div>';
                return $html;
            })
                ->clickable(false)
                ->asHtml(),
            Column::make(__('Servicio solicitado'), 'servicios.nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Bien para el servicio'), 'activos.nombre')
                ->sortable()
                ->searchable(),
            Column::make(__('Solicitante'), 'users.name'),
            Column::make(__('Descripción'), 'descripcion'),
            DateColumn::make(__('Fecha'), 'fechaHora')
                ->format('Y-m-d')
                ->sortable(),
            Column::make(__('Estado de Solicitud'), 'estado_presupuesto')
                ->searchable(),
            // Columna "Cotización" siempre presente
            Column::make(__('Cotización'), 'cotizacion')
                ->asHtml(),
            Column::make(__('Encargado'), 'encargado')
                ->sortable()
                ->searchable(),
            Column::make(__('Tipo Cliente'), 'tipo_cliente'),
        ];
    }

    public function cancelarVisita($data)
    {
        $this->dispatch('rechazar', ['servicioId' => $data])->to('servicios.servicio-motivo-rechazo');
    }

    public function asignarEncargado($data)
    {
        $this->dispatch('openASignarTecnicoEncargado', ['servicioId' => $data])->to('servicios.asignar-tecnico-encargado');
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Estado Presupuesto'), 'estado_presupuesto')
                ->options([
                    'Visita expirada'                           => 'Visita expirada',
                    'Rechazado'                                 => 'Rechazado',
                    'Cotizacion expirada'                       => 'Cotizacion expirada',
                    'Servicio Realizado, Solicitud Cerrada'     => 'Servicio Realizado, Solicitud Cerrada',
                    'Solicitud Cerrada'                         => 'Solicitud Cerrada',
                    'Confirmado por Cliente y esperando visita' => 'Confirmado por Cliente y esperando visita',
                    'Esperando confirmación de prestadora'      => 'Esperando confirmación de prestadora',
                    'Esperando confirmacion del Cliente'        => 'Esperando confirmacion del Cliente',
                    'Rechazado por Cliente'                     =>  'Rechazado por Cliente',
                    ' Rechazado por la prestadora'              =>  'Rechazado por la prestadora',

                ]),

            DateFilter::make(__('Fecha'), 'fechaHora'),

            // Filtro de Servicios Asignados
            SelectFilter::make(__('Servicios Asignados'), 'tecnico_id')
                ->options([
                    $this->usuario_empresa->id_usuario => 'Mis Servicios',
                ]),
        ];
    }

    public function visitar($data)
    {
        session(['previous_url' => url()->current()]);
        return redirect()->route('servicios-realizar-servicios', ['servicio' => $data]);
    }

    protected function actions(): array
    {
        return [
            Action::make(__('Exportar'), 'export', function (Enumerable $models): mixed {
                return Excel::download(
                    new SolicitudesServiciosExport($models),
                    'ResumenServicios.xlsx'
                );
            }),
        ];
    }
}
