<?php

namespace App\Livewire\Activos\Bienes;

use App\Models\ActivosModel;
use App\Models\TiposModel;
use App\Models\CategoriaModel;
use App\Models\SubCategoriaModel;
use App\Models\EmpresasModel;
use Livewire\Component;
use App\Helpers\IdHelper;
use App\Models\ActivosAsignacionModel;
use App\Models\ActivosCompartidosModel;
use App\Models\MisProveedoresModel;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class DelegarBienes extends Component
{
    public $userId, $activos, $tipos, $categoria, $subcategoria, $empresa, $id;
    public $categorias = [], $subcategorias = [];
    public $selectedAtributos = [], $datoAtributo = [], $campo_numerico = [], $campo = [];
    public $open = false;
    public $id_estado_sit_alta, $id_estado_sit_general, $id_tipo, $id_categoria, $id_subcategoria, $id_ubicacion,
        $comentarios_sit_alta, $estado_inventario, $empresa_titular, $usuario_titular, $ubicacion, $altas, $general, $campos,
        $etiqueta, $numero_serie, $nombre, $tipoAsociado, $imagen, $propietario, $tipo, $categoriasTipos, $valorTipo, $nombreTipo, $ubicaciones,
        $gestionado_por, $asignado_a, $usuarioEmpresa, $empresas, $users, $user, $empleadosLista, $noEmpleadosEncontrados, $responsable, $responsable_id,
        $id_usuario, $selectedEmpleado, $fecha_asignacion, $asignado_a_id, $gestionado_por_id, $cuit_empresa;
    public $searchGestionado, $searchAsignado, $searchResponsable;
    public $fotos = []; // Todas las fotos (subidas y capturadas)
    public $nuevasFotos = []; // Nuevas fotos subidas
    public $capturedPhotos = []; // Fotos capturadas desde la cámara
    // Propiedades para los dropdowns personalizados
    public $selectedTipoNombre, $searchTipo, $tipoPrueba;
    public $selectedCategoriaNombre, $searchCategoria;
    public $categorias2;
    public $selectedSubcategoriaNombre;
    public $searchSubcategoria = '';
    public $selectedUbicacionNombre, $searchUbicacion, $ubicacionesList;
    public $tipoInicial = false; // Indica si el tipo inicial proviene del servidor
    public $sinUbicacion = -1;
    public $activoId;
    public $fecha = [];
    public $fecha_compra;
    public $factura_compra;
    public $garantia_vigente = 'No'; // Valor por defecto
    public $vencimiento_garantia;
    public $atributoDefinid = [];
    public $AtributoMultiple = [];
    public $atributosValores = [];
    public $atributosSeleccionadosValoresCheckboxes = [];
    public $atributosSeleccionadosValoresSelects = [];
    public $proveedores = [];
    public $id_proveedor;
    public $selectedProveedorNombre;
    public $searchProveedor = '';

    protected $listeners = [
        'autorizar' => 'delegarActivos',
    ];

    public function asignar()
    {
        $this->userId = IdHelper::identificador();
        $this->id = auth()->user()->id;
        $this->proveedores = MisProveedoresModel::where('id_usuario', $this->id)->get();
    }

    public function mount()
    {
        $this->asignar();
        $this->tipos = TiposModel::all();
        $this->activos = collect(); // Inicialmente vacío, se llenará con `filtrarActivos`
    }

    public function filtrarActivos()
    {
        // Filtrar activos por los valores seleccionados y la empresa titular
        $query = ActivosModel::query()->where('empresa_titular', $this->userId);

        if ($this->id_subcategoria) {
            $query->where('id_tipo', $this->id_tipo)
                ->where('id_categoria', $this->id_categoria)
                ->where('id_subcategoria', $this->id_subcategoria)
                ->where('id_estado_sit_alta', '!=', 2);
        }

        $this->activos = $query->get();
    }

    // Establece el Tipo seleccionado y carga las Categorías asociadas
    public function setTipo($id)
    {
        $tipo = TiposModel::find($id);
        $this->selectedTipoNombre = $tipo ? $tipo->nombre : null;
        $this->id_tipo = $id;
        Session::put('tipoNombre',  $this->selectedTipoNombre);
        $this->searchTipo = '';
        $this->tipoPrueba = TiposModel::where('nombre', 'like', '%' . $this->searchTipo . '%')->get();
        $this->cargarCategoria($id); // Cargar categorías relacionadas
        $this->dispatch('closeDropdown', ['dropdown' => 'tipo']);
    }

    // Establece la Categoría seleccionada
    public function setCategoria($id)
    {
        $categoria = CategoriaModel::find($id);
        $this->selectedCategoriaNombre = $categoria ? $categoria->nombre : null;
        $this->id_categoria = $id;
        $this->searchCategoria = ''; // Limpiar el campo de búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'categoria']); // Cerrar el dropdown
        // Cargar Subcategorías correspondientes a la Categoría y Tipo seleccionada
        $this->cargaSubcategoria($id);
    }

    // Establece la Subcategoría seleccionada
    public function setSubcategoria($id)
    {
        $subcategoria = SubcategoriaModel::find($id);
        $this->selectedSubcategoriaNombre = $subcategoria ? $subcategoria->nombre : null;
        $this->id_subcategoria = $id;
        $this->searchSubcategoria = '';
        $this->dispatch('closeDropdown', ['dropdown' => 'subcategoria']);
        $this->filtrarActivos();
    }

    // Actualiza la búsqueda de Tipos
    public function updatedSearchTipo()
    {
        if ($this->searchTipo) {
            $this->tipoPrueba = TiposModel::where('nombre', 'like', '%' . $this->searchTipo . '%')->get();
        } else {
            $this->tipoPrueba = TiposModel::all();
        }
        // Si la búsqueda no coincide con el Tipo seleccionado, resetea la selección
        if ($this->tipoPrueba->isEmpty() || !$this->tipoPrueba->contains('id_tipo', $this->id_tipo)) {
            $this->id_tipo = null;
            $this->selectedTipoNombre = null;
            $this->categorias2 = collect();
            $this->id_categoria = null;
            $this->selectedCategoriaNombre = null;
            $this->subcategoria = collect();
            $this->id_subcategoria = null;
        }
    }

    // Actualiza la búsqueda de Categorías
    public function updatedSearchCategoria()
    {
        if ($this->id_tipo && $this->searchCategoria) {
            $this->categorias2 = CategoriaModel::where('id_tipo', $this->id_tipo)
                ->where('nombre', 'like', '%' . $this->searchCategoria . '%')
                ->get();
        } elseif ($this->id_tipo) {
            $this->categorias2 = CategoriaModel::where('id_tipo', $this->id_tipo)->get();
        } else {
            $this->categorias2 = collect();
        }
        // Si la búsqueda no coincide con la Categoría seleccionada, resetea la selección
        if ($this->categorias2->isEmpty() || !$this->categorias2->contains('id_categoria', $this->id_categoria)) {
            $this->id_categoria = null;
            $this->selectedCategoriaNombre = null;
            $this->subcategoria = collect();
            $this->id_subcategoria = null;
        }
    }

    // Actualiza la búsqueda de Subcategorías
    public function updatedSearchSubcategoria()
    {
        if ($this->id_categoria && $this->searchSubcategoria) {
            $this->subcategoria = SubcategoriaModel::where('id_categoria', $this->id_categoria)
                ->where('id_tipo', $this->id_tipo) // Filtrar por Tipo
                ->where('nombre', 'like', '%' . $this->searchSubcategoria . '%')
                ->get();
        } elseif ($this->id_categoria) {
            $this->subcategoria = SubcategoriaModel::where('id_categoria', $this->id_categoria)
                ->where('id_tipo', $this->id_tipo) // Filtrar por Tipo
                ->get();
        } else {
            $this->subcategoria = collect();
        }

        // Si la búsqueda no coincide con la Subcategoría seleccionada, resetea la selección
        if ($this->subcategoria->isEmpty() || !$this->subcategoria->contains('id_subcategoria', $this->id_subcategoria)) {
            $this->id_subcategoria = null;
            $this->selectedSubcategoriaNombre = null;
        }
    }

    // Carga las Categorías asociadas al Tipo seleccionado
    public function cargarCategoria($id)
    {
        if ($id) {
            $this->categorias2 = CategoriaModel::where('id_tipo', $id)->get();
            $this->id_categoria = null; // Limpiar la selección actual sin asignar automáticamente
            $this->selectedCategoriaNombre = null;
            $this->subcategoria = collect(); // Clear subcategory
            $this->id_subcategoria = null;
        } else {
            $this->categorias2 = collect();
            $this->id_categoria = null;
            $this->selectedCategoriaNombre = null;
            $this->subcategoria = collect();
            $this->id_subcategoria = null;
        }
    }

    // Carga las Subcategorías asociadas a la Categoría seleccionada
    public function cargaSubcategoria($value)
    {
        $this->categoria = $value;
        $this->loadSubcategorias($value);
    }

    protected function loadSubcategorias($value)
    {
        $this->categoria = $value;
        $this->subcategoria = collect();
        $this->id_subcategoria = null;
        $this->selectedSubcategoriaNombre = null;
        if ($value) {
            $this->subcategoria = SubcategoriaModel::where('id_categoria', $value)
                ->where('id_tipo', $this->id_tipo)
                ->get();
        }
    }

    public function updatedSearchProveedor()
    {
        $this->proveedores = MisProveedoresModel::where('id_usuario', $this->id)
            ->where('razon_social', 'like', '%' . $this->searchProveedor . '%')
            ->get();
    }

    public function setProveedor($id)
    {
        $this->id_proveedor = $id;
        $this->selectedProveedorNombre = MisProveedoresModel::find($id);
    }
    public $activosSeleccionados = [];

    public function confirmarDelegacion()
    {
        // Verificar si hay activos seleccionados
        if (empty($this->activosSeleccionados)) {
            $this->dispatch('errorInfo', [
                'title'   => 'Error al delegar los bienes',
                'message' => 'No se han seleccionado bienes.'
            ]);
            return;
        }

        // Verificar si no se han seleccionado un proveedor
        if (empty($this->selectedProveedorNombre->cuit)) {
            $this->dispatch('errorInfo', [
                'title'   => 'Error al delegar los bienes',
                'message' => 'No se ha seleccionado un proveedor.'
            ]);
            return;
        }

        // Lanzar el SweetAlert desde Livewire
        $mensaje = "Está a punto de delegar " . count($this->activosSeleccionados) . " bienes.";
        $this->dispatch('delegar', ['message' => $mensaje]);
    }

    public function delegarActivos()
    {

        $activosDelegados = false;  // Variable para saber si al menos un activo fue delegado correctamente

        foreach ($this->activosSeleccionados as $activoId => $seleccionado) {
            if ($seleccionado) {
                $activo = ActivosModel::find($activoId);

                if ($activo) {
                    // Comprobar si ya existe una delegación con los mismos parámetros y con estado "En Revisión" o "Aceptado"
                    $delegacionExistente = ActivosCompartidosModel::where([
                        'id_activo' => $activo->id_activo,
                        'id_subcat' => $activo->id_subcategoria,
                        'id_cat' => $activo->id_categoria,
                        'id_tipo' => $activo->id_tipo,
                        'empresa_titular' => $this->userId,
                        'empresa_proveedora' => $this->selectedProveedorNombre->cuit,
                    ])
                        ->whereIn('estado_asignacion', ['En Revisión', 'Aceptado']) // Verifica ambos estados
                        ->first();

                    if ($delegacionExistente) {
                        // Notificar que el activo ya ha sido delegado o está en revisión
                        $this->dispatch('errorInfo', [
                            'title'   => 'Error al delegar los bienes',
                            'message' => 'El activo "' . $activo->nombre . '" está en revisión o ha sido aceptado por la empresa con CUIT: ' . $this->selectedProveedorNombre->cuit . '.'
                        ]);
                    } else {

                        $asignacionExistente = ActivosAsignacionModel::where('id_activo', $activo->id_activo)
                                                ->where('empresa_empleados', $this->userId)
                                                ->first();
                        
                        if($asignacionExistente)
                        {
                            $asignacionExistente->update([
                                'estado_asignacion' => 'Cancelado',
                                'fecha_fin_asignacion' => Carbon::parse($this->fecha_asignacion)->format('Y-m-d H:i:s'),
                            ]);
                        }

                        // Si no existe, proceder a insertar en la tabla activos_compartidos
                        ActivosCompartidosModel::create([
                            'id_activo' => $activo->id_activo,
                            'id_subcat' => $activo->id_subcategoria,
                            'id_cat' => $activo->id_categoria,
                            'id_tipo' => $activo->id_tipo,
                            'empresa_titular' => $this->userId,
                            'empresa_proveedora' => $this->selectedProveedorNombre->cuit,
                            'estado_asignacion' => 'En Revisión',
                        ]);

                        // Cambiar el estado de "activosDelegados" a true para notificar al final
                        $activosDelegados = true;

                        $this->dispatch('Exito', [
                            'title'   => 'Operación exitosa',
                            'message' => 'Activo "' . $activo->nombre . '" delegado exitosamente.'
                        ]);
                    }
                }
            }
        }

        // Solo enviar mensaje de éxito general si al menos un activo fue delegado correctamente
        if ($activosDelegados) {
            $this->dispatch('delegacionExitosa', [
                'title'   => 'Delegación exitosa',
                'message' => 'Se han delegado los bienes correctamente.'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.activos.bienes.delegar-bienes');
    }
}
