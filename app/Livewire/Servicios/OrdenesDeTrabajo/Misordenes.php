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

class Misordenes extends LivewireTable
{
    protected string $model = OrdenesModel::class;

    public $title = 'Mis Ordenes';
    public $createForm = '';
    protected bool $useSelection = false;

    public $empresa, $clientes_empresas, $key, $usuario, $roles;

    protected $listeners = ['RechazarOrden', 'refreshMisOrdenes'];

    public function asignar()
    {
        $this->empresa = IdHelper::idEmpresa();
        $this->usuario = auth()->id();
        if ($this->roles = $this->roles()) {
            $this->createForm = 'crearOrdenes';
        }
    }

    protected function query(): Builder
    {
        $this->asignar();

        $idsRol = \App\Models\RolesModel::where('nombre', 'Prestadora - Manager')
            ->pluck('id_rol');

        $prestadoraManager = \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();

        $idsRol = \App\Models\RolesModel::where('nombre', 'Usuario Tecnico Empresa Prestadora')
            ->pluck('id_rol');

        $tecnicoPrestadora = \App\Models\AsignacionesRolesModel::where('usuario_empresa', Auth::user()->id)
            ->whereIn('id_rol', $idsRol)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();

        if ($prestadoraManager) {
            return $this->model::where('proveedor', $this->empresa)
                ->where('act.ordenes.id_usuario', $this->usuario)
                ->where(function ($query) {
                    $query->where('estado_vigencia', '!=', 'Cerrado');
                });
        } elseif ($tecnicoPrestadora) {
            return $this->model::where('proveedor', $this->empresa)
                ->where(function ($query) {
                    $query->where('estado_vigencia', '=', 'Cerrado')
                        ->where('act.ordenes.representante_tecnico', $this->usuario);
                });
        } else {
            return $this->model::where('proveedor', $this->empresa);
        }
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Acciones'), function (Model $model): string {
                $id = $model->getKey();
                $estado = $model->estado_orden;
                $esPendiente = $estado === 'Pendiente';

                // Colores / íconos
                $color      = $esPendiente ? '#BBF7D0' : '#BFDBFE';
                $textColor  = $esPendiente ? 'text-green-800' : 'text-blue-800';
                $icono      = $esPendiente ? 'fa-sign-in-alt' : 'fa-circle-info';
                $tooltip    = $esPendiente ? 'Visitar' : 'Detalles';

                // URL directa a la vista contenedora (sin nombre de ruta)
                $url = url("/servicios/ordenes/{$id}/cerrar");

                // Botón principal como <a>
                $botonPrincipal = sprintf(
                    '<a href="%s"
                        style="background-color:%s;"
                        class="%s font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                        title="%s">
                        <i class="fa-solid %s text-base"></i>
                    </a>',
                    e($url),
                    $color,
                    $textColor,
                    e($tooltip),
                    e($icono)
                );

                // Botón Rechazar (solo si pendiente)
                $rechazarBoton = '';
                if ($esPendiente) {
                    $rechazarBoton = sprintf(
                        '<button 
                            wire:click="confirmarRechazo(%d)" 
                            style="background-color: #FECACA;"
                            class="text-red-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12 transition"
                            title="Rechazar Orden">
                            <i class="fa fa-times text-base"></i>
                        </button>',
                        $id
                    );
                }

                return '<div class="flex space-x-2 items-center">' . $botonPrincipal . $rechazarBoton . '</div>';
            })
                ->clickable(false)
                ->asHtml(),

            Column::make(__('Estado vigencia'), 'estado_vigencia'),
            Column::make(__('Cliente'), 'Cliente.razon_social'),
            Column::make(__('comentarios'), 'comentarios'),
            Column::make(__('Tecnico asignado'), 'Representante_tecnico.usuarios.name'),
            Column::make(__('Tipo de orden'), 'tipo_orden_colored')->asHtml(),
            Column::make(__('Estado orden'), 'estado_orden_colored')->asHtml(),
            DateColumn::make(__('Fecha'), 'fecha')->format('Y-m-d'),
        ];
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

    public function confirmarRechazo($id)
    {
        $mensaje = "Esta acción cancelará la orden de forma definitiva. ¿Desea continuar?";
        $this->dispatch('rechazarOrden', ['message' => $mensaje, 'id' => $id]);
    }

    public function RechazarOrden($id)
    {
        $orden = OrdenesModel::where('id_orden', $id);

        if ($orden) {
            $orden->update([
                'estado_orden'   => 'Rechazado',
                'estado_vigencia' => 'Cerrado',
            ]);

            $this->dispatch('Exito', [
                'title'   => 'Orden Rechazada',
                'message' => 'La orden de trabajo ha sido rechazada correctamente.'
            ]);
            $this->dispatch('refreshLivewireTable');
        }
    }

    public function crearOrdenes()
    {
        return redirect()->route('ordenes-de-trabajo');
    }

    public function refreshMisOrdenes()
    {
        $this->render();
    }
}
