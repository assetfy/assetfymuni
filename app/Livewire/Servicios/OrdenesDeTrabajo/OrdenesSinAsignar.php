<?php

namespace App\Livewire\Servicios\OrdenesDeTrabajo;

use App\Models\OrdenesModel;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\DateColumn;
use Illuminate\Database\Eloquent\Builder;
use App\Helpers\IdHelper;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class OrdenesSinAsignar extends LivewireTable
{
    protected string $model = OrdenesModel::class;
    public $title = 'Mis Ordenes'; // Nombre del emcabezado
    public $createForm = ''; // Nombre del componente de creación predeterminado
    protected bool $useSelection = false;
    public $empresa, $clientes_empresas, $key, $usuario, $roles;
    protected bool $modalDispatched = false;

    protected $listeners = ['RechazarOrden', 'refreshMisOrdenes'];

    public function asignar()
    {
        $this->empresa = IdHelper::idEmpresa();
        $this->usuario = auth()->id();
        if ($this->roles = $this->roles()) {
            $this->createForm = 'crearOrdenes';
        }
    }

    public function hydrate(): void
    {
        if (! $this->modalDispatched) {
            $this->dispatch('openModal', [
                'servicios.OrdenesDeTrabajo.editar-orden',
                'servicios.ordenes-de-trabajo.asginar-tecnico-encargado-ordenes'
            ]);
            $this->modalDispatched = true;
        }
    }

    protected function query(): Builder
    {
        $this->asignar();
        $query = $this->model::where('proveedor', $this->empresa)
            ->where('representante_tecnico', null)
            ->where('estado_vigencia', '!=', 'Cerrado');

        return $query;
    }


    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                $id = $model->getKey();
                $esPendiente = $model->estado_orden === 'Pendiente';

                $html = '<div class="flex items-center space-x-2">';

                // === Botón Ver Detalle / Editar ===
                $color     = $esPendiente ? '#BBF7D0' : '#BFDBFE';
                $textColor = $esPendiente ? 'text-green-800' : 'text-blue-800';
                $icono     = $esPendiente ? 'fa-sign-in-alt' : 'fa-circle-info';
                $tooltip   = $esPendiente ? 'Visitar Orden' : 'Ver Detalles';

                $html .= sprintf(
                    '<button type="button"
                    wire:click="openEditOrdens(%d)"
                    style="background-color:%s;"
                    class="%s font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                    title="%s">
                    <i class="fa-solid %s text-base"></i>
                </button>',
                    $id,
                    $color,
                    $textColor,
                    e($tooltip),
                    $icono
                );

                // === Botón Rechazar (solo Pendiente) ===
                if ($esPendiente) {
                    $html .= sprintf(
                        '<button type="button"
                        wire:click="confirmarRechazo(%d)"
                        style="background-color:#FECACA;"
                        class="text-red-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                        title="Rechazar Orden">
                        <i class="fa fa-times text-base"></i>
                    </button>',
                        $id
                    );
                }

                // === Botón Asignar Técnico (solo Pendiente) ===
                if ($esPendiente) {
                    $html .= sprintf(
                        '<button type="button"
                        wire:click="$dispatch(\'openASignarTecnicoEncargadoOrdenes\', { data: %d })"
                        style="background-color:#C7D2FE;"
                        class="text-indigo-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                        title="Asignar Técnico Encargado">
                        <i class="fa-solid fa-hard-hat text-base"></i>
                    </button>',
                        $id
                    );
                }

                $html .= '</div>';

                return $html;
            })
                ->clickable(false)
                ->asHtml(),


            Column::make(__('Id Orden'), 'id_orden')
                ->sortable()
                ->searchable(),
            Column::make(__('Estado vigencia'), 'estado_vigencia'),
            Column::make(__('comentarios'), 'comentarios'),
            Column::make(__('Tecnico asignado'), 'Representante_tecnico.usuarios.name'),
            Column::make(__('Tipo de orden'), 'tipo_orden_colored')->asHtml(),
            Column::make(__('Estado orden'), 'estado_orden_colored')->asHtml(),
            DateColumn::make(__('Fecha Creación'), 'fecha')
                ->format('Y-m-d'),
            Column::make(__('Ubicacion'), function (Model $model): string {
                // Usamos el operador nullsafe para evitar errores si alguna relación no existe
                return e($model->activos?->ubicacion?->nombre ?? 'Sin ubicación');
            })->sortable()->asHtml(),
        ];
    }

    public function openEditOrdens(int $idOrden)
    {
        return redirect()->route('servicios.ordenes-de-trabajo.cerrar-orden', ['id' => $idOrden]);
    }

    private function roles()
    {
        $idsRol = \App\Models\RolesModel::where('nombre', 'Admin Empresa Prestadora')
            ->orWhere('nombre', 'Usuario Gestor')
            ->pluck('id_rol');

        return \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();
    }

    public function crearOrdenes()
    {
        return redirect()->route('ordenes-de-trabajo');
    }
}
