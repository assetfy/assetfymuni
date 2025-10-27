<?php

namespace App\Livewire\Subcategoria;

use App\Models\ActivosModel;
use Illuminate\Support\Facades\Storage;
use App\Services\MiddlewareInvoker;
use App\Models\SubcategoriaModel;
use App\Traits\VerificacionTrait;
use App\Models\CategoriaModel;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\TiposModel;
use Livewire\Component;

class EditSubcategoria extends Component
{
    use WithFileUploads;
    use VerificacionTrait;

    public $open = false;
    public $categoria;
    public $id_categoria, $id_tipo, $updatedSigla, $updatedNombre, $updatedDescripcion, $updatedMovilofijo, $updatedRelacion, $categorias, $tipoPruebas;
    public $tipos, $rutaFoto, $updatedImagen, $imagen;
    public $subcategoria;
    public $hasActivos = false; // Nueva propiedad para verificar si hay activos

    protected $listeners = ['openModalSubcategoria'];

    protected $rules = [
        'updatedSigla' => 'required|max:10|min:3',
        'updatedNombre' => 'required|max:50',
        'updatedMovilofijo' => 'required',
        'updatedDescripcion' => 'required|max:100',
        'updatedRelacion' => 'required',
        'updatedImagen' => 'nullable|image|max:2048'
    ];

    protected $messages = [
        'updatedSigla.required' => 'La sigla es obligatoria.',
        'updatedSigla.max' => 'La sigla no debe exceder los 10 caracteres.',
        'updatedSigla.min' => 'La sigla debe tener al menos 3 caracteres.',
        'updatedNombre.required' => 'El nombre es obligatorio.',
        'updatedNombre.max' => 'El nombre no debe exceder los 50 caracteres.',
        'updatedDescripcion.required' => 'La descripción es obligatoria.',
        'updatedDescripcion.max' => 'La descripción no debe exceder los 100 caracteres.',
        'updatedImagen.image' => 'El archivo debe ser una imagen válida.',
        'updatedImagen.mimes' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif, svg.',
        'updatedImagen.max' => 'La imagen no debe ser mayor a 2 MB.',
    ];



    private function datos($data)
    {
        $this->subcategoria = SubcategoriaModel::find($data);
        $this->updatedSigla =  $this->subcategoria->sigla;
        $this->updatedMovilofijo =  $this->subcategoria->movil_o_fijo;
        $this->updatedRelacion =  $this->subcategoria->se_relaciona;
        $this->updatedNombre =  $this->subcategoria->nombre;
        $this->updatedDescripcion =  $this->subcategoria->descripcion;
        $this->categorias = CategoriaModel::find($this->subcategoria->id_categoria);
        $this->tipoPruebas = TiposModel::find($this->subcategoria->id_tipo);
        $this->hasActivos = $this->activo() ? true : false;
        $this->imagen = $this->subcategoria->imagen ?? null;
    }

    private function activo()
    {
        return ActivosModel::where('id_subcategoria', $this->subcategoria->id_subcategoria)->first();
    }

    public function openModalSubcategoria($data)
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->datos($data);
            $this->open = true;
        }
    }

    #[On('guardado')]
    public function actualizarSubcategoria()
    {
        $this->actualizar();
    }

    protected function actualizar()
    {
        // Si se ha realizado alguna modificacion de la imagen
        if ($this->updatedImagen) {
            // Carga la nueva imagen
            $filename = $this->updatedImagen->store('fotos', 'public');
            $this->rutaFoto = $filename; // 'fotos/imagen.jpg'
            // Elimina la imagen anterior si existe
            if ($this->imagen && Storage::exists(str_replace('public/storage/', '', $this->imagen))) {
                Storage::delete(str_replace('public/storage/', '', $this->imagen));
            }
        }

        $campos = ['sigla', 'nombre', 'descripcion', 'movil_o_fijo', 'se_relaciona', 'imagen'];

        $valoresActualizados = [
            'sigla' => $this->updatedSigla,
            'movil_o_fijo' => $this->updatedMovilofijo,
            'se_relaciona' => $this->updatedRelacion,
            'nombre' => $this->updatedNombre,
            'descripcion' => $this->updatedDescripcion,
            'imagen' => $this->rutaFoto,
        ];

        $this->verificar($this->subcategoria, $campos, $valoresActualizados);
        $this->dispatch('refreshLivewireTable');
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.subcategoria.edit-subcategoria', [
            'categoriaSeleccionada' => $this->categorias,
            'tipoSeleccionado' => $this->tipoPruebas,
            'hasActivos' => $this->hasActivos, // Pasar la variable al modal
        ]);
    }
}
