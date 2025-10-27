<?php

namespace App\Livewire\Perfil\Empresas;

use App\Models\EmpresasModel;
use App\Models\FotosDeEmpresaModel;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Session;
use Intervention\Image\ImageManager;
use Intervention\Image\ImageManagerStatic as InterventionImage;

use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class Empresa extends Component
{
    use WithFileUploads;

    protected $listeners = ['render' => 'render'];

    public $id, $empresa, $updateDescripcion;
    public $photo, $fotoId, $logo, $photoAgregar, $photoEditar;
    public $search = "";
    public $sort = 'cuit';
    public $direction = 'desc';

    // Reglas de verificación para el campo de la descripción
    protected $rules = [
        'updateDescripcion' =>  'required|string|max:400',
    ];

    // Mensajes de error para ser mostrados
    protected $messages = [
        'updateDescripcion.max' => 'La descripción no debe exceder de los 400 caracteres.',
        'photoAgregar.max' => 'La imagen no debe ser mayor a 1MB.',
        'photoAgregar.mimes' => 'La imagen debe estar en formato JPEG o PNG.',
        'photoEditar.max' => 'La imagen no debe ser mayor a 1MB.',
        'photoEditar.mimes' => 'La imagen debe estar en formato JPEG o PNG.',
        'logo.max' => 'La imagen no debe ser mayor a 1MB.',
        'logo.mimes' => 'La imagen debe estar en formato JPEG o PNG.',
    ];

    public function mount()
    {
        $this->id = Session::get('cuitEmpresaSeleccionado'); // Obtiene el CUIT de la empresa
        $this->empresa = $this->datosEmpresa($this->id); // Envia dichos datos a la funcion privada datosEmpresa
        $this->updateDescripcion = $this->empresa->descripcion_actividad ?? 'Sin actividad';
    }

    private function datosEmpresa($id)
    {
        // Permite obtener la actividad asociada de la empresa
        return EmpresasModel::with('actividades')->where('cuit', $id)->first();
    }

    public function guardarDescripcion()
    {
        // Valida los datos
        $this->validate([
            'updateDescripcion' => 'required|string|max:400',
        ]);

        // Si se efectuaron cambios, se muestra mensajes de exito
        if ($this->empresa->descripcion_actividad != $this->updateDescripcion) {
            $this->empresa->descripcion_actividad = $this->updateDescripcion; // Actualizar la descripción
            $this->empresa->save();
            $this->close();
        } else {
            $this->dispatch('warning', 'Sin cambios efectuados.');
            $this->dispatch('close-edit-modal');
        }
    }

    public function updatedPhoto()
    {
        // Validamos la imagen en cuanto se actualiza el archivo
        $this->validate([
            'photoAgregar' => 'image|max:1024|mimes:jpeg,png',
            'photoEditar' => 'image|max:1024|mimes:jpeg,png',
            'logo' => 'image|max:1024|mimes:jpeg,png',
        ]);
    }

    public function savePhoto()
    {
        $path = null;

        // Validamos si se ha cargado una imagen
        if (!$this->photoAgregar && !$this->photoEditar && !$this->logo && !$this->fotoId) {
            $this->addError('photo', 'Debe cargar una imagen para continuar.');
            return;
        }

        if ($this->logo instanceof \Illuminate\Http\UploadedFile) {
            $filename = 'logo_' . uniqid() . '.' . $this->logo->extension();
            $path     = $this->logo->storeAs(
                'StorageMvp/logos',  // carpeta en S3
                $filename,
                's3'
            );
            \App\Models\EmpresasModel::find($this->id)
                ->update(['logo' => $path]);
            // opcionalmente refresca $this->empresa:
            $this->empresa = $this->datosEmpresa($this->id);
        }
        if (isset($this->photoAgregar) && $this->photoAgregar instanceof \Illuminate\Http\UploadedFile) {
            // 1) Guardar en disco 'public' dentro de la carpeta 'empresas'
            $path = $this->photoAgregar->store('empresas', 'public');

            // 2) Leer con Intervention Image
            $manager = new ImageManager(Driver::class);
            $image   = $manager->read($this->photoAgregar->getRealPath());

            // 3) Volver a escribir la versión redimensionada en public/storage/empresas
            $image->save(public_path('storage/' . $path));
        }
        if (isset($this->photoEditar) && $this->photoEditar instanceof \Illuminate\Http\UploadedFile) {
            // 1) Construyes un nombre único
            $filename = 'empresa_' . uniqid() . '.' . $this->photoEditar->extension();

            // 2) Lo subes a S3 dentro de StorageMvp/facturas
            $path = $this->photoEditar->storeAs(
                'StorageMvp/imagenEmpresa',  // carpeta en S3
                $filename,
                's3'
            );

            $manager = new ImageManager(Driver::class);
            $image   = $manager->read($this->photoEditar->getRealPath());
            // 4) Actualizas tu modelo con la nueva ruta S3
            $fotos = FotosDeEmpresaModel::find($this->fotoId);
            if ($fotos) {
                // borras la anterior de S3 si existe
                if (Storage::disk('s3')->exists($fotos->foto)) {
                    Storage::disk('s3')->delete($fotos->foto);
                }
                $fotos->update(['foto' => $path]);
            }
        }

        if ($this->fotoId) {
            $fotos = FotosDeEmpresaModel::find($this->fotoId);

            if ($fotos && $this->photoEditar instanceof \Illuminate\Http\UploadedFile) {
                $file     = $this->photoEditar;
                $filename = 'empresa_' . uniqid() . '.' . $file->extension();
                $path     = $file->storeAs('StorageMvp/imagenEmpresa', $filename, 's3');

                // Borra la anterior de S3 si existe
                if ($fotos->foto && Storage::disk('s3')->exists($fotos->foto)) {
                    Storage::disk('s3')->delete($fotos->foto);
                }

                // Actualiza la BD con la nueva ruta S3
                $fotos->update([
                    'foto' => $path,
                ]);
            }
        } else {
            // Alta de nueva foto
            if ($this->photoAgregar instanceof \Illuminate\Http\UploadedFile) {
                $file     = $this->photoAgregar;
                $filename = 'empresa_' . uniqid() . '.' . $file->extension();
                $path     = $file->storeAs('StorageMvp/imagenEmpresa', $filename, 's3');

                FotosDeEmpresaModel::create([
                    'cuit' => $this->id,
                    'foto' => $path,
                ]);
            }
        }
        $this->close();
    }

    public function eliminarFoto()
    {
        // Buscar la empresa por ID
        $empresa = EmpresasModel::find($this->id);

        if ($empresa && $empresa->logo) {
            // 1) Eliminar de S3 si existe
            if (Storage::disk('s3')->exists($empresa->logo)) {
                Storage::disk('s3')->delete($empresa->logo);
            }

            // 2) Seguir eliminando de 'public' si lo tenías ahí también
            if (Storage::disk('public')->exists($empresa->logo)) {
                Storage::disk('public')->delete($empresa->logo);
            }

            // 3) Poner el campo logo a null en la BD
            $empresa->update([
                'logo' => null,
            ]);
        }

        $this->dispatch('render');
    }


    public function fotoSeleccionada($index)
    {
        $photos = FotosDeEmpresaModel::where('cuit', $this->id)->get();  // Obtener todas las fotos
        $photo = $photos[$index] ?? null;
        if ($photo) {
            $this->fotoId = $photo->id_foto;
            $this->photo = null;  // Limpiar cualquier imagen anterior cargada
        }
    }

    private function fotosEmpresa($id)
    {
        return FotosDeEmpresaModel::where('cuit', $id)
            ->select('id_foto', 'foto')
            ->orderBy('id_foto', 'asc')
            ->get();
    }

    public function render()
    {
        $fotosEmpresa = $this->fotosEmpresa($this->id)->take(4);

        // Obtener las fotos de la empresa y completar con "Sin imagen" si es necesario
        $imagenes = $fotosEmpresa->pluck('foto')->toArray();
        $missingImagesCount = 4 - count($imagenes);
        for ($i = 0; $i < $missingImagesCount; $i++) {
            $imagenes[] = null; // Representa una imagen faltante
        }

        return view('livewire.perfil.empresas.empresa', [
            'empresa' => $this->empresa,
            'imagenes' => $imagenes
        ]);
    }

    public function close()
    {
        $this->reset('photoAgregar', 'photoEditar', 'fotoId', 'logo');

        $this->dispatch('lucky');
        $this->dispatch('close-edit-modal');
        $this->dispatch('render');
    }

    public function asignarActividad()
    {
        $this->dispatch('editarActividad')->to('perfil.empresas.editar-actividad');
    }
}
