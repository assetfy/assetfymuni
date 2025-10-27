<?php

namespace App\Livewire\Categoria;

use Illuminate\Support\Facades\Storage;
use App\Traits\VerificacionTrait;
use App\Models\CategoriaModel;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\TiposModel;
use App\Services\MiddlewareInvoker;
use Livewire\Component;


class EditCategoria extends Component
{
    use WithFileUploads;
    use VerificacionTrait;
    public $open = false;
    public $categoria;
    public $updateTipo;
    public $updatedSigla;
    public $updatedNombre;
    public $updatedDescripcion;
    public $updatedImagenCategoria, $imagen;
    public $rutaFoto;
    protected $listeners = ['openModalCategoria'];

    protected $rules = [
        'updatedSigla' => 'required|max:10|min:3',
        'updatedNombre' => 'required|max:50',
        'updatedDescripcion' => 'required|max:100',
        'updatedImagenCategoria' => 'nullable|image|max:1024'
    ];

    protected $messages = [
        'updatedSigla.required' => 'La sigla es obligatoria.',
        'updatedSigla.max' => 'La sigla no debe exceder los 10 caracteres.',
        'updatedSigla.min' => 'La sigla debe tener al menos 3 caracteres.',
        'updatedNombre.required' => 'El nombre es obligatorio.',
        'updatedNombre.max' => 'El nombre no debe exceder los 50 caracteres.',
        'updatedDescripcion.required' => 'La descripción es obligatoria.',
        'updatedDescripcion.max' => 'La descripción no debe exceder los 100 caracteres.',
        'updatedImagenCategoria.image' => 'El archivo debe ser una imagen válida.',
        'updatedImagenCategoria' => 'La imagen debe ser de tipo: jpeg, png, jpg, gif, svg.',
        'updatedImagenCategoria.max' => 'La imagen no debe ser mayor a 2 MB.',
    ];

    private function datos($data)
    {
        $this->categoria = CategoriaModel::find($data);
        $this->updatedSigla =  $this->categoria->sigla;
        $this->updatedNombre =  $this->categoria->nombre;
        $this->updatedDescripcion =  $this->categoria->descripcion;
        $this->imagen = $this->categoria->imagen ?? null;
    }

    public function openModalCategoria($data)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->datos($data);
            $this->open = true;
        }
    }

    #[On('guardado')]
    public function actualizarCategoria()
    {
        $this->actualizar();
    }

    protected function actualizar()
    {
        // Si se ha realizado alguna modificación de la imagen
        if ($this->updatedImagenCategoria) {
            // Carga la nueva imagen
            $filename = $this->updatedImagenCategoria->store('fotos', 'public');
            $this->rutaFoto = $filename;

            // Elimina la imagen anterior si existe
            if ($this->imagen && Storage::exists($this->imagen)) {
                Storage::delete($this->imagen);
            }
        }

        $campos = ['sigla', 'nombre', 'descripcion', 'imagen'];

        $valoresActualizados = [
            'sigla' => $this->updatedSigla,
            'nombre' => $this->updatedNombre,
            'descripcion' => $this->updatedDescripcion,
            'imagen' => $this->rutaFoto
        ];
        // Actualizar la categoría
        $this->verificar($this->categoria, $campos, $valoresActualizados);
        $this->dispatch('refreshLivewireTable');
    }


    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        $tipoPrueba = TiposModel::all();
        return view('livewire.categoria.edit-categoria', compact('tipoPrueba'));
    }
}
