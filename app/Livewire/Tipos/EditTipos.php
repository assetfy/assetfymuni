<?php

namespace App\Livewire\Tipos;

use Illuminate\Support\Facades\Storage;
use App\Traits\VerificacionTrait;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use App\Models\TiposModel;
use App\Services\MiddlewareInvoker;
use Livewire\Component;


class EditTipos extends Component
{
    use WithFileUploads;
    use VerificacionTrait;
    public $open = false;
    public $tipo;
    public $updatedSigla;
    public $updatedNombre;
    public $updatedDescripcion;
    public $tipos;
    public $rutaFoto;
    public $updatedImagen, $imagen;
    protected $listeners = ['openEditTipo'];

    protected $rules = [
        'updatedSigla' => 'required|max:10|min:3',
        'updatedNombre' => 'required|max:50',
        'updatedDescripcion' => 'required|max:100',
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
        $this->tipos = TiposModel::find($data);
        $this->updatedSigla =  $this->tipos->sigla;
        $this->updatedNombre =  $this->tipos->nombre;
        $this->updatedDescripcion =  $this->tipos->descripcion;
        $this->imagen = $this->tipos->imagen ?? null;
    }

    public function openEditTipo($data)
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
    public function verificacion()
    {
        $this->actualizar();
    }

    protected function actualizar()
    {
        // Si se ha realizado alguna modificación en la imagen
        if ($this->updatedImagen) {
            // Carga la nueva imagen
            $filename = $this->updatedImagen->store('fotos', 'public');
            $this->rutaFoto = $filename; // Ejemplo: 'fotos/imagen.jpg'

            // Solo intentamos eliminar la imagen antigua si $this->tipo existe y tiene una imagen
            if ($this->imagen) {
                // Remover la parte 'public/storage/' si está presente en la ruta (ajustá según tu configuración)
                $existingPath = str_replace('public/storage/', '', $this->imagen);
                if (Storage::exists($existingPath)) {
                    Storage::delete($existingPath);
                }
            }
        }

        $campos = ['sigla', 'nombre', 'descripcion', 'imagen'];

        $valoresActualizados = [
            'sigla' => $this->updatedSigla,
            'nombre' => $this->updatedNombre,
            'descripcion' => $this->updatedDescripcion,
            'imagen' => $this->rutaFoto
        ];
        $this->verificar($this->tipos, $campos, $valoresActualizados);

        $this->dispatch('refreshLivewireTable');
    }


    public function close()
    {
        $this->open = false;
    }
}
