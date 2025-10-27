<?php

namespace App\Livewire\Usuarios;

use App\Exports\CotizacionesExport;
use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Enums\Direction;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Actions\Action;
use App\Models\SolicitudesServiciosModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Enumerable;
use App\Models\EmpresasModel;
use App\Helpers\IdHelper;

class UsuariosServicios extends LivewireTable
{
    protected string $model = SolicitudesServiciosModel::class;
    public $title = 'Cotizaciones'; // Nombre del emcabezado
    public $createForm = 'crearCotizacion'; // Nombre del componente de creación predeterminado
    public $editForm = ''; // Nombre del componente de edición predeterminado
    public $cuit, $municipio, $esApoderado;
    protected bool $useSelection = true;

    protected function query(): Builder
    {
        $servicios = Auth::user();
        $tipoEmpresa = $this->tipoEmpresa();
        $manager = $this->rolGestor();
        $this->dispatch('openModal', ['usuarios.usuarios-servicios-autorizacion']);
        if ($tipoEmpresa == 1) {
            if ($manager || $this->esApoderado) {
                return $this->model::where('empresa_solicitante', IdHelper::idEmpresa())
                    ->whereNotIn('estado_presupuesto', ['Servicio Realizado, Solicitud Cerrada', 'Aceptado']);
            } else {
                return $this->model::where('id_solicitante', $servicios->id)
                    ->whereNotIn('estado_presupuesto', ['Servicio Realizado, Solicitud Cerrada', 'Aceptado']);
            }
        } else {
            if ($manager || $this->esApoderado) {
                return $this->model::where('empresa_prestadora', IdHelper::idEmpresa())
                    ->whereNotIn('estado_presupuesto', ['Servicio Realizado, Solicitud Cerrada', 'Aceptado']);
            } else {
                return $this->model::where('empresa_prestadora', IdHelper::idEmpresa())
                    ->whereNotIn('estado_presupuesto', ['Servicio Realizado, Solicitud Cerrada', 'Aceptado']);
            }
        }
    }

    protected function getServicios()
    {
        $this->getValue();

        $consulta = SolicitudesServiciosModel::query();

        $servicioSolicitados = $consulta->where('empresa_prestadora', $this->cuit)->get();

        return $servicioSolicitados->pluck('id_solicitud');
    }

    protected function getValue()
    {
        $this->cuit = IdHelper::idEmpresa();
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                return sprintf(
                    '<div class="flex items-center">
                        <button 
                            wire:click="$dispatch(\'openModalServiciosSolicitados\', { data: %d })"
                            style="background-color: #BFDBFE;"
                            class="text-blue-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                            title="Ver Detalle">
                            <i class="fa-solid fa-circle-info text-base"></i>
                        </button>
                    </div>',
                    $model->getKey()
                );
            })
                ->clickable(false)
                ->asHtml(),

            Column::make(__('id cotizacion'), 'id_solicitud')
                ->searchable(),
            DateColumn::make(__('Fecha'), 'fechaHora')
                ->format('Y-m-d')
                ->sortable(),
            Column::make(__('Nombre del bien'), 'activos.nombre')
                ->sortable(),
            Column::make(__('Titulo de la Solicitud'), 'Nombre_solicitud')
                ->sortable(),
            Column::make(__('Prestadora'), 'empresasPrestadora.razon_social')
                ->sortable(),
            Column::make(__('Precio'), 'precio') // atributo real de BD
                ->displayUsing(
                    fn(?float $value) =>
                    empty($value) || $value == 0 ? 'Sin datos' : '$' . number_format((float)$value, 2, ',', '.')
                )
                ->sortable(function (Builder $b, Direction $d): void {
                    $b->reorder($b->getModel()->qualifyColumn('precio'), $d->value);
                }),
            Column::make(__('Garantias'), 'garantia_display'),
            Column::make(__('Estado Presupuesto'), 'estado_presupuesto'),
        ];
    }

    public function isSelectable($row): bool
    {
        // Lógica específica de selección por fila
        return true;
    }

    private function rolGestor()
    {
        // 🔍 Verificar si el usuario es Apoderado
        $this->esApoderado = \App\Models\UsuariosEmpresasModel::where('id_usuario', auth()->user()->id)
            ->where('cuit', IdHelper::idEmpresa())
            ->where('cargo', 'Apoderado')
            ->exists();

        $idsRol = \App\Models\RolesModel::where('nombre', 'Admin Empresa')
            ->orWhere('nombre', 'Admin Empresa Prestadora')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::where('usuario_empresa', auth()->user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();
    }

    private function tipoEmpresa()
    {
        return EmpresasModel::where('cuit', (int) auth()->user()->entidad)
            ->pluck('tipo')
            ->first();
    }

    public function crearCotizacion()
    {
        return redirect()->route('servicios-vista-formulario');
    }

    protected function actions(): array
    {
        return [
            Action::make('Exportar Historial', 'export_selected', function (Enumerable $models) {
                // $models son los registros marcados
                return Excel::download(
                    new CotizacionesExport($models),
                    'historial_Ordenes.xlsx'
                );
            }),
        ];
    }
}
