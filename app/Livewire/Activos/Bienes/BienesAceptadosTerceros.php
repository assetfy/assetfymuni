<?php

namespace App\Livewire\Activos\Bienes;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\Column;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use App\Models\ActivosModel;
use App\Helpers\IdHelper;
use App\Models\ActivosAsignacionModel;
use App\Models\ActivosCompartidosModel;
use App\Models\EstadoGeneralModel;
use App\Models\UsuariosEmpresasModel;
use Carbon\Carbon;
use App\Helpers\Funciones;

class BienesAceptadosTerceros extends LivewireTable
{
    protected string $model = ActivosCompartidosModel::class;

    public $title = 'Bienes de Clientes'; // Nombre del encabezado
    public $createForm = null; // Inicialmente vacío
    public $origen = 'bienes_aceptados'; // Identificador de la vista    
    public int $tipoId, $userId = 0, $id_activo, $user;
    public $fecha_asignacion, $fecha_fin_asignacion, $fecha_fin, $adminGestor, $admin, $id_estado_sit_general;

    protected $listeners = ['removerDelegacion', 'refreshBienesAceptados' => 'refreshTable'];

    public function asignar()
    {
        // dd(auth()->id());
        $this->userId = IdHelper::identificador();
        $this->user = auth()->id();
    }

    public function hydrate()
    {
        // Obtener el ID del usuario autenticado
        $usuarioId = auth()->id();

        // 🔍 Verificar si el usuario es Apoderado
        $esApoderado = \App\Models\UsuariosEmpresasModel::where('id_usuario', $usuarioId)
            ->where('cuit', $this->userId)
            ->where('cargo', 'Apoderado')
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();

        $idsRoles = \App\Models\RolesModel::where('nombre', 'Admin Empresa Prestadora')
            ->orWhere('nombre', 'Usuario Gestor')
            ->pluck('id_rol');

        $this->adminGestor = \App\Models\AsignacionesRolesModel::where('usuario_empresa', $usuarioId)
            ->whereIn('id_rol', $idsRoles)
            ->where('cuit', IdHelper::idEmpresa())
            ->exists();

        // dd($this->userId, $this->adminGestor);
        // Solo permitir el botón si el usuario es Apoderado
        if ($esApoderado || $this->adminGestor) {
            $this->createForm = 'crearActivos';
        } else {
            $this->createForm = null;
        }
    }

    protected function query(): Builder
    {
        $this->asignar();
        $this->dispatch('openModal', ['activos.create-activos', 'servicios.ordenes-de-trabajo.solicitar-ordenes-modal', 'activos.edit-activos', 'activos.asignacion-activos']);
        $query = $this->model()->query()
            ->where(function ($subquery) {
                $subquery->where('empresa_proveedora', '=', $this->userId)
                    ->where('estado_asignacion', 'Aceptado')
                    ->whereNull('fecha_fin');
            });

        $query->leftJoin('act.activos', 'act.activos.id_activo', '=', 'activos_compartidos.id_activo')
            ->orderBy('act.activos.fecha_creacion', 'desc');

        // Condicion para visualizar los datos de los activos unicamente de dicha empresa
        if (auth()->user()->panel_actual == 'Empresa' || auth()->user()->panel_actual == 'Prestadora') {

            // 🔍 Verificar si el usuario es Apoderado
            $esApoderado = \App\Models\UsuariosEmpresasModel::where('id_usuario', auth()->id())
                ->where('cuit', $this->userId)
                ->where('cargo', 'Apoderado')
                ->exists();

            // Mostrar unicamente los activos que pertenecen a la empresa y que han sido delegados
            $activosIds = \App\Models\ActivosCompartidosModel::where('empresa_proveedora', $this->userId)
                ->where('estado_asignacion', 'Aceptado')
                ->whereNull('fecha_fin') // Filtrar activos que no tienen fecha de fin
                ->pluck('id_activo');

            $idsRoles = \App\Models\RolesModel::where('nombre', 'Admin Empresa Prestadora')
                ->pluck('id_rol');

            $this->admin = \App\Models\AsignacionesRolesModel::where('usuario_empresa', auth()->id())
                ->whereIn('id_rol', $idsRoles)
                ->where('cuit', IdHelper::idEmpresa())
                ->exists();

            // Solo el apoderado de la empresa, puede ver los bienes que delegó
            if ($esApoderado || $this->admin) {
                // Si es apoderado, solo puede ver los activos que ha delegado
                $query = \App\Models\ActivosCompartidosModel::whereIn('id_activo', $activosIds)
                    ->where('empresa_proveedora', $this->userId)
                    ->where('estado_asignacion', '!=', 'Cancelado');
            } else {
                // ❌ No es Apoderado, filtrar por asignaciones
                $query->whereHas('asignaciones', function ($q) {
                    $q->where('responsable', $this->user)
                        ->orWhere('gestionado_por', $this->user)
                        ->orWhere('asignado_a', $this->user)
                        ->whereNull('fecha_fin')
                        ->where('empresa_empleados', $this->userId)
                        ->where('estado_asignacion', '!=', 'Cancelado');
                });
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
            Column::make(__('Empresa Propietaria'), 'empresaTitular.razon_social')
                ->sortable()
                ->searchable(),
            Column::make(__('CUIT'), 'empresaTitular.cuit')
                ->sortable()
                ->searchable(),
            Column::make(__('Estado delegación'), 'estado_asignacion')
                ->sortable()
                ->searchable(),
            Column::make(__('Creado por'), function (Model $model): string {

                // Obtener la ubicación a través de ActivosCompartidos -> ActivosModel -> Estado General
                $creacion = strtolower($model->activo->propietario) === 'propio' ? 'Cliente' : 'Prestadora';

                return e($creacion);
            })->sortable()->searchable(),
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
                $botones = '';
                $esApoderado = \App\Models\UsuariosEmpresasModel::where('id_usuario', auth()->id())
                    ->where('cuit', $this->userId)
                    ->where('cargo', 'Apoderado')
                    ->exists();

                // Panel actual del usuario
                $panelActual = auth()->user()->panel_actual;
                $userData    = auth()->id();

                // Obtener la asignación del usuario
                $asignacion = $model->asignaciones()
                    ->where(function ($query) use ($userData) {
                        $query->where('responsable', $userData)
                            ->orWhere('asignado_a', $userData)
                            ->orWhere('gestionado_por', $userData)
                            ->where('estado_asignacion', 'Aceptado')
                            ->whereNull('fecha_fin_asignacion')
                            ->where('estado_asignacion', '!=', 'Cancelado');
                    })
                    ->first();

                // Roles sobre el activo
                $asignado    = in_array($panelActual, ['Empresa', 'Prestadora']) && optional($asignacion)->asignado_a == $userData;
                $gestor      = in_array($panelActual, ['Empresa', 'Prestadora']) && optional($asignacion)->gestionado_por == $userData;
                $responsable = in_array($panelActual, ['Empresa', 'Prestadora']) && optional($asignacion)->responsable     == $userData;

                $id_activos_compartidos = $model->getKey();
                $id_activo              = $model::where('id_activos_compartidos', $id_activos_compartidos)
                    ->pluck('id_activo')
                    ->first();

                // Verificamos si el activo está dado de baja
                $isDisabled   = EstadoGeneralModel::where($this->id_estado_sit_general, Funciones::activoBaja());

                // URLs necesarias
                $detalleUrl   = "/activos/{$id_activo}";
                $controlesUrl = "/controles/controles-vista-detalle/{$id_activo}";

                // Botón Asignar
                $botonAsignar = '
                    <button 
                        wire:click="$dispatch(\'asignarActivo\', { data: ' . $id_activo . ' })" 
                        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'
                    . ($isDisabled ? ' opacity-50 cursor-not-allowed' : '') . '" 
                        title="Asignar activo" ' . ($isDisabled ? 'disabled' : '') . '>
                        <i class="fa-solid fa-user-plus"></i>
                    </button>';

                // Botón Solicitar Orden de Trabajo
                $botonSolicitarOrden = '
                    <button
                         wire:click="$dispatch(\'openSoliitarOrden\', { data: ' . $id_activo . ' })" 
                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded'
                    . ($isDisabled ? ' opacity-50 cursor-not-allowed' : '') . '"
                        title="Solicitar Orden de Trabajo"
                        ' . ($isDisabled ? 'disabled' : '') . '>
                        <i class="fa-solid fa-file-contract"></i>
                    </button>';

                // Botón Ver Detalles
                $botonDetalles = '
                    <button 
                        wire:click="$dispatch(\'editActivos\', { data: ' . $id_activo . ' })" 
                        class="bg-black hover:bg-gray-800 text-white font-bold py-2 px-4 rounded" 
                        title="Detalle">
                        <i class="fa-solid fa-eye"></i>
                    </button>';

                // Armo el HTML de botones según rol
                if ($gestor && $responsable) {
                    // Gestor & Responsable: Asignar + Controles + Orden + Detalles
                    $botones = '<div class="flex space-x-2">'
                        . $botonAsignar
                        . '<a href="' . $controlesUrl . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'
                        . ($isDisabled ? ' opacity-50 cursor-not-allowed' : '') . '" title="Controles"'
                        . ($isDisabled ? 'aria-disabled="true"' : '') . '>
                            <i class="fa-solid fa-cogs"></i>
                          </a>'
                        . $botonSolicitarOrden
                        . $botonDetalles
                        . '</div>';
                } elseif ($gestor || $this->admin) {
                    // Solo Gestor: Asignar
                    $botones = '<div class="flex space-x-2">' . $botonAsignar . $botonDetalles . '</div>';
                } elseif ($asignado) {
                    // Solo Asignado: Detalles
                    $botones = '<div class="flex space-x-2">' . $botonDetalles . '</div>';
                } elseif ($responsable) {
                    // Responsable: Controles + Orden + Detalles
                    $botones = '<div class="flex space-x-2">'
                        . '<a href="' . $controlesUrl . '" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded'
                        . ($isDisabled ? ' opacity-50 cursor-not-allowed' : '') . '" title="Controles"'
                        . ($isDisabled ? 'aria-disabled="true"' : '') . '>
                            <i class="fa-solid fa-cogs"></i>
                          </a>'
                        . $botonSolicitarOrden
                        . $botonDetalles
                        . '</div>';
                }
                // Si es admin y el botón de asignar no está ya presente, lo agregamos
                if ($this->admin && strpos($botones, 'fa-user-plus') === false) {
                    // Agregar el botón de asignar al inicio del contenedor
                    $botones = str_replace('<div class="flex space-x-2">', '<div class="flex space-x-2">' . $botonAsignar, $botones);
                }
                // Devolver los botones según la lógica
                return $botones;
            })->clickable(false)->asHtml(),
            // Column::make(__('Remover'), function (Model $model): string {

            //     $id_activos_compartidos = $model->getKey();

            //     $panelActual = auth()->user()->panel_actual;
            //     $userData = auth()->id();

            //     // Obtener la asignación del usuario en una sola consulta
            //     $asignacion = $model->asignaciones()
            //         ->where(function ($query) use ($userData) {
            //             $query->where('responsable', $userData)
            //                 ->orWhere('asignado_a', $userData)
            //                 ->orWhere('gestionado_por', $userData);
            //         })
            //         ->first();

            //     $gestor = ($panelActual === 'Empresa' || $panelActual === 'Prestadora') && optional($asignacion)->gestionado_por == $userData;

            //     // Inicializar $button como una cadena vacía
            //     $button = '';

            //     if ($gestor) {
            //         $button = '
            //                 <button 
            //                     class="w-28 h-8 px-4 py-2 rounded-md bg-red-500 hover:bg-red-600 text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
            //                     wire:click="confirmarDelegacion(' . $id_activos_compartidos . ')"
            //                 >
            //                     Eliminar Delegación
            //                 </button>
            //             ';
            //     }

            //     return $button;
            // })->clickable(false)->asHtml(),
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
        $activosIds = \App\Models\ActivosCompartidosModel::where('estado_asignacion', 'Aceptado')
            ->where('empresa_proveedora', $this->userId)
            ->pluck('id_activo');

        $empresasCuit = \App\Models\ActivosCompartidosModel::where('estado_asignacion', 'Aceptado')
            ->where('empresa_proveedora', $this->userId)
            ->pluck('empresa_titular');

        $ubicaciones = ActivosModel::whereIn('id_activo', $activosIds)
            ->whereIn('empresa_titular', $empresasCuit)
            ->has('ubicacion')
            ->with('ubicacion')
            ->get();

        return $ubicaciones->pluck('ubicacion.nombre', 'ubicacion.id_ubicacion')->toArray();
    }

    public function solicitarOrden()
    {
        return redirect()->to('servicios/ordenes-de-trabajos');
    }

    protected function getEstados()
    {
        $activosIds = \App\Models\ActivosCompartidosModel::where('estado_asignacion', 'Aceptado')
            ->where('empresa_proveedora', $this->userId)
            ->pluck('id_activo');

        $empresasCuit = \App\Models\ActivosCompartidosModel::where('estado_asignacion', 'Aceptado')
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

    // Para eliminar la delegacion del bien
    public function removerDelegacion($id)
    {
        $compartidos  = ActivosCompartidosModel::where('id_activos_compartidos', $id)
            ->whereNotIn('estado_asignacion', ['Cancelado', 'En Revisión'])
            ->whereNull('fecha_fin')
            ->first();

        $asignaciones = ActivosAsignacionModel::where('id_activo', $compartidos->id_activo)
            ->where('estado_asignacion', '!=', 'Cancelado')
            ->whereNull('fecha_fin_asignacion')
            ->where('empresa_empleados', $compartidos->empresa_proveedora)
            ->first();

        $activo = ActivosModel::where('id_activo', $compartidos->id_activo)->first();

        $apoderadoEmpresa = UsuariosEmpresasModel::where('cuit', $activo->empresa_titular)
            ->where('cargo', 'Apoderado')
            ->where('estado', 'Aceptado')
            ->pluck('id_usuario')
            ->first();

        if ($compartidos && $asignaciones) {
            // Actualizar los registros de ActivosCompartidosModel
            $compartidos->update([
                'estado_asignacion' => 'Cancelado',
                'fecha_fin' => Carbon::parse($this->fecha_fin)->format('Y-m-d'),
            ]);

            // Actualizar los registros de ActivosAsignacionModel
            $asignaciones->update([
                'estado_asignacion' => 'Cancelado',
                'fecha_fin_asignacion' => Carbon::parse($this->fecha_fin_asignacion)->format('Y-m-d H:i:s'),
            ]);


            // Verificar si el activo está compartido con otras empresas
            $activoCompartidoConOtrasEmpresas = ActivosCompartidosModel::where('id_activo', $activo->id_activo)
                ->where('estado_asignacion', '!=', 'Cancelado')
                ->exists();

            if (!$activoCompartidoConOtrasEmpresas) {
                ActivosAsignacionModel::create([
                    'id_activo' => $activo->id_activo,
                    'id_tipo' => $activo->id_tipo,
                    'id_categoria' => $activo->id_categoria,
                    'id_subcategoria' => $activo->id_subcategoria,
                    'asignado_a' => null,
                    'gestionado_por' => $apoderadoEmpresa,
                    'fecha_asignacion' => Carbon::parse($this->fecha_asignacion)->format('Y-m-d H:i:s'),
                    'responsable' => $apoderadoEmpresa,
                    'empresa_empleados' => $activo->empresa_titular,
                    'estado_asignacion' => 'Aceptado'
                ]);
            }

            $this->dispatch('Exito', [
                'title'   => 'Cancelacion de delegacion',
                'message' => 'El bien dejó de estar delegado.'
            ]);
        }
        $this->dispatch('refreshLivewireTable'); // Refrescar la tabla
    }

    public function crearActivos()
    {
        $this->dispatch('createActivos', origen: $this->origen)->to('activos.create-activos');
    }

    // NO BORRAR ESTA LINEA 
    public function refreshTable()
    {
        $this->dispatch('refreshLivewireTable'); // Refrescar la tabla
    }

    protected function getTipos()
    {
        $tiposConCategorias = ActivosModel::whereHas('compartidos', function ($query) {
            $query->whereNull('fecha_fin')
                ->where('estado_asignacion', 'Aceptado')
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
                ->where('estado_asignacion', 'Aceptado')
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
                ->where('estado_asignacion', 'Aceptado')
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
