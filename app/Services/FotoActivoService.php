<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use App\Models\ActivosFotosModel;

class FotoActivoService
{
    public function guardarFotosSubidas(array $fotos, int $activoId, array $metadatos): void
    {
        foreach ($fotos as $foto) {
            $filename = $foto->store('StorageMvp/fotos', 's3');
            if ($filename) {
                $this->crearFoto($filename, $activoId, $metadatos);
            }
        }
    }

    public function guardarFotosCapturadas(array $capturedPhotos, int $activoId, array $metadatos): void
    {
        foreach ($capturedPhotos as $fotoDataUrl) {
            if (!str_starts_with($fotoDataUrl, 'data:image/')) {
                continue;
            }

            $data = explode(',', $fotoDataUrl);
            if (count($data) !== 2) {
                continue;
            }

            $decoded = base64_decode($data[1]);
            if ($decoded === false) {
                continue;
            }

            $filename = 'captured_' . uniqid() . '.png';
            $filePath = 'StorageMvp/fotos/' . $filename;

            if (Storage::disk('s3')->put($filePath, $decoded)) {
                $this->crearFoto($filePath, $activoId, $metadatos);
            }
        }
    }

    protected function crearFoto(string $ruta, int $activoId, array $metadatos): void
    {
        if ($this->validarMetadatos($metadatos)) {
            ActivosFotosModel::create([
                'id_activo' => $activoId,
                'ruta_imagen' => $ruta,
                'id_tipo' => $metadatos['id_tipo'],
                'id_categoria' => $metadatos['id_categoria'],
                'id_subcategoria' => $metadatos['id_subcategoria'],
            ]);
        }
    }

    protected function validarMetadatos(array $metadatos): bool
    {
        return isset($metadatos['id_tipo'], $metadatos['id_categoria'], $metadatos['id_subcategoria']);
    }
}
