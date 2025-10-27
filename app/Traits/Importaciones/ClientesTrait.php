<?php

namespace App\Traits\Importaciones;

use App\Services\FileImport\FileClientes;
use Exception;
use App\Services\FileImport\FileImportService;

trait ClientesTrait
{
    public function descargarEjemploClientes()
    {
        $this->descargarEjemplo('ClientesArchivoEjemplo.xlsx', 'ClientesArchivoEjemplo.xlsx');
    }

    public function importarClientes(FileImportService $importService)
    {
        // Verificar que se haya seleccionado un archivo
        if (!$this->archivo) {
            $this->addError('archivo', 'No se ha seleccionado ningún archivo.');
            $this->limpiarClientes();
            return;
        }

        try {
            // Definir las columnas requeridas (tal como vienen en el archivo original)
            $requiredKeys = ['name', 'email', 'cuil', 'password'];

            $importService = new FileClientes();

            $this->previewDataClientes =  $importService->importFile($this->archivo, $requiredKeys);

            // Si no se obtuvieron registros, lanzar un error
            if (empty($this->previewDataClientes)) {
                $this->addError('archivo', 'El archivo no contiene registros válidos o faltan claves requeridas.');
                return;
            }
            $this->resetErrorBag('archivo');
        } catch (Exception $e) {
            $this->addError('archivo', $e->getMessage());

            $this->dispatch('errorInfo', [
                'title'   => 'Error de Importación',
                'message' => $e->getMessage()
            ]);
            $this->limpiarClientes();
        }
    }

    public function confirmarClientes(FileImportService $importService)
    {
        if (empty($this->previewDataClientes)) {
            $this->mostrarErrorImportacion('No hay datos para importar.');
            return;
        }

        $importService = new FileClientes();

        $resultado = $importService->confirmarClientes(
            $this->previewDataClientes,       // Datos normalizados del archivo para activos
            $this->tipoOperacion,
            function ($errorMessage, $index) {
                $this->mostrarErrorImportacion($errorMessage);
            }
        );

        if ($resultado === true) {
            $this->mostrarExitoImportacion();
            $this->closeCompleto();
        } elseif ($resultado === 'cliente_duplicado') {
            $this->limpiarClientes();
        }
    }

    private function limpiarClientes()
    {
        $this->previewDataClientes = [];
    }
}
