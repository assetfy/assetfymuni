<?php

namespace App\Livewire\Menus;

use RamonRietdijk\LivewireTables\Livewire\LivewireTable;
use RamonRietdijk\LivewireTables\Filters\SelectFilter;
use RamonRietdijk\LivewireTables\Columns\ImageColumn;
use RamonRietdijk\LivewireTables\Columns\Column;
use RamonRietdijk\LivewireTables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Enumerable;
use App\Exports\ActivosExport;
use App\Models\ActivosModel;
use App\Helpers\IdHelper;
use App\Helpers\RouteAttributesHelper;
use Illuminate\Support\Facades\Cache;
use App\Models\ActivosCompartidosModel;
use App\Services\ServicioContextoUsuario;
use Carbon\Carbon;
use App\Helpers\Funciones;
use Illuminate\Database\Eloquent\Collection;
use App\Livewire\Qrs\PrintQr;
use App\Livewire\Qrs\PrintQrModal;

class Tablas extends LivewireTable
{
    protected string $model = ActivosModel::class;
    // marca si ya lanzamos el modal como una bandera 
    //Declara la propiedad en la clase para los datos del servicio 
    protected ServicioContextoUsuario $servicioContextoUsuario;
    public bool $initialized = false;


    public string $title               = 'Bienes';
    public string $createForm          = 'crearActivos';

    public int    $userId              = 0;
    public int    $user                = 0;
    public bool   $esApoderado         = false;
    public bool   $esAdmin             = false;
    public array  $atributos           = [];
    public array  $activosCompartidosIds = [];
    protected bool $modalDispatched = false;
    protected bool $garantiasActualizadas = false;
    protected ?bool $memoPuedeActualizar = null;

    protected $listeners = ['refreshBienes' => 'refreshTable'];
    // antes de que livewire renderice o hidrate el componente, ya estara disponible la instancia cacheada del servicio en $this->servicioContextoUsuario

    public function asignar(): void
    {
        if ($this->userId !== 0) {
            return;
        }

        $this->userId = IdHelper::empresaActual()->cuit;
        $this->user   = IdHelper::usuarioEmpresaActual()->id_usuario;
        $this->esApoderado = $this->servicioContextoUsuario->esApoderado();
        $this->esAdmin     = $this->servicioContextoUsuario->esAdminEmpresa();

        $this->activosCompartidosIds = ActivosCompartidosModel::where('empresa_titular', $this->userId)
            ->whereIn('estado_asignacion', ['En Revisión', 'Aceptado'])
            ->pluck('id_activo')
            ->toArray();

        $this->atributos = RouteAttributesHelper::getRouteAttributes() ?: [];
    }

    //probe el metodo este, ocurre antes de la deserialización y la bandera me permite que se cargue una ves, aunque creo que podria funcionar solo con la bandera 


    public function boot(ServicioContextoUsuario $servicioContextoUsuario): void
    {
        $this->servicioContextoUsuario = $servicioContextoUsuario;

        if (!$this->initialized) {
            // Esto sólo corre una vez, en la primera petición
            $this->asignar();
            $this->initialized = true;
        }
    }

    public function hydrate(): void
    {
        if (!$this->modalDispatched) {
            $this->dispatch('openModal', [
                'activos.create-activos',
                'activos.edit-activos',
                'activos.edicion-masiva',
                'ubicaciones.crear-ubicaciones',
                'activos.asignacion-activos',
                'activos.correos',
                'qrs.print-qr',
            ]);
            $this->modalDispatched = true;
        }
    }


    protected function query(): Builder
    {
        if (! $this->garantiasActualizadas) {
            $this->model()->actualizarGarantias();
            $this->garantiasActualizadas = true;
        }

        $base = $this->model()
            ->select([
                'id_activo',
                'nombre',
                'id_ubicacion',
                'id_estado_sit_general',
                'usuario_titular',
                'empresa_titular',
                'fecha_creacion'
            ])
            ->with([
                'fotoPortada',
                'ubicacion:id_ubicacion,nombre',
                'estadoGeneral:id_estado_sit_general,nombre',
                'estadoAlta',
                'usuarioAsignado:id,name',
                'responsableAsignado:id,name',
            ])
            ->withCount([
                'asignaciones as es_gestor_count' => function ($q) {
                    $q->where('empresa_empleados', $this->userId)
                        ->where('estado_asignacion', '!=', 'Cancelado')
                        ->whereNull('fecha_fin_asignacion')
                        ->where('gestionado_por', auth()->id());
                },
                'asignaciones as es_asignado_count' => function ($q) {
                    $q->where('empresa_empleados', $this->userId)
                        ->where('estado_asignacion', '!=', 'Cancelado')
                        ->whereNull('fecha_fin_asignacion')
                        ->where('asignado_a', auth()->id());
                },
                'asignaciones as es_responsable_count' => function ($q) {
                    $q->where('empresa_empleados', $this->userId)
                        ->where('estado_asignacion', '!=', 'Cancelado')
                        ->whereNull('fecha_fin_asignacion')
                        ->where('responsable', auth()->id());
                },
            ])
            ->where(function ($q) {
                $q->where('usuario_titular', $this->userId)
                    ->orWhere('empresa_titular', $this->userId);
            });

        if ($this->esPanelValido()) {
            if (! empty($this->activosCompartidosIds)) {
                $base->whereNotIn('id_activo', $this->activosCompartidosIds);
            }

            if (! $this->esApoderado && ! $this->esAdmin) {
                $base->whereHas('asignaciones', function ($q) {
                    $q->where('empresa_empleados', $this->userId)
                        ->where('estado_asignacion', 'Aceptado')
                        ->whereNull('fecha_fin_asignacion')
                        ->where(function ($sub) {
                            $sub->where('responsable', auth()->id())
                                ->orWhere('asignado_a', auth()->id());
                        });
                });
            }
        }

        if (! empty($this->atributos)) {
            $valores = array_filter($this->atributos);
            $table   = $this->model()->getTable();

            $base->where(function ($q) use ($valores, $table) {
                $q->whereIn("{$table}.id_categoria", $valores)
                    ->orWhereIn("{$table}.id_subcategoria", $valores)
                    ->orWhereIn("{$table}.id_tipo", $valores);
            });
        }

        // Filtro por estado general (sin N+1)
        $filtro = $this->filters['id_estado_sit_general'] ?? null;
        $base->whereHas('estadoGeneral', function ($q) use ($filtro) {
            if ($filtro) {
                $q->where('id_estado_sit_general', $filtro);
            } else {
                $q->whereNotIn('id_estado_sit_general', Funciones::activosAmbos());
            }
        });

        return $base->orderBy('fecha_creacion', 'desc');
    }


    protected function filtro(Builder $query): Builder
    {
        $filtro = $this->filters['id_estado_sit_general'] ?? null;

        $query->whereHas('estadoGeneral', function ($q) use ($filtro) {
            if ($filtro) {
                $q->where('id_estado_sit_general', $filtro);
            } else {
                $q->whereNotIn('id_estado_sit_general', Funciones::activosAmbos());
            }
        });

        return $query->orderBy('fecha_creacion', 'desc');
    }

    protected function columns(): array
    {
        return [

            ImageColumn::make(__('Imagen'), 'fotoPortada.ruta_imagen')->size(75, 75),

            Column::make(__('Acciones'), function (Model $model): string {
                $gestor      = ($model->es_gestor_count ?? 0) > 0;
                $asignado    = ($model->es_asignado_count ?? 0) > 0;
                $responsable = ($model->es_responsable_count ?? 0) > 0;

                $isDisabled  = optional($model->estadoGeneral)->nombre === 'Baja';

                // Botones
                $btnAsignar = '<button wire:click="$dispatch(\'asignarActivo\', { data: '
                    . $model->getKey() . ' })" '
                    . 'style="background-color: #BFDBFE;" '
                    . 'class="text-blue-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12'
                    . ($isDisabled ? ' opacity-50 cursor-not-allowed' : '') . '" '
                    . ($isDisabled ? 'disabled' : '') . '>'
                    . '<i class="fa-solid fa-user-plus text-base"></i>'
                    . '</button>';

                $btnConcierge = '<a href="/servicios/servicios-realizados/' . $model->id_activo . '" '
                    . 'style="background-color: #71d9ecff;" '
                    . 'class="text-black-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12'
                    . ($isDisabled ? ' opacity-50 cursor-not-allowed' : '') . '" title="Servicios">'
                    . '<i class="fa-solid fa-concierge-bell text-base"></i>'
                    . '</a>';

                $btnSolicitar = '<button wire:click="$dispatch(\'seleccionarSolicitud\', { id: '
                    . $model->getKey() . ' })" '
                    . 'style="background-color: #BBF7D0;" '
                    . 'class="text-green-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12'
                    . ($isDisabled ? ' opacity-50 cursor-not-allowed' : '') . '" '
                    . 'title="Seleccionar tipo de solicitud">'
                    . '<i class="fa-solid fa-clipboard-list text-base"></i>'
                    . '</button>';

                $btnDet = '<button wire:click="$dispatch(\'editActivos\', { data: '
                    . $model->getKey() . ' })" '
                    . 'style="background-color: #E5E7EB;" '
                    . 'class="text-gray-800 font-bold py-2 px-4 rounded flex items-center justify-center w-12 h-12" '
                    . 'title="Detalle">'
                    . '<i class="fa-solid fa-eye text-base"></i>'
                    . '</button>';

                if ($gestor && $asignado) {
                    $html = "<div class=\"flex space-x-2\">{$btnAsignar}{$btnDet}</div>";
                } elseif ($gestor && $responsable) {
                    $html = "<div class=\"flex space-x-2\">{$btnAsignar}{$btnConcierge}{$btnSolicitar}{$btnDet}</div>";
                } elseif ($gestor) {
                    $html = "<div class=\"flex space-x-2\">{$btnAsignar}{$btnDet}</div>";
                } elseif ($asignado) {
                    $html = "<div class=\"flex space-x-2\">{$btnDet}</div>";
                } else {
                    $html = "<div class=\"flex space-x-2\">{$btnConcierge}{$btnSolicitar}{$btnDet}</div>";
                }

                if ($this->esPanelValido() && $this->esAdmin && ! str_contains($html, 'fa-user-plus')) {
                    $html = str_replace('<div class="flex space-x-2">', '<div class="flex space-x-2">' . $btnAsignar, $html);
                }

                return $html;
            })->clickable(false)->asHtml(),

            Column::make(__('Creado'), function (Model $model): string {
                $fecha = $model->fecha_creacion;
                return $fecha
                    ? Carbon::parse($fecha)->setTimezone('America/Argentina/Buenos_Aires')->format('Y-m-d')
                    : 'No hay datos';
            })->sortable()->searchable(),

            // ✅ Sin reconsultar el activo
            Column::make(__('Nombre'), function (Model $model): string {
                $faltaInfo = empty($model->id_ubicacion);

                $mensajeFaltante = $faltaInfo ? 'Falta: Ubicacion' : '';
                $icono = $faltaInfo
                    ? '<span class="text-red-500 ml-2 text-2xl" title="' . e($mensajeFaltante) . '">
                       <i class="fa-solid fa-exclamation-circle"></i>
                   </span>'
                    : '';

                return '<span class="' . ($faltaInfo ? 'text-red-500 font-bold' : '') . '">' . e($model->nombre) . '</span>' . $icono;
            })->sortable()->searchable()->asHtml(),

            Column::make(__('Ubicación'), 'ubicacion.nombre')->sortable()->searchable(),
            Column::make(__('Estado General'), 'estadoGeneral.nombre')->sortable()->searchable(),

            ...($this->esPanelValido()
                ? [
                    Column::make(__('Responsable'), 'responsableAsignado.name')->searchable(),
                    Column::make(__('Usuario asignado'), 'usuarioAsignado.name')->searchable(),
                ]
                : []
            ),
        ];
    }

    protected function filters(): array
    {
        $tipoSel = $this->filters['id_tipo'] ?? '';
        $catSel  = $this->filters['id_categoria'] ?? '';

        $activos = $this->getActivosForFilters();

        // Plucks ya en memoria, sin tocar la BD de nuevo
        $responsables = $activos
            ->pluck('responsableAsignado.name', 'responsableAsignado.id')
            ->filter() // elimina nulls
            ->unique()
            ->toArray();

        $usuarios = $activos
            ->pluck('usuarioAsignado.name', 'usuarioAsignado.id')
            ->filter()
            ->unique()
            ->toArray();

        return [
            SelectFilter::make(__('Tipo'), 'id_tipo')
                ->options(
                    Cache::remember(
                        "tablas_tipos_{$this->userId}",  // 1. Clave única de caché, el id de usuario para que se sepa si la cache pertenece al usuario que ve la tabla 
                        now()->addMinutes(15), // 2. Tiempo de vida (TTL), 15 minutos para probar 
                        fn() => $this->getTipos()  // 3. Callback que devuelve los datos, obteneos los datos de la tabla
                    )
                ),

            // La clave de categorías incluye el tipo seleccionado
            SelectFilter::make(__('Categoría'), 'id_categoria')
                ->options(
                    Cache::remember(
                        "tablas_categorias_{$this->userId}_tipo_{$tipoSel}", // lo mismo que arriba solo que le pasamos el tipo selecionado, con la variable declarada arriba
                        now()->addMinutes(15),
                        fn() => $this->getCategorias()
                    )
                ),

            // La clave de subcategorías incluye la categoría seleccionada
            SelectFilter::make(__('Subcategoría'), 'id_subcategoria')
                ->options(
                    Cache::remember(
                        "tablas_subcategorias_{$this->userId}_cat_{$catSel}",
                        now()->addMinutes(15),
                        fn() => $this->getSubcategorias()
                    )
                ),

            // Para Estado y Ubicación, si no dependen de otro filtro, podemos mantener la clave fija
            SelectFilter::make(__('Estado'), 'id_estado_sit_general')
                ->options(
                    Cache::remember(
                        "tablas_estados_{$this->userId}",
                        now()->addMinutes(15),
                        fn() => $this->getEstados()
                    )
                ),
            SelectFilter::make(__('Ubicación'), 'id_ubicacion')
                ->options(
                    Cache::remember(
                        "tablas_ubicaciones_{$this->userId}",
                        now()->addMinutes(15),
                        fn() => $this->getUbicaciones()
                    )
                ),
            SelectFilter::make(__('Responsable'), 'responsableAsignado.id')
                ->options($responsables),

            SelectFilter::make(__('Usuario asignado'), 'usuarioAsignado.id')
                ->options($usuarios),
        ];
    }

    protected function getActivosForFilters()
    {
        return Cache::remember(
            "activos_filtros_min_{$this->userId}",
            now()->addMinutes(10),
            function () {
                $q = ActivosModel::query()
                    ->select('id_activo')
                    ->where(
                        fn($q) => $q
                            ->where('usuario_titular', $this->userId)
                            ->orWhere('empresa_titular', $this->userId)
                    )
                    ->with([
                        'responsableAsignado:id,name',
                        'usuarioAsignado:id,name',
                    ]);

                if ($this->esPanelValido() && !empty($this->activosCompartidosIds)) {
                    $q->whereNotIn('id_activo', $this->activosCompartidosIds);
                }

                // No counts, no otras relaciones
                return $q->get();
            }
        );
    }

    protected function getTipos(): array
    {
        return ActivosModel::query()
            ->select('id_tipo')
            ->whereHas(
                'asignaciones',
                fn($q) => $q
                    ->whereNull('fecha_fin_asignacion')
                    ->where('estado_asignacion', 'Aceptado')
                    ->where('empresa_empleados', IdHelper::idEmpresa())
            )
            ->whereNot('id_estado_sit_general', Funciones::activoService())
            ->whereNotNull('id_tipo')
            ->distinct()
            ->with(['tipo:id_tipo,nombre'])
            ->get()
            ->mapWithKeys(fn($a) => [$a->tipo->id_tipo ?? null => $a->tipo->nombre ?? null])
            ->filter()
            ->toArray();
    }

    protected function getCategorias(?string $tipoSel = null): array
    {
        $q = ActivosModel::query()
            ->select('id_categoria', 'id_tipo')
            ->whereHas(
                'asignaciones',
                fn($q) => $q
                    ->whereNull('fecha_fin_asignacion')
                    ->where('estado_asignacion', 'Aceptado')
                    ->where('empresa_empleados', IdHelper::idEmpresa())
            )
            ->whereNot('id_estado_sit_general', Funciones::activoService())
            ->whereNotNull('id_categoria');

        if ($tipoSel) $q->where('id_tipo', $tipoSel);

        return $q->distinct()
            ->with(['categoria:id_categoria,nombre'])
            ->get()
            ->mapWithKeys(fn($a) => [$a->categoria->id_categoria ?? null => $a->categoria->nombre ?? null])
            ->filter()
            ->toArray();
    }

    protected function getSubcategorias(?string $catSel = null): array
    {
        $q = ActivosModel::query()
            ->select('id_subcategoria', 'id_categoria')
            ->whereHas(
                'asignaciones',
                fn($q) => $q
                    ->whereNull('fecha_fin_asignacion')
                    ->where('estado_asignacion', 'Aceptado')
                    ->where('empresa_empleados', IdHelper::idEmpresa())
            )
            ->whereNot('id_estado_sit_general', Funciones::activoService())
            ->whereNotNull('id_subcategoria');

        if ($catSel) $q->where('id_categoria', $catSel);

        return $q->distinct()
            ->with(['subcategoria:id_subcategoria,nombre'])
            ->get()
            ->mapWithKeys(fn($a) => [$a->subcategoria->id_subcategoria ?? null => $a->subcategoria->nombre ?? null])
            ->filter()
            ->toArray();
    }


    protected function getUbicaciones(): array
    {
        return ActivosModel::where(
            fn($q) => $q
                ->where('usuario_titular', $this->userId)
                ->orWhere('empresa_titular', $this->userId)
        )
            ->whereNot('id_estado_sit_general', Funciones::activoBaja())
            ->has('ubicacion')
            ->with('ubicacion')
            ->get()
            ->pluck('ubicacion.nombre', 'ubicacion.id_ubicacion')
            ->toArray();
    }

    protected function getEstados(): array
    {
        return ActivosModel::where(
            fn($q) => $q
                ->where('usuario_titular', $this->userId)
                ->orWhere('empresa_titular', $this->userId)
        )
            ->has('estadoGeneral')
            ->with('estadoGeneral')
            ->get()
            ->pluck('estadoGeneral.nombre', 'estadoGeneral.id_estado_sit_general')
            ->toArray();
    }

    protected function esPanelValido(): bool
    {
        return in_array(auth()->user()->panel_actual, ['Empresa', 'Prestadora']);
    }

    protected function esGestorEn(Model $model): bool
    {
        return $model->asignaciones()
            ->where('gestionado_por', auth()->id())
            ->where('empresa_empleados', $this->userId)
            ->where('estado_asignacion', '!=', 'Cancelado')
            ->whereNull('fecha_fin_asignacion')
            ->exists();
    }

    protected function esAsignadoEn(Model $model): bool
    {
        return $model->asignaciones()
            ->where('asignado_a', auth()->id())
            ->where('empresa_empleados', $this->userId)
            ->where('estado_asignacion', '!=', 'Cancelado')
            ->whereNull('fecha_fin_asignacion')
            ->exists();
    }

    protected function esResponsableEn(Model $model): bool
    {
        return $model->asignaciones()
            ->where('responsable', auth()->id())
            ->where('empresa_empleados', $this->userId)
            ->where('estado_asignacion', '!=', 'Cancelado')
            ->whereNull('fecha_fin_asignacion')
            ->exists();
    }

    public function crearActivos(): void
    {
        $this->dispatch('createActivos');
    }

    public function refreshTable(): void
    {
        $this->dispatch('refreshLivewireTable');
    }

    protected function puedeActualizar(): bool
    {
        if ($this->memoPuedeActualizar !== null) return $this->memoPuedeActualizar;

        if (! $this->esPanelValido()) return $this->memoPuedeActualizar = false;
        if ($this->esApoderado || $this->esAdmin) return $this->memoPuedeActualizar = true;

        $existe = ActivosModel::query()
            ->whereHas('asignaciones', function ($q) {
                $q->where('empresa_empleados', $this->userId)
                    ->where('estado_asignacion', '!=', 'Cancelado')
                    ->whereNull('fecha_fin_asignacion')
                    ->where(function ($s) {
                        $s->where('responsable', auth()->id())
                            ->orWhere('asignado_a', auth()->id())
                            ->orWhere('gestionado_por', auth()->id());
                    });
            })
            ->limit(1)
            ->exists();

        return $this->memoPuedeActualizar = $existe;
    }

    protected function actions(): array
    {
        // 1️⃣ Botón de exportar, siempre disponible
        $actions = [
            Action::make(
                __('Exportar'),
                'export',
                fn(Enumerable $models) =>
                Excel::download(new ActivosExport($models), 'activos.xlsx')
            ),
        ];

        // 👉 Nueva acción: Imprimir QR
        $actions[] =    Action::make(__('Imprimir QR'), 'print_qr', function (Enumerable $models) {
            $this->impresionMasiva($models->pluck('id_activo')->all());
        });

        // 2️⃣ Botón de cambio masivo, sólo si puede
        if ($this->PuedeActualizar()) {
            $actions[] = Action::make(__('Cambio Masivo'), 'prueba', function (Enumerable $models) {
                $this->procesarSeleccionados($models->pluck('id_activo')->all());
            });
        }
        return $actions;
    }

    public function impresionMasiva(array $ids): void
    {
        $this->dispatch('open-qr-modal', $ids)->to(\App\Livewire\Qrs\PrintQr::class);
    }

    public function procesarSeleccionados(array $ids): void
    {
        $this->dispatch('editMasivo', ['ids' => $ids]);
    }
}
