<?php

namespace App\Livewire\Servicios\Subcategorias;

use App\Models\ServiciosSubcategoriasModel;
use App\Services\MiddlewareInvoker;
use App\Models\SubcategoriaModel;
use App\Traits\VerificacionTrait;
use App\Models\CategoriaModel;
use App\Models\ServiciosModel;
use App\Models\TiposModel;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateServiciosSubcategorias extends Component
{
    use VerificacionTrait;

    public $open = false;
    public $unico, $obligatorio_carga_ini, $id_servicio, $id_categoria, $id_subcategoria, $req_fotos_carga_inicial, $id_tipo;
    public $categorias = []; // Colección de Categorías
    public $subcategorias = []; // Colección de Subcategorías
    public $servicios;
    public $tipos;
    // Propiedades de búsqueda
    public $searchServicio = '';
    public $searchTipo = '';
    public $searchCategoria = '';
    public $searchSubcategoria = '';

    protected $listeners = ['CreateServiciosSubcategoria'];

    protected $rules = [
        'id_servicio' => 'required',
        'id_tipo' => 'required',
        'id_categoria' => 'required',
        'id_subcategoria' => 'required',
        'req_fotos_carga_inicial' => 'required'
    ];

    protected $messages = [
        'id_servicio.required' => 'El campo Servicio es obligatorio.',
        'id_tipo.required' => 'El campo Tipo es obligatorio.',
        'id_categoria.required' => 'El campo Categoría es obligatorio.',
        'id_subcategoria.required' => 'El campo Subcategoría es obligatorio.',
        'req_fotos_carga_inicial.required' => 'El campo Requiere Foto es obligatorio.'
    ];

    public function mount()
    {
        $this->servicios = ServiciosModel::all();
        $this->tipos = TiposModel::all();
        $this->categorias = collect();
        $this->subcategorias = collect();
    }

    public function save()
    {
        DB::beginTransaction();

        try {
            ServiciosSubcategoriasModel::create([
                'id_servicio' => $this->id_servicio,
                'id_tipo' => $this->id_tipo,
                'id_categoria' => $this->id_categoria,
                'id_subcategoria' => $this->id_subcategoria,
                'req_fotos_carga_inicial' => $this->req_fotos_carga_inicial,
            ]);

            DB::commit();
            $this->dispatch('lucky');
            $this->dispatch('refreshLivewireTable');

            $this->close();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('errorCreacion');
        }
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.servicios.subcategorias.create-servicios-subcategorias');
    }

    public function CreateServiciosSubcategoria()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->resetForm();
            $this->open = true;
        }
    }

    public function setServicio($id)
    {
        $servicio = ServiciosModel::find($id);
        if ($servicio) {
            $this->id_servicio = $id;
            $this->searchServicio = '';
        }
    }

    public function setTipo($id)
    {
        $tipo = TiposModel::find($id);
        if ($tipo) {
            $this->id_tipo = $id;
            $this->searchTipo = '';
            $this->cargarCategorias($id);
            $this->reset(['id_categoria', 'id_subcategoria', 'subcategorias', 'searchCategoria', 'searchSubcategoria']);
        }

        $this->dispatch('closeDropdown', ['dropdown' => 'tipo']);
    }

    public function cargarCategorias($id)
    {
        if ($id) {
            $this->categorias = CategoriaModel::where('id_tipo', $id)->get();
            $this->id_categoria = null; // Limpiar la selección actual
        } else {
            $this->categorias = collect();
            $this->id_categoria = null;
        }
    }

    public function setCategoria($id)
    {
        $categoria = CategoriaModel::find($id);
        if ($categoria) {
            $this->id_categoria = $id;
            $this->searchCategoria = '';
            $this->cargarSubcategorias($id);
            $this->reset(['id_subcategoria', 'searchSubcategoria']);
        }

        $this->dispatch('closeDropdown', ['dropdown' => 'categoria']);
    }

    public function cargarSubcategorias($id)
    {
        if ($id) {
            $this->subcategorias = SubcategoriaModel::where('id_categoria', $id)->get();
            $this->id_subcategoria = null; // Limpiar la selección actual
        } else {
            $this->subcategorias = collect();
            $this->id_subcategoria = null;
        }
    }
    // Métodos de búsqueda
    public function updatedSearchServicio()
    {
        if ($this->searchServicio) {
            $this->servicios = ServiciosModel::where('nombre', 'like', '%' . $this->searchServicio . '%')->get();
        } else {
            $this->servicios = ServiciosModel::all();
        }
    }

    public function updatedSearchTipo()
    {
        if ($this->searchTipo) {
            $this->tipos = TiposModel::where('nombre', 'like', '%' . $this->searchTipo . '%')->get();
        } else {
            $this->tipos = TiposModel::all();
        }
    }

    public function updatedSearchCategoria()
    {
        if ($this->id_tipo && $this->searchCategoria) {
            $this->categorias = CategoriaModel::where('id_tipo', $this->id_tipo)
                ->where('nombre', 'like', '%' . $this->searchCategoria . '%')
                ->get();
        } elseif ($this->id_tipo) {
            $this->categorias = CategoriaModel::where('id_tipo', $this->id_tipo)->get();
        } else {
            $this->categorias = collect();
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
            $this->subcategorias = SubcategoriaModel::where('id_categoria', $this->id_categoria)
                ->where('nombre', 'like', '%' . $this->searchSubcategoria . '%')
                ->get();
        } elseif ($this->id_categoria) {
            $this->subcategorias = SubcategoriaModel::where('id_categoria', $this->id_categoria)->get();
        } else {
            $this->subcategorias = collect();
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
            'id_servicio',
            'id_tipo',
            'id_categoria',
            'id_subcategoria',
            'req_fotos_carga_inicial',
            'categorias',
            'subcategorias',
            'searchServicio',
            'searchTipo',
            'searchCategoria',
            'searchSubcategoria'
        ]);
    }
}
