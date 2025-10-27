<?php

namespace App\Livewire\Activos\Bienes;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Columns\SelectColumn;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Enumerable;
use App\Exports\ActivosExport;
use Livewire\Attributes\On;
use App\Models\ActivosModel;
use App\Helpers\IdHelper;
use App\Models\ActivosAsignacionModel;
use App\Models\ActivosCompartidosModel;
use Carbon\Carbon;
use App\Helpers\Funciones;

class BienesPendientesTerceros extends LivewireTable
{
    protected string $model = ActivosCompartidosModel::class;

    public $title = 'Bienes Pendientes Clientes'; // Nombre del encabezado
    public $createForm = ''; // Nombre de la función que llama al evento
    public int $tipoId, $userId = 0, $id_activo, $user;
    public $fecha_asignacion, $fecha_fin;

    protected $listeners = ['removerDelegacion', 'admitirDelegacion'];

    public function asignar()
    {
        $this->userId = IdHelper::identificador();
        $this->user = auth()->id();
    }

    protected function query(): Builder
    {
        $this->asignar();

        $query = $this->model()->query()
            ->where(function ($subquery) {
                $subquery->where('empresa_proveedora', '=', $this->userId)
                    ->where('estado_asignacion', 'En Revisión')
                    ->where('fecha_fin', null);
            });

        $query->leftJoin('act.activos', 'act.activos.id_activo', '=', 'activos_compartidos.id_activo')
            ->orderBy('act.activos.fecha_creacion', 'desc');
            
        // Condicion para visualizar los datos de los activos unicamente de dicha empresa
        if (auth()->user()->panel_actual == 'Empresa' || auth()->user()->panel_actual == 'Prestadora') {

            // 🔍 Verificar si el usuario es Apoderado
            $esApoderado = \App\Models\UsuariosEmpresasModel::where('id_usuario', auth()->id())
                ->where('cuit', $this->userId)
                ->where('cargo', 'Aceptado')
                ->exists();

            // Mostrar unicamente los activos que pertenecen a la empresa y que han sido delegados
            $activosIds = \App\Models\ActivosCompartidosModel::where('empresa_proveedora', $this->userId)
                ->where('estado_asignacion', 'En Revisión')
                ->whereNull('fecha_fin') // Filtrar activos que no tienen fecha de fin
                ->pluck('id_activo');

            // Solo el apoderado de la empresa, puede ver los bienes que delegó
            if ($esApoderado) {
                // Si es apoderado, solo puede ver los activos que ha delegado
                $query = \App\Models\ActivosModel::whereIn('id_activo', $activosIds);
            }
        }

        return $query;
    }

    protected function filtro(Builder $query): Builder
    {
        $filtroSeleccionado = $this->filters['id_estado_sit_general'] ?? null;

        if (!empty($filtroSeleccionado)) {
            $query->whereHas('estadoGeneral', function ($query) use ($filtroSeleccionado) {
                $query->where('id_estado_sit_general', $filtroSeleccionado);
            });
        } else {
            // Por defecto, se muestran solo los que están en estado "Normal"
            $query->whereHas('estadoGeneral', function ($query) {
                $query->whereNotIn('id_estado_sit_general', Funciones::activosAmbos());
            });
        }

        return $query;
    }

    protected function columns(): array
    {
        return [
            Column::make(__('Empresa'), 'empresaTitular.razon_social')
                ->sortable()
                ->searchable(),
            Column::make(__('CUIT'), 'empresaTitular.cuit')
                ->sortable()
                ->searchable(),
            Column::make(__('Estado delegación'), 'estado_asignacion')
                ->sortable()
                ->searchable(),
            Column::make(__('Creado'), function (Model $model): string {
                $fecha = $model->fecha_creacion;
                return $fecha
                    ? Carbon::parse($fecha)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d')
                    : 'No hay datos';
            })
                ->sortable()
                ->searchable(),
            Column::make(__('Nombre'), function (Model $model): string {

                $id_activos_compartidos = $model->getKey();

                $id_activo = $model::where('id_activos_compartidos', $id_activos_compartidos)->pluck('id_activo');

                $activo = \App\Models\ActivosModel::where('id_activo', $id_activo)->first();

                // Verificacion de ausencia de informacion, se pueden agregar los campos aqui que se validen
                $faltaInfo = empty($activo->id_ubicacion);

                // Detalles sobre los datos faltantes
                $datosFaltantes = [];
                if ($faltaInfo) {
                    $datosFaltantes[] = 'Ubicacion';
                }

                // Si hay datos faltantes, se muestra un mensaje
                $mensajeFaltante = empty($datosFaltantes) ? '' : 'Falta: ' . implode(', ', $datosFaltantes);

                // Agregar icono de advertencia
                $iconoAdvertencia = $faltaInfo
                    ?   '<span class="text-red-500 ml-2 text-2xl" title="' . e($mensajeFaltante) . '">
                            <i class="fa-solid fa-exclamation-circle"></i>
                        </span>'
                    : '';

                return '<span class="' . ($faltaInfo ? 'text-red-500 font-bold' : '') . '">' .
                    e($activo->nombre) .
                    '</span>' . $iconoAdvertencia;
            })->sortable()->searchable()->asHtml(),
            // Column::make(__('Tipo'), 'tipo.nombre')->sortable()->searchable(),
            // Column::make(__('Categoria'), 'categoria.nombre')->sortable()->searchable(),
            // Column::make(__('Subcategoria'), 'subcategoria.nombre')->sortable()->searchable(),
            Column::make(__('Ubicacion'), function (Model $model): string {

                // Obtener la ubicación a través de ActivosCompartidos -> ActivosModel -> UbicacionesModel
                $ubicacion = $model->activo->ubicacion ? $model->activo->ubicacion->nombre : 'Sin ubicación';

                return e($ubicacion);
            })->sortable()->searchable()->asHtml(),
            Column::make(__('Estado'), function (Model $model): string {

                // Obtener la ubicación a través de ActivosCompartidos -> ActivosModel -> Estado General
                $estado = $model->activo->estadoGeneral ? $model->activo->estadoGeneral->nombre : 'Sin estado';

                return e($estado);
            })->sortable()->searchable()->asHtml(),
            Column::make(__('Acciones'), function (Model $model): string {

                $id_activos_compartidos = $model->getKey();

                $button = '
                <button 
                    class="w-28 h-12 px-4 py-3 rounded-md bg-green-500 hover:bg-green-600 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    wire:click="aceptarDelegacion(' . $id_activos_compartidos . ')"
                >
                    Aceptar Delegación
                </button>
                <button 
                    class="w-28 h-12 px-4 py-3 rounded-md bg-red-500 hover:bg-red-600 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    wire:click="confirmarDelegacion(' . $id_activos_compartidos . ')"
                >
                    Eliminar Delegación
                </button>
            ';
                return $button;
            })->clickable(false)->asHtml(),
        ];
    }

    protected function filters(): array
    {
        return [
            SelectFilter::make(__('Tipo'), 'id_tipo')->options($this->getTipos()),
            SelectFilter::make(__('Categoria'), 'id_cat')->options($this->getCategorias()),
            SelectFilter::make(__('Subcategoria'), 'id_subcat')->options($this->getSubcategorias()),
            SelectFilter::make(__('Estado General'), 'id_estado_sit_general')->options($this->getEstados()),
            SelectFilter::make(__("Ubicaciones"), 'id_ubicacion')->options($this->getUbicaciones()),
        ];
    }

    protected function getUbicaciones()
    {
        $activosIds = \App\Models\ActivosCompartidosModel::where('estado_asignacion', 'En Revisión')
            ->where('empresa_proveedora', $this->userId)
            ->pluck('id_activo');

        $empresasCuit = \App\Models\ActivosCompartidosModel::where('estado_asignacion', 'En Revisión')
            ->where('empresa_proveedora', $this->userId)
            ->pluck('empresa_titular');

        $ubicaciones = ActivosModel::whereIn('id_activo', $activosIds)
            ->whereIn('empresa_titular', $empresasCuit)
            ->has('ubicacion')
            ->with('ubicacion')
            ->get();

        return $ubicaciones->pluck('ubicacion.nombre', 'ubicacion.id_ubicacion')->toArray();
    }

    protected function getEstados()
    {
        $activosIds = \App\Models\ActivosCompartidosModel::where('estado_asignacion', 'En Revisión')
            ->where('empresa_proveedora', $this->userId)
            ->pluck('id_activo');

        $empresasCuit = \App\Models\ActivosCompartidosModel::where('estado_asignacion', 'En Revisión')
            ->where('empresa_proveedora', $this->userId)
            ->pluck('empresa_titular');

        $estado = ActivosModel::whereIn('id_activo', $activosIds)
            ->whereIn('empresa_titular', $empresasCuit)
            ->has('estadoGeneral')
            ->with('estadoGeneral')
            ->get();

        return $estado->pluck('estadoGeneral.nombre', 'estadoGeneral.id_estado_sit_general')->toArray();
    }

    public function confirmarDelegacion($id)
    {
        // Lanzar el SweetAlert desde Livewire
        $mensaje = "Está a punto de eliminar la delegación del bien";
        $this->dispatch('eliminarDelegacion', ['message' => $mensaje, 'id' => $id]);
    }

    public function aceptarDelegacion($id)
    {
        // Lanzar el SweetAlert desde Livewire
        $mensaje = "Está a punto de aceptar la delegación del bien";
        $this->dispatch('mostrarConfirmacionDelegacion', ['message' => $mensaje, 'id' => $id]);
    }

    // Para eliminar la delegacion del bien
    public function removerDelegacion($id)
    {
        $compartidos  = ActivosCompartidosModel::where('id_activos_compartidos', $id)
            ->whereNotIn('estado_asignacion', ['Cancelado', 'Aceptado'])
            ->whereNull('fecha_fin')
            ->first();

        if ($compartidos) {
            // Actualizar los registros de ActivosCompartidosModel
            $compartidos->update([
                'estado_asignacion' => 'Cancelado',
                'fecha_fin' => Carbon::parse($this->fecha_fin)->format('Y-m-d'),
            ]);
        }

        $this->dispatch('Exito', [
            'title'   => 'Cancelacion de delegación',
            'message' => 'El bien no ha sido aceptado como delegado.'
        ]);
        $this->dispatch('refreshLivewireTable'); // Refrescar la tabla
    }

    // Para eliminar la delegacion del bien
    public function admitirDelegacion($id)
    {
        $compartidos  = ActivosCompartidosModel::where('id_activos_compartidos', $id)
            ->whereNotIn('estado_asignacion', ['Cancelado', 'Aceptado'])
            ->whereNull('fecha_fin')
            ->first();

        $activos = ActivosModel::where('id_activo', $compartidos->id_activo)->first();

        if ($compartidos && $activos) {
            // Crear registro en ActivosAsignacionModel
            ActivosAsignacionModel::create([
                'id_activo' => $activos->id_activo,
                'id_tipo' => $activos->id_tipo,
                'id_categoria' => $activos->id_categoria,
                'id_subcategoria' => $activos->id_subcategoria,
                'asignado_a' => null,
                'gestionado_por' => auth()->user()->id,
                'fecha_asignacion' => Carbon::parse($this->fecha_asignacion)->format('Y-m-d H:i:s'),
                'responsable' => null,
                'empresa_empleados' => $this->userId,
                'estado_asignacion' => 'Aceptado'
            ]);
        }

        // Actualizar los registros de ActivosCompartidosModel
        $compartidos->update([
            'estado_asignacion' => 'Aceptado'
        ]);

        $this->dispatch('Exito', [
            'title'   => 'Aceptación de delegación',
            'message' => 'El bien ha sido aceptado.'
        ]);
        $this->dispatch('refreshLivewireTable'); // Refrescar la tabla
    }

    protected function getTipos()
    {
        $tiposConCategorias = ActivosModel::whereHas('compartidos', function ($query) {
            $query->whereNull('fecha_fin')
                ->where('estado_asignacion', 'En Revisión')
                ->where('empresa_proveedora', IdHelper::idEmpresa());
        })
            ->whereNot('id_estado_sit_general', Funciones::activoService())
            ->has('tipo')->get();

        return $tiposConCategorias->pluck('tipo.nombre', 'tipo.id_tipo')->toArray();
    }

    protected function getCategorias()
    {
        $userId = auth()->id();
        $tipoSeleccionado = $this->filters['id_tipo'] ?? null;

        $query = ActivosModel::whereHas('compartidos', function ($q) {
            $q->whereNull('fecha_fin')
                ->where('estado_asignacion', 'En Revisión')
                ->where('empresa_proveedora', IdHelper::idEmpresa());
        })
            ->whereNot('id_estado_sit_general', Funciones::activoService())
            ->has('categoria');

        // Si hay tipo seleccionado, filtrar también por tipo
        if ($tipoSeleccionado) {
            $query->where('id_tipo', $tipoSeleccionado);
        }

        $categorias = $query->get();

        return $categorias->pluck('categoria.nombre', 'categoria.id_categoria')->toArray();
    }

    protected function getSubcategorias()
    {
        $categoriaSeleccionada = $this->filters['id_cat'] ?? null;

        $query = ActivosModel::whereHas('compartidos', function ($q) {
            $q->whereNull('fecha_fin')
                ->where('estado_asignacion', 'En Revisión')
                ->where('empresa_proveedora', IdHelper::idEmpresa());
        })
            ->whereNot('id_estado_sit_general', Funciones::activoService())
            ->whereNotNull('act.activos.id_subcategoria');

        // Si hay tipo seleccionado, filtrar también por tipo
        if ($categoriaSeleccionada) {
            $query->where('act.activos.id_categoria', $categoriaSeleccionada);
        }

        $subcategorias = $query
            ->join('act.subcategorias', 'activos.id_subcategoria', '=', 'act.subcategorias.id_subcategoria')
            ->select('act.subcategorias.id_subcategoria', 'act.subcategorias.nombre')
            ->distinct()
            ->get();

        return $subcategorias->pluck('nombre', 'id_subcategoria')->toArray();
    }
}
