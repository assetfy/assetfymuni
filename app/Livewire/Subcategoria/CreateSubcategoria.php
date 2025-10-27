<?php

namespace App\Livewire\Subcategoria;

use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use App\Models\SubcategoriaModel;
use Livewire\WithFileUploads;
use App\Models\CategoriaModel;
use App\Models\TiposModel;
use Livewire\Component;

class CreateSubCategoria extends Component
{
    use WithFileUploads;
    use VerificacionTrait;

    public $search = "";
    public $open = false;
    public $sigla, $id_tipo, $nombre, $descripcion, $id_categoria, $movil_o_fijo, $se_relaciona;
    public $imagenSubcategoria; // Cambiada la variable
    public $selectedTipoNombre, $searchTipo, $tipoPrueba;
    public $categorias2, $selectedCategoriaNombre, $searchCategoria;

    protected $listeners = ['crearSubcategoria'];

    protected $rules = [
        'sigla'                => 'required|max:10|min:3',
        'id_tipo'              => 'required',
        'nombre'               => 'required|max:50',
        'descripcion'          => 'required|max:100',
        'id_categoria'         => 'required',
        'movil_o_fijo'         => 'required',
        'se_relaciona'         => 'required',
        'imagenSubcategoria'   => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ];

    public function mount()
    {
        $this->tipoPrueba = TiposModel::all();
    }

    public function save()
    {
        $this->validate();
        $this->registro();
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    protected function registro()
    {
        $rutaFoto = null;
        if ($this->imagenSubcategoria) {
            $filename = $this->imagenSubcategoria->store('fotos', 'public');
            $rutaFoto = $filename;
        }

        $campos = ['sigla', 'nombre'];
        $valoresNuevos = [
            'id_tipo'       => $this->id_tipo,
            'id_categoria'  => $this->id_categoria,
            'sigla'         => $this->sigla,
            'nombre'        => $this->nombre,
            'movil_o_fijo'  => $this->movil_o_fijo,
            'se_relaciona'  => $this->se_relaciona,
            'imagen'        => $rutaFoto,
            'descripcion'   => $this->descripcion,
        ];

        $this->create(SubcategoriaModel::class, $campos, $valoresNuevos);

        $this->dispatch('refreshLivewireTable');
        $this->close();
    }

    public function setTipo($id)
    {
        $tipo = TiposModel::find($id);
        $this->selectedTipoNombre = $tipo ? $tipo->nombre : null;
        $this->id_tipo = $id;
        $this->searchTipo = '';
        $this->tipoPrueba = TiposModel::all();
        $this->cargarCategoria($id);
        $this->dispatch('closeDropdown', ['dropdown' => 'tipo']);
    }

    public function cargarCategoria($id)
    {
        if ($id) {
            $this->categorias2 = CategoriaModel::where('id_tipo', $id)->get();
            $this->id_categoria = null;
        } else {
            $this->categorias2 = collect();
            $this->id_categoria = null;
        }
    }

    public function updatedSearchTipo()
    {
        if ($this->searchTipo) {
            $this->tipoPrueba = TiposModel::where('nombre', 'like', '%' . $this->searchTipo . '%')->get();
        } else {
            $this->tipoPrueba = TiposModel::all();
        }
        if ($this->tipoPrueba->isEmpty() || !$this->tipoPrueba->contains('id_tipo', $this->id_tipo)) {
            $this->id_tipo = null;
            $this->categorias2 = collect();
            $this->id_categoria = null;
        }
    }

    public function setCategoria($id)
    {
        $categoria = CategoriaModel::find($id);
        $this->selectedCategoriaNombre = $categoria ? $categoria->nombre : null;
        $this->id_categoria = $id;
        $this->searchCategoria = '';
        $this->dispatch('closeDropdown', ['dropdown' => 'categoria']);
    }

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
        if ($this->categorias2->isEmpty() || !$this->categorias2->contains('id_categoria', $this->id_categoria)) {
            $this->id_categoria = null;
        }
    }

    public function close()
    {
        $this->reset([
            'id_categoria',
            'sigla',
            'nombre',
            'descripcion',
            'id_tipo',
            'se_relaciona',
            'movil_o_fijo',
            'categorias2',
            'imagenSubcategoria',
            'selectedTipoNombre',
            'selectedCategoriaNombre',
            'searchTipo',
            'searchCategoria',
            'tipoPrueba',
        ]);
        $this->open = false;
    }

    public function crearSubcategoria()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }

    public function removeImagen()
    {
        $this->imagenSubcategoria = null;
    }

    public function render()
    {
        return view('livewire.Subcategoria.create-subcategoria');
    }
}
