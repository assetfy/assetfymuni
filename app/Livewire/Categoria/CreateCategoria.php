<?php

namespace App\Livewire\Categoria;

use Livewire\Component;
use App\Models\TiposModel;
use Livewire\WithFileUploads;
use App\Models\CategoriaModel;
use App\Traits\VerificacionTrait;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\Cache;

class CreateCategoria extends Component
{
    use WithFileUploads;
    use VerificacionTrait;

    public $tipos;
    public $search = "";
    public $open = false;
    public $sigla, $id_tipo, $nombre, $descripcion, $imagenCategoria, $categorias, $selectedTipoNombre, $searchTipo, $tipoPrueba;
    public $currentPage = 1;
    public $perPage = 10;
    public $hasMorePages = false;

    protected $listeners = ['crearCategoria'];

    protected $rules = [
        'sigla'           => 'required|max:10|min:3',
        'id_tipo'         => 'required',
        'nombre'          => 'required|max:50',
        'descripcion'     => 'required|max:100',
        'imagenCategoria' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Imagen es opcional
    ];

    public function mount()
    {
        $this->resetPagination();
        $this->tipoPrueba = $this->fetchTipos();
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
        // Procesar la imagen si se subió
        if ($this->imagenCategoria) {
            $filename = $this->imagenCategoria->store('fotos', 'public');
            $rutaFoto = $filename;
        }

        $campos = ['sigla', 'nombre', 'descripcion'];
        $valoresNuevos = [
            'id_tipo'     => $this->id_tipo,
            'sigla'       => $this->sigla,
            'nombre'      => $this->nombre,
            'descripcion' => $this->descripcion,
            'imagen'      => $rutaFoto, // Puede ser nulo si no se subió una imagen
        ];

        $this->create(CategoriaModel::class, $campos, $valoresNuevos);
        $this->dispatch('refreshLivewireTable');
        $this->close();
    }

    public function iniciarBusqueda()
    {
        // Se actualiza sin paginación
        $this->tipoPrueba = $this->fetchTipos();
    }

    // Método para eliminar la imagen cargada
    public function removeImagen()
    {
        $this->imagenCategoria = null;
    }

    public function fetchTipos()
    {
        $totalTipos = TiposModel::count();

        if ($totalTipos <= 500) {
            $tiposCache = Cache::remember('tipos_cache', 60, function () {
                return TiposModel::all();
            });
            $filteredTipos = $tiposCache->filter(function ($tipo) {
                return stripos($tipo->nombre, $this->searchTipo) !== false;
            })->values();

            // Si se quita la paginación, no se usa offset ni slice
            return $filteredTipos;
        } else {
            // Consultamos directamente sin paginación
            $query = TiposModel::query();
            if ($this->searchTipo) {
                $query->where('nombre', 'like', '%' . $this->searchTipo . '%');
            }
            return $query->get();
        }
    }

    public function setTipo($id)
    {
        $this->selectedTipoNombre = TiposModel::find($id)?->nombre;
        $this->id_tipo = $id;
        $this->searchTipo = '';
        $this->tipoPrueba = $this->fetchTipos();
        $this->dispatch('closeDropdown', ['dropdown' => 'tipo']);
    }

    public function render()
    {
        return view('livewire.categoria.create-categoria');
    }

    public function close()
    {
        $this->_close();
    }

    protected function _close()
    {
        $this->reset(['sigla', 'nombre', 'descripcion', 'id_tipo', 'imagenCategoria', 'searchTipo', 'selectedTipoNombre']);
        $this->resetPagination();
        $this->open = false;
    }

    public function crearCategoria()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }

    // Eliminamos los métodos de paginación: loadMore() y resetPagination()
    public function resetPagination()
    {
        $this->currentPage = 1;
        $this->hasMorePages = false;
    }
}
