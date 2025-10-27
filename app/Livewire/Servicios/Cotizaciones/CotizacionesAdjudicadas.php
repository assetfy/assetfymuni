<?php

namespace App\Livewire\Servicios\Cotizaciones;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use App\Models\SolicitudesServiciosModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use App\Helpers\IdHelper;
use App\Models\EmpresasModel;

class CotizacionesAdjudicadas extends LivewireTable
{
    protected string $model = SolicitudesServiciosModel::class;
    public $title = 'Cotizaciones de servicios'; // Nombre del emcabezado
    public $createForm = 'crearCotizacion'; // Nombre del componente de creación predeterminado
    public $editForm = 'usuarios.usuarios-servicios-autorizacion'; // Nombre del componente de edición predeterminado
    public $cuit, $municipio, $esApoderado, $tipoEmpresa;
    protected bool $useSelection = false;

    protected function query(): Builder
    {
        $servicios = Auth::user();
        $tipoEmpresa = $this->tipoEmpresa();
        $manager = $this->rolGestor();

        if ($manager || $this->esApoderado) {
            return $this->model::where('empresa_solicitante', IdHelper::idEmpresa())
                ->where('estado_presupuesto', 'Aceptado');
        } else {
            return $this->model::where('id_solicitante', $servicios->id)
                ->where('estado_presupuesto', 'Aceptado');
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
            Column::make(__('Id cotizacion'), 'id_solicitud')
                ->searchable(),
            DateColumn::make(__('Fecha'), 'fechaHora')
                ->format('Y-m-d')
                ->sortable(),
            Column::make(__('Tipo'), 'tipoServicio.nombre')
                ->sortable(),
            Column::make(__('Nombre del bien'), 'activos.nombre')
                ->sortable(),
            Column::make(__('Titulo de la Solicitud'), 'Nombre_solicitud')
                ->sortable(),
            Column::make(__('Precio'), 'precio')
                ->sortable(),
            Column::make(__('Estado Presupuesto'), 'estado_presupuesto'),
            Column::make(__('Acciones'), function (Model $model): string {
                return
                    '<button wire:click="$dispatch(\'openModalServiciosSolicitados\', { data: ' . $model->getKey() . ' })" 
                            class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            Detalle
                            </button>';
            })
                ->clickable(false)
                ->asHtml(),
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
}
