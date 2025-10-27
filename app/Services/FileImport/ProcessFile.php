<?php

namespace App\Services\FileImport;

use Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProcessFile
{
    /**
     * Procesa un archivo JSON, XML o XLSX y devuelve su contenido como array asociativo.
     */
    protected function procesarArchivo($file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $datos = [];

        if ($extension === 'json') {
            $content = file_get_contents($file->getRealPath());
            $datos = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Error al procesar el archivo JSON: ' . json_last_error_msg());
            }
            // Si el JSON tiene una clave "Worksheet", usarla para obtener el array de registros
            if (isset($datos['Worksheet']) && is_array($datos['Worksheet'])) {
                $datos = $datos['Worksheet'];
            }
        } elseif ($extension === 'xml') {
            $content = file_get_contents($file->getRealPath());
            $xmlObject = simplexml_load_string($content, 'SimpleXMLElement', LIBXML_NOCDATA);
            if ($xmlObject === false) {
                throw new Exception('Error al procesar el archivo XML.');
            }
            $json = json_encode($xmlObject);
            $datos = json_decode($json, true);
        } elseif ($extension === 'xlsx') {
            try {
                $spreadsheet = IOFactory::load($file->getRealPath());
                $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                if (count($sheetData) > 0) {
                    // Se asume que la primera fila contiene los encabezados
                    $headers = array_shift($sheetData);
                    $datos = [];
                    foreach ($sheetData as $row) {
                        $record = [];
                        foreach ($headers as $col => $header) {
                            $record[$header] = $row[$col] ?? null;
                        }
                        $datos[] = $record;
                    }
                }
            } catch (Exception $e) {
                throw new Exception('Error al procesar el archivo XLSX: ' . $e->getMessage());
            }
        } else {
            throw new Exception('Tipo de archivo no soportado.');
        }

        return $datos;
    }

    /**
     * Valida que todas las columnas requeridas existan en el archivo importado.
     */
    protected function validarColumnas(array $datos, array $requiredKeys): void
    {
        // Normaliza las claves del registro.
        $normalizarDatos = [];
        foreach ($datos as $key => $value) {
            $normalizarDatos[$this->normalizarKey($key)] = $value;
        }
        // Normaliza las claves requeridas.
        $normalizarRequeridos = array_map([$this, 'normalizarKey'], $requiredKeys);

        // Busca claves faltantes.
        $missingKeys = [];
        foreach ($normalizarRequeridos  as $reqKey) {
            if (!array_key_exists($reqKey, $normalizarDatos)) {
                $missingKeys[] = $reqKey;
            }
        }

        if (!empty($missingKeys)) {
            throw new Exception(
                'El archivo no contiene las columnas requeridas para ser importado'
            );
        }
    }
    /**
     * Normaliza una cadena: quita espacios, pasa a minúsculas y reemplaza espacios por guiones bajos.
     */
    protected function normalizarKey(string $key): string
    {
        return str_replace(' ', '_', strtolower(trim($key)));
    }

    /**
     * Asigna el valor correcto a un campo, desde array local o global, y lo valida si es requerido.
     */
    protected function validateAndAssign(
        array &$dato,
        string $fieldKey,
        array &$localArray,
        &$globalValue,
        int $index,
        string $fieldDisplay,
        callable $errorCallback
    ): bool {
        // Campo especial que se permite en blanco (gestor opcional)

        if ($fieldKey === 'cuil_gestor') {
            $valor = $localArray[$index] ?? $globalValue ?? null;
            $dato[$fieldKey] = $valor !== '' ? $valor : null;
            return true;
        }

        // Asignar desde array local o valor global
        if (isset($localArray[$index]) && $localArray[$index] !== '') {
            $dato[$fieldKey] = $localArray[$index];
        } elseif ($globalValue !== '') {
            $dato[$fieldKey] = $globalValue;
        } elseif (!isset($dato[$fieldKey])) {
            // Si no venía desde antes, aseguramos la clave con null
            $dato[$fieldKey] = null;
        }

        // Validar campo obligatorio (excepto algunos campos opcionales específicos)
        if ($dato[$fieldKey] === null || $dato[$fieldKey] === '' || is_array($dato[$fieldKey])) {
            // Llamar al callback con un mensaje de error si no se asignó un valor válido
            if (!in_array($fieldKey, ['gestor', 'responsable', 'asignado'])) {
                $errorCallback("Error en la fila " . ($index + 1) . ": Falta el valor de $fieldDisplay.", $index);
                return false;
            }
        }
        return true;
    }

    /**
     * Importa y normaliza los datos del archivo.
     * Permite aplicar validaciones o transformaciones específicas por fila.
     */
    public function importFile(
        $file,
        array $requiredKeys = [],
        callable $normalizacionEspecifica = null
    ): array {
        // 1. Procesar el archivo y obtener registros sin normalizar
        $datos = $this->procesarArchivo($file);

        // 2. Validar columnas requeridas (si se pasaron)
        if (!empty($requiredKeys)) {
            $primerRegistro = reset($datos);
            if (!is_array($primerRegistro)) {
                throw new Exception('El archivo no contiene registros válidos.');
            }
            $this->validarColumnas($primerRegistro, $requiredKeys);
        }

        // 3. Normalizar claves y limpiar valores
        $datosNormalizados = [];
        foreach ($datos as $registro) {
            // Normalizar claves
            $registroNormalizado = [];
            foreach ($registro as $key => $value) {
                $normalizedKey = str_replace(' ', '_', strtolower(trim($key)));
                $registroNormalizado[$normalizedKey] = $value;
            }

            // Aplicar normalización o validación específica si se pasó
            if (is_callable($normalizacionEspecifica)) {
                $registroNormalizado = $normalizacionEspecifica($registroNormalizado);
            }

            // Saltar filas que queden completamente vacías
            $todosVacios = true;
            foreach ($registroNormalizado as $valor) {
                if ((is_string($valor) && trim($valor) !== '') || (!is_string($valor) && !is_null($valor))) {
                    $todosVacios = false;
                    break;
                }
            }
            if ($todosVacios) {
                continue;
            }

            $datosNormalizados[] = $registroNormalizado;
        }

        return $datosNormalizados;
    }
}