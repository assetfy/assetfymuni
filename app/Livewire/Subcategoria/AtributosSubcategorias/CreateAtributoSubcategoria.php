<?php

namespace App\Livewire\Subcategoria\AtributosSubcategorias;

use App\Models\AtributosSubcategoriaModel;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\DB;
use App\Models\SubcategoriaModel;
use App\Traits\VerificacionTrait;
use App\Models\CategoriaModel;
use App\Models\AtributosModel;
use Livewire\WithFileUploads;
use App\Models\TiposModel;
use Livewire\Component;

class CreateAtributoSubcategoria extends Component
{
    use VerificacionTrait;
    use WithFileUploads;

    public $open = false;
    public $unico, $obligatorio_carga_ini, $obligatorio_carga_inicial;
    public $id_atributo, $id_tipo, $id_categoria, $id_subcategoria;
    public $obligatorio;
    // Propiedades para manejar los selects dependientes
    public $searchTipo, $searchCategoria, $searchSubcategoria, $searchAtributo;
    public $tiposCargados, $categoriasCargadas, $subcategoriasCargadas, $atributosCargados;
    public $selectedTipoNombre, $selectedCategoriaNombre, $selectedSubcategoriaNombre, $selectedAtributoNombre;
    // Listeners para eventos de Livewire
    protected $listeners = ['crearAtributoSubcategoria'];
    // Reglas de validación
    protected $rules = [
        'unico' => 'required',
        'obligatorio_carga_ini' => 'required',
        'id_atributo' => 'required_if:obligatorio,No',
        'id_tipo' => 'required',
        'id_categoria' => 'required',
        'id_subcategoria' => 'required_if:obligatorio,No',
    ];

    public function save()
    {
        // Validar los datos del formulario según las reglas definidas
        $this->validate();
        // Registrar e nuevo Atributo-Subcategoría
        $this->registro();
    }

    public function mount()
    {
        $this->tiposCargados = TiposModel::all();
        $this->atributosCargados = AtributosModel::all(); // Cargar Atributos
    }

    protected function registro()
    {
        DB::beginTransaction();
        try {
            // Crear el nuevo Atributo-Subcategoría
            AtributosSubcategoriaModel::create([
                'unico' => $this->unico,
                'obligatorio_carga_ini' => $this->obligatorio_carga_ini,
                'id_atributo' => $this->id_atributo,
                'id_tipo' => $this->id_tipo,
                'id_categoria' => $this->id_categoria,
                'id_subcategoria' => $this->id_subcategoria,
            ]);

            DB::commit();

            // Emitir evento para refrescar la tabla si es necesario
            $this->dispatch('refreshLivewireTable');
            $this->dispatch('lucky');

            // Emitir evento de éxito
            $this->dispatch('Exito', [
                'title' => 'Registro Exitoso',
                'message' => 'El atributo-subcategoría se ha creado correctamente.'
            ]);

            // Cerrar el modal y resetear las propiedades
            $this->close();
        } catch (\Exception $e) {
            DB::rollBack();
            // Emitir evento de error
            $this->dispatch('errorInfo', [
                'title' => 'Error en el registro',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function setTipo($id)
    {
        $tipo = TiposModel::find($id);
        $this->selectedTipoNombre = $tipo ? $tipo->nombre : null;
        $this->id_tipo = $id;
        $this->searchTipo = '';
        $this->cargarCategorias($id); // Cargar Categorías relacionadas
        $this->dispatch('closeDropdown', ['dropdown' => 'tipo']);
    }

    public function cargarCategorias($id)
    {
        if ($id) {
            $this->categoriasCargadas = CategoriaModel::where('id_tipo', $id)->get();
            $this->id_categoria = null; // Limpiar la selección actual
            $this->subcategoriasCargadas = collect(); // Limpiar Subcategorias
            $this->id_subcategoria = null;
        } else {
            $this->categoriasCargadas = collect();
            $this->id_categoria = null;
            $this->subcategoriasCargadas = collect();
            $this->id_subcategoria = null;
        }
    }

    public function updatedSearchTipo()
    {
        if ($this->searchTipo) {
            $this->tiposCargados = TiposModel::where('nombre', 'like', '%' . $this->searchTipo . '%')->get();
        } else {
            $this->tiposCargados = TiposModel::all();
        }
        // Si la búsqueda no coincide con el Tipo seleccionado, resetea la selección
        if ($this->tiposCargados->isEmpty() || !$this->tiposCargados->contains('id_tipo', $this->id_tipo)) {
            $this->id_tipo = null;
            $this->categoriasCargadas = collect();
            $this->id_categoria = null;
            $this->subcategoriasCargadas = collect();
            $this->id_subcategoria = null;
        }
    }

    public function setCategoria($id)
    {
        $categoria = CategoriaModel::find($id);
        $this->selectedCategoriaNombre = $categoria ? $categoria->nombre : null;
        $this->id_categoria = $id;
        $this->searchCategoria = ''; // Limpiar el campo de búsqueda
        $this->cargarSubcategorias($id); // Cargar Subcategorías relacionadas
        $this->dispatch('closeDropdown', ['dropdown' => 'categoria']);
    }

    public function cargarSubcategorias($id)
    {
        if ($id) {
            $this->subcategoriasCargadas = SubcategoriaModel::where('id_categoria', $id)->get();
            $this->id_subcategoria = null; // Limpiar la selección actual
            if ($this->subcategoriasCargadas->isNotEmpty()) {
                $this->id_subcategoria = null;
            }
        } else {
            $this->subcategoriasCargadas = collect();
            $this->id_subcategoria = null;
        }
    }

    public function updatedSearchCategoria()
    {
        if ($this->id_tipo && $this->searchCategoria) {
            $this->categoriasCargadas = CategoriaModel::where('id_tipo', $this->id_tipo)
                ->where('nombre', 'like', '%' . $this->searchCategoria . '%')
                ->get();
        } elseif ($this->id_tipo) {
            $this->categoriasCargadas = CategoriaModel::where('id_tipo', $this->id_tipo)->get();
        } else {
            $this->categoriasCargadas = collect();
        }
        // Si la búsqueda no coincide con la Categoría seleccionada, resetea la selección
        if ($this->categoriasCargadas->isEmpty() || !$this->categoriasCargadas->contains('id_categoria', $this->id_categoria)) {
            $this->id_categoria = null;
            $this->subcategoriasCargadas = collect();
            $this->id_subcategoria = null;
        }
    }

    public function updatedSearchSubcategoria()
    {
        if ($this->id_categoria && $this->searchSubcategoria) {
            $this->subcategoriasCargadas = SubcategoriaModel::where('id_categoria', $this->id_categoria)
                ->where('nombre', 'like', '%' . $this->searchSubcategoria . '%')
                ->get();
        } elseif ($this->id_categoria) {
            $this->subcategoriasCargadas = SubcategoriaModel::where('id_categoria', $this->id_categoria)->get();
        } else {
            $this->subcategoriasCargadas = collect();
        }
        // Si la búsqueda no coincide con la subcategoría seleccionada, resetea la selección
        if ($this->subcategoriasCargadas->isEmpty() || !$this->subcategoriasCargadas->contains('id_subcategoria', $this->id_subcategoria)) {
            $this->id_subcategoria = null;
        }
    }

    public function updatedSearchAtributo()
    {
        if ($this->searchAtributo) {
            $this->atributosCargados = AtributosModel::where('nombre', 'like', '%' . $this->searchAtributo . '%')->get();
        } else {
            $this->atributosCargados = AtributosModel::all();
        }

        // Si la búsqueda no coincide con el Atributo seleccionado, resetea la selección
        if ($this->atributosCargados->isEmpty() || !$this->atributosCargados->contains('id_atributo', $this->id_atributo)) {
            $this->id_atributo = null;
        }
    }

    public function setAtributo($id)
    {
        $atributo = AtributosModel::find($id);
        $this->selectedAtributoNombre = $atributo ? $atributo->nombre : null;
        $this->id_atributo = $id;
        $this->searchAtributo = ''; // Limpiar el campo de búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'atributo']);
    }

    public function setSubcategoria($id)
    {
        $subcategoria = SubcategoriaModel::find($id);
        $this->selectedSubcategoriaNombre = $subcategoria ? $subcategoria->nombre : null;
        $this->id_subcategoria = $id;
        $this->searchSubcategoria = ''; // Limpiar el campo de búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'subcategoria']);
    }

    public function crearAtributoSubcategoria()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            // Recargar Tipos y Atributos al abrir el modal
            $this->tiposCargados = TiposModel::all();
            $this->atributosCargados = AtributosModel::all();
            $this->open = true;
        }
    }

    public function close()
    {
        $this->reset([
            'unico',
            'obligatorio_carga_ini',
            'obligatorio_carga_inicial',
            'id_atributo',
            'id_tipo',
            'id_categoria',
            'id_subcategoria',
            'obligatorio',
            'searchTipo',
            'searchCategoria',
            'searchSubcategoria',
            'searchAtributo',
            'selectedTipoNombre',
            'selectedCategoriaNombre',
            'selectedSubcategoriaNombre',
            'selectedAtributoNombre',
            'categoriasCargadas',
            'subcategoriasCargadas',
            'atributosCargados',
        ]);
        $this->open = false;
    }

    public function updatedOpen($value)
    {
        if ($value) {
            $this->tiposCargados = TiposModel::all();
            $this->atributosCargados = AtributosModel::all();
        }
    }

    public function render()
    {
        return view('livewire.subcategoria.create-atributo-subcategoria');
    }
}
