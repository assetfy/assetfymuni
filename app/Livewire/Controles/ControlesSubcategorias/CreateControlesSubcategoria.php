<?php

namespace App\Livewire\Controles\ControlesSubcategorias;

use App\Models\ControlesSubcategoriaModel;
use App\Services\MiddlewareInvoker;
use App\Models\SubcategoriaModel;
use App\Traits\VerificacionTrait;
use App\Models\ControlesModel;
use App\Models\CategoriaModel;
use App\Models\TiposModel;
use Livewire\Component;
class CreateControlesSubcategoria extends Component
{
    use VerificacionTrait;
    public $open = false;
    public $unico, $obligatorio_carga_ini, $id_control, $id_categoria, $id_subcategoria,
        $es_periodico, $frecuencia_control, $cantidad_estandar, $req_foto, $id_tipo;
    public $cat = []; // Colección de Categorías
    public $subcat = []; // Colección de Subcategorías
    public $controles;
    public $tipos;
    // Propiedades de búsqueda
    public $searchControl = '';
    public $searchTipo = '';
    public $searchCategoria = '';
    public $searchSubcategoria = '';

    protected $listeners = ['CrearControlesSubcategorias'];

    protected $rules = [
        'id_control' => 'required',
        'unico' => 'required|max:50|min:2',
        'obligatorio_carga_ini' => 'required',
        'id_tipo' => 'required',
        'id_categoria' => 'required',
        'id_subcategoria' => 'required',
        'es_periodico' => 'required',
        'frecuencia_control' => 'required|numeric',
        'cantidad_estandar' => 'required|numeric',
        'req_foto' => 'required'
    ];

    public function mount()
    {
        $this->controles = ControlesModel::all();
        $this->tipos = TiposModel::all();
        $this->cat = collect();
        $this->subcat = collect();
    }

    public function save()
    {
        $this->validate();

        ControlesSubcategoriaModel::create([
            'id_control' => $this->id_control,
            'unico' => $this->unico,
            'obligatorio_carga_ini' => $this->obligatorio_carga_ini,
            'id_tipo' => $this->id_tipo,
            'id_categoria' => $this->id_categoria,
            'id_subcategoria' => $this->id_subcategoria,
            'es_periodico' => $this->es_periodico,
            'frecuencia_control' => $this->frecuencia_control,
            'cantidad_estandar' => $this->cantidad_estandar,
            'req_foto' => $this->req_foto
        ]);

        $this->dispatch('lucky');
        // Emitir evento para refrescar la tabla si estás usando Livewire Tables u otro componente
        $this->dispatch('refreshLivewireTable');
        // Cerrar el modal y resetear el formulario
        $this->close();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.controles.controlessubcategorias.create-controles-subcategoria');
    }

    public function CrearControlesSubcategorias()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->resetForm();
            $this->open = true;
        }
    }

    public function setControl($id)
    {
        $control = ControlesModel::find($id);
        if ($control) {
            $this->id_control = $id;
            $this->searchControl = '';
        }
    }

    public function setTipo($id)
    {
        $tipo = TiposModel::find($id);
        if ($tipo) {
            $this->id_tipo = $id;
            $this->searchTipo = '';
            $this->cargarCategoria($id);

            // Resetear Categoría y Subcategoría
            $this->reset(['id_categoria', 'id_subcategoria', 'subcat', 'searchCategoria', 'searchSubcategoria']);
        }

        $this->dispatch('closeDropdown', ['dropdown' => 'tipo']);
    }

    public function cargarCategoria($id)
    {
        if ($id) {
            $this->cat = CategoriaModel::where('id_tipo', $id)->get();
            $this->id_categoria = null; // Limpiar la selección actual
        } else {
            $this->cat = collect();
            $this->id_categoria = null;
        }
    }

    public function setCategoria($id)
    {
        $categoria = CategoriaModel::find($id);
        if ($categoria) {
            $this->id_categoria = $id;
            $this->searchCategoria = '';
            $this->cargaSubcategoria($id);

            // Resetear Subcategoría
            $this->reset(['id_subcategoria', 'searchSubcategoria']);
        }

        $this->dispatch('closeDropdown', ['dropdown' => 'categoria']);
    }

    public function cargaSubcategoria($id)
    {
        if ($id) {
            $this->subcat = SubcategoriaModel::where('id_categoria', $id)->get();
            $this->id_subcategoria = null; // Limpiar la selección actual
        } else {
            $this->subcat = collect();
            $this->id_subcategoria = null;
        }
    }
    // Métodos de búsqueda
    public function updatedSearchControl()
    {
        if ($this->searchControl) {
            $this->controles = ControlesModel::where('nombre', 'like', '%' . $this->searchControl . '%')->get();
        } else {
            $this->controles = ControlesModel::all();
        }
    }

    public function updatedSearchTipo()
    {
        if ($this->searchTipo) {
            $this->tipos = TiposModel::where('nombre', 'like', '%' . $this->searchTipo . '%')->get();
        } else {
            $this->tipos = TiposModel::all();
        }

        // Si la búsqueda no coincide con el Tipo seleccionado, resetea la selección
        if ($this->tipos->isEmpty()) {
            $this->reset(['id_tipo', 'cat', 'id_categoria', 'subcat', 'id_subcategoria', 'searchCategoria', 'searchSubcategoria']);
        }
    }

    public function updatedSearchCategoria()
    {
        if ($this->id_tipo && $this->searchCategoria) {
            $this->cat = CategoriaModel::where('id_tipo', $this->id_tipo)
                ->where('nombre', 'like', '%' . $this->searchCategoria . '%')
                ->get();
        } elseif ($this->id_tipo) {
            $this->cat = CategoriaModel::where('id_tipo', $this->id_tipo)->get();
        } else {
            $this->cat = collect();
        }
    }

    public function setSubcategoria($id)
    {
        $subcategoria = SubcategoriaModel::find($id);
        if ($subcategoria) {
            $this->id_subcategoria = $id;
            $this->searchSubcategoria = '';
        }

        $this->dispatch('closeDropdown', ['dropdown' => 'subcategoria']);
    }

    public function updatedSearchSubcategoria()
    {
        if ($this->id_categoria && $this->searchSubcategoria) {
            $this->subcat = SubcategoriaModel::where('id_categoria', $this->id_categoria)
                ->where('nombre', 'like', '%' . $this->searchSubcategoria . '%')
                ->get();
        } elseif ($this->id_categoria) {
            $this->subcat = SubcategoriaModel::where('id_categoria', $this->id_categoria)->get();
        } else {
            $this->subcat = collect();
        }
    }

    public function close()
    {
        $this->resetForm();
        $this->open = false;
    }

    private function resetForm()
    {
        $this->reset([
            'id_control',
            'unico',
            'obligatorio_carga_ini',
            'id_tipo',
            'id_categoria',
            'id_subcategoria',
            'es_periodico',
            'frecuencia_control',
            'cantidad_estandar',
            'req_foto',
            'cat',
            'subcat',
            'searchControl',
            'searchTipo',
            'searchCategoria',
            'searchSubcategoria'
        ]);
    }
}
