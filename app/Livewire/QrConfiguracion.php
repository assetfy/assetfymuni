<?php

namespace App\Livewire;

use App\Helpers\IdHelper;
use Livewire\Component;
use App\Models\QrBienModel;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QrConfiguracion extends Component
{
    use WithFileUploads;
    protected $listeners = ['render' => 'render'];

    public $foto, $texto, $cuit, $empresa, $updateDescripcion, $photoAgregar;

    protected $rules = [
        'updateDescripcion' =>  'nullable|string|max:200',
        'photoAgregar'      => 'nullable|image|max:1024|mimes:jpeg,png,jpg,webp',
    ];

    protected $messages = [
        'updateDescripcion.max' => 'La descripción no debe exceder de los 200 caracteres.',
        'photoAgregar.max' => 'La imagen no debe ser mayor a 1MB.',
        'photoAgregar.mimes' => 'La imagen debe estar en formato JPEG o PNG.',
    ];

    public function mount()
    {
        $this->cuit = IdHelper::idEmpresa(); // Obtiene el CUIT de la empresa
        // dd($this->cuit);
        $this->empresa = QrBienModel::where('cuit', $this->cuit)->first(); // Envia dichos datos a la funcion privada datosEmpresa
        $this->updateDescripcion = $this->empresa->texto ?? '';
    }

    public function updatedPhotoAgregar()
    {
        $this->validateOnly('photoAgregar');
    }

    public function guardar()
    {
        $this->validate();

        // 1) Crea o actualiza la fila por CUIT (no se borra nunca)
        $row = QrBienModel::updateOrCreate(
            ['cuit' => $this->cuit],
            ['texto' => $this->updateDescripcion]
        );

        // 2) Si subieron logo, guardarlo y reemplazar el anterior
        if ($this->photoAgregar) {
            $dir = "StorageMvp/qr-logos/{$this->cuit}";
            $filename = 'qr_logo_' . Str::uuid() . '.' . $this->photoAgregar->getClientOriginalExtension();

            // Igual que tu profile-photos pero en qr-logos
            $path = $this->photoAgregar->storeAs($dir, $filename, 's3'); // privado por defecto

            // Borra anterior si había
            if (filled($row->foto) && Storage::disk('s3')->exists($row->foto)) {
                Storage::disk('s3')->delete($row->foto);
            }

            $row->forceFill(['foto' => $path])->save();
            $this->reset('photoAgregar');
        }

        $this->empresa = $row->fresh();
        $this->dispatch('lucky');
    }

    public function eliminarFoto()
    {
        $row = QrBienModel::firstOrCreate(['cuit' => $this->cuit]);

        try {
            if ($row->foto && Storage::disk('s3')->exists($row->foto)) {
                Storage::disk('s3')->delete($row->foto);
            }
        } catch (\Throwable $e) {
            Log::warning('Error borrando archivo en S3', ['e' => $e->getMessage(), 'path' => $row->foto]);
        }

        // Asegurate que 'foto' sea fillable, si no usá forceFill()
        $row->update(['foto' => null]);

        $this->empresa = $row->fresh();
        $this->dispatch('lucky');
    }


    public function limpiarTexto()
    {
        $row = QrBienModel::firstOrCreate(['cuit' => $this->cuit]);
        $row->update(['texto' => null]);
        $this->updateDescripcion = '';
        $this->empresa = $row->fresh();
    }

    public function resetForm()
    {
        // volver a los valores guardados (no borra nada)
        $this->empresa = QrBienModel::where('cuit', $this->cuit)->first();
        $this->updateDescripcion = $this->empresa->texto ?? '';
        $this->reset('photoAgregar');
    }

    public function render()
    {
        $logoUrl = ($this->empresa && $this->empresa->foto)
            ? Storage::disk('s3')->temporaryUrl($this->empresa->foto, now()->addMinutes(10))
            : null;

        return view('livewire.qr-configuracion', [
            'empresa' => $this->empresa,
            'logoUrl' => $logoUrl,
        ]);
    }
}
