<?php

namespace App\Traits\Importaciones;

use Exception;
use App\Services\FileImport\FileImportService;
use App\Services\FileImport\FileUbicaciones;
use App\Services\FileImport\FileProveedores;

trait ProveedoresTrait
{
    public $errores = [];

    public function descargarEjemploPrestadores()
    {
        $this->descargarEjemplo('proveedoresejemplo.xlsx', 'proveedoresejemplo.xlsx');
    }

    public function importarArchivoProveedores(FileImportService $importService)
    {
        if (!$this->archivo) {
            $this->addError('archivo', 'No se ha seleccionado ningún archivo.');
            $this->limpiarProveedores();
            return;
        }
        try {
            $requiredKeys = ['razon_social', 'cuit', 'localidad', 'provincia', 'ciudad', 'codigo_postal', 'calle', 'altura'];

            $importService = new FileUbicaciones();

            $this->previewDataProveedores = $importService->importFileUbicacion($this->archivo, $requiredKeys);

            if (empty($this->previewDataProveedores)) {
                $this->addError('archivo', 'El archivo no contiene registros válidos o faltan claves requeridas.');
                return;
            }

            // Actualizar valores locales basados en la previsualización
            $this->actualizarLocales();
            $this->resetErrorBag('archivo');
        } catch (Exception $e) {
            $this->addError('archivo', $e->getMessage());
            // Despachar el evento para que lo capture tu listener en JS (por ejemplo, SweetAlert)
            $this->dispatch('errorInfo', [
                'title'   => 'Error de Importación',
                'message' => $e->getMessage()
            ]);
            $this->limpiarProveedores();
        }
    }

    public function confirmarProveedores(FileImportService $importService)
    {
        if (empty($this->previewDataProveedores)) {
            $this->mostrarErrorImportacion('No hay datos para importar.');
            return;
        }

        $importService = new FileProveedores();

        $resultado = $importService->confirmarProveedores(
            $this->previewDataProveedores,       // Datos normalizados del archivo para activos
            $this->tipoOperacion,
            function ($errorMessage, $index) {
                // Callback para manejar errores
                $this->mostrarErrorImportacion($errorMessage);
            }
        );
        if ($resultado === true) {
            $this->mostrarExitoImportacion();
            $this->closeCompleto();
        } elseif ($resultado === 'proveedor_duplicado') {
            // 🧹 Resetear todo solo si es ese caso
            $this->limpiarProveedores();
        }
    }

    private function limpiarProveedores()
    {
        $this->previewDataProveedores = [];
    }
}
