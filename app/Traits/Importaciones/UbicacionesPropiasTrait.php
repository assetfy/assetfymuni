<?php

namespace App\Traits\Importaciones;

use App\Helpers\IdHelper;
use App\Models\EmpresasModel;
use App\Models\TiposUbicacionesModel;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Services\FileImport\FileImportService;
use App\Services\FileImport\FileUbicaciones;

trait UbicacionesPropiasTrait
{
    public $errores = [];
    public $tiposUbicacionCache;

    // DESCARGA DE EJEMPLO DE UBICACIONES PROPIAS
    public function descargarEjemploUbicacion()
    {
        $this->descargarEjemplo('UbicacionesPropiasEjemplo.xlsx', 'UbicacionesPropiasEjemplo.xlsx');
    }

    public function descargarEjemploUbicacionCliente()
    {
        $this->descargarEjemplo('UbicacionesClientesEjemplo.xlsx', 'UbicacionesClientesEjemplo.xlsx');
    }

    private function validarArchivoSeleccionado()
    {
        if (!$this->archivo) {
            $this->addError('archivo', 'No se ha seleccionado ningún archivo.');
            $this->limpiarPrevisualizacion();
            return false;
        }
        return true;
    }

    private function getUbicacionImporter(): FileUbicaciones
    {
        return new FileUbicaciones($this->tipoDatos);
    }

    /**
     * Método para procesar el archivo subido (JSON o XML) y previsualizar su contenido.
     */
    public function importarArchivoUbicaciones(FileImportService $importService)
    {
        // Verificar que se haya seleccionado un archivo
        if (!$this->validarArchivoSeleccionado()) return;

        try {
            // Define las columnas requeridas
            $requiredKeys = ['Nombre', 'Pais', 'Provincia', 'Ciudad', 'Codigo Postal', 'Calle', 'Altura', 'Tipo De Ubicacion', 'Multiples Pisos'];

            $importService = $this->getUbicacionImporter();

            // Importar y normalizar el archivo
            $this->previewDataUbicacion = $importService->importFileUbicacion($this->archivo, $requiredKeys);

            $tipos = collect($this->previewDataUbicacion)
                ->pluck('tipo_de_ubicacion')
                ->filter()
                ->unique()
                ->map(fn($v) => strtolower(trim($v)))
                ->values()
                ->all();

            // dd($tipos);
            if (count($tipos) > 0) {
                $tiposDb = TiposUbicacionesModel::where(function ($query) use ($tipos) {
                    $query->whereIn('id_tipo', array_filter($tipos, fn($v) => is_numeric($v)))
                        ->orWhereIn(DB::raw('LOWER(nombre)'), array_filter($tipos, fn($v) => !is_numeric($v)));
                })->get(['id_tipo', 'nombre']);

                // dd($tiposDb);
                foreach ($tiposDb as $tipo) {
                    $this->tiposUbicacionCache[strtolower(trim($tipo->id_tipo))] = $tipo->toArray();
                    $this->tiposUbicacionCache[strtolower(trim($tipo->nombre))] = $tipo->toArray();
                }

                foreach ($this->previewDataUbicacion as $index => $row) {
                    $valor = strtolower(trim($row['tipo_de_ubicacion'] ?? ''));

                    $ubicacion = $this->tiposUbicacionCache[$valor] ?? null;

                    $this->localTipoUbicacion[$index] = $ubicacion['id_tipo'] ?? '';
                }
            }

            foreach ($this->previewDataUbicacion as $index => $row) {
                $multiples = strtolower(trim($row['multiples_pisos'] ?? ''));
                $pisos = $row['piso'] ?? '';
                $subsuelo = $row['subsuelo'] ?? '';

                if ($multiples === 'si') {
                    $this->local_multiples[$index] = '1';
                    $this->localPiso[$index] = $pisos; // Asignar el valor de pisos
                    $this->localSubsuelo[$index] = $subsuelo; // Asignar el valor de subsuelo
                } elseif ($multiples === 'no') {
                    $this->local_multiples[$index] = '0';
                    $this->localPiso[$index] = ''; // Limpiar si no es múltiple
                    $this->localSubsuelo[$index] = ''; // Limpiar si no es
                } else {
                    $this->local_multiples[$index] = ''; // Por si falta o viene mal
                }
            }

            if ($this->tipoDatos === 'UbicacionesUsuarios') {

                // dd('aca', $this->previewDataUbicacion);
                // Verificar si la clave 'cuit_propietario' existe en al menos una fila
                $contieneCuitPropietario = collect($this->previewDataUbicacion)
                    ->contains(fn($fila) => array_key_exists('cuit_propietario', $fila));

                if (!$contieneCuitPropietario) {
                    $this->mostrarErrorImportacion('El archivo no contiene registros válidos o faltan claves requeridas.');
                    $this->limpiarPrevisualizacion();
                }

                [$miCuitDigits, $misNombresNorm] = $this->misIdentidadesEmpresa();


                $filasPropias = [];
                foreach ($this->previewDataUbicacion as $i => $fila) {
                    // puede venir como "CUIT Propietario" o "cuit_propietario"
                    $raw = $fila['cuit_propietario'] ?? ($fila['CUIT Propietario'] ?? '');
                    $raw = (string)$raw;

                    $digits = $this->normDigits($raw);
                    if ($digits !== '') {
                        // Caso CUIT → comparo por dígitos
                        if ($digits === $miCuitDigits) {
                            $filasPropias[] = $i + 1;
                            continue;
                        }
                    } else {
                        // Caso NOMBRE → comparo nombre normalizado contra mis nombres
                        $norm = $this->normName($raw);
                        if ($norm !== '' && in_array($norm, $misNombresNorm, true)) {
                            $filasPropias[] = $i + 1;
                            continue;
                        }
                    }
                }
                if (!empty($filasPropias)) {
                    // limpio lo que pinta la vista ANTES de salir
                    $this->limpiarPrevisualizacion();

                    $miCuitMostrar = IdHelper::idEmpresa();
                    $this->addError(
                        'archivo',
                        'No se pueden cargar ubicaciones de la propia empresa (' . $miCuitMostrar . '). Filas: ' . implode(', ', $filasPropias) . '.'
                    );
                    $this->dispatch('errorInfo', [
                        'title'   => 'Error de Importación',
                        'message' => 'Se encontraron filas con "CUIT Propietario" igual a tu empresa (por CUIT o por nombre). ' .
                            'Corrige esas filas y vuelve a intentar. Filas: ' . implode(', ', $filasPropias) . '.',
                    ]);
                    return; // ← CORTE REAL
                }
                // 1) Tomo todos los valores únicos que vienen en "empresa"
                $valores = collect($this->previewDataUbicacion)
                    ->pluck('cuit_propietario')
                    ->filter()       // quita null/'' 
                    ->unique();

                // 2) Normalizo y separo: solo-dígitos = CUIT; resto = nombres
                $cuits = $valores->map(function ($v) {
                    // si viene con guiones/espacios, los saco
                    $soloDigitos = preg_replace('/\D+/', '', (string)$v);
                    return $soloDigitos;
                })
                    ->filter(fn($v) => $v !== '' && ctype_digit($v)) // me quedo con números
                    ->values();

                $nombres = $valores->filter(function ($v) {
                    $soloDigitos = preg_replace('/\D+/', '', (string)$v);
                    return $soloDigitos === '' || !ctype_digit($soloDigitos);
                })
                    ->map(fn($v) => trim((string)$v))
                    ->values();

                // 3) Busco empresas por CUIT (cast a varchar por si la columna es BIGINT)
                $query = EmpresasModel::query();

                if ($cuits->isNotEmpty()) {
                    $query->orWhereIn(DB::raw('CAST(cuit AS VARCHAR(20))'), $cuits->all());
                }

                if ($nombres->isNotEmpty()) {
                    // ajustá el campo según tu modelo: razon_social / nombre_fantasia
                    $query->orWhereIn('razon_social', $nombres->all());
                }

                $empresas = $query->get();

                // 4) Armo índices para resolver rápido por CUIT o por nombre
                $byCuit = $empresas->keyBy(function ($e) {
                    return preg_replace('/\D+/', '', (string)$e->cuit); // normalizado
                });

                $byNombre = $empresas->keyBy(function ($e) {
                    return trim((string)$e->razon_social);
                });

                // 5) Matcheo fila a fila
                foreach ($this->previewDataUbicacion as $index => $row) {
                    $raw = (string)($row['cuit_propietario'] ?? '');

                    $soloDigitos = preg_replace('/\D+/', '', $raw);
                    if ($soloDigitos !== '' && isset($byCuit[$soloDigitos])) {
                        // match por CUIT
                        $this->localEmpresa[$index] = (string)$byCuit[$soloDigitos]->cuit;
                        continue;
                    }

                    $nombre = trim($raw);
                    if ($nombre !== '' && isset($byNombre[$nombre])) {
                        // match por nombre
                        $this->localEmpresa[$index] = (string)$byNombre[$nombre]->cuit;
                        continue;
                    }

                    // sin match
                    $this->localEmpresa[$index] = '';
                }
            } else {
                // Verifica si el archivo tiene la columna 'cuit_propietario'
                $tieneColumnaClientes = collect($this->previewDataUbicacion)
                    ->filter(fn($fila) => array_key_exists('cuit_propietario', $fila))
                    ->isNotEmpty();

                if ($tieneColumnaClientes) {
                    $this->mostrarErrorImportacion('No se pueden cargar ubicaciones de clientes desde aquí. Solo se permiten ubicaciones propias.');
                    $this->limpiarPrevisualizacion();
                    return;
                }
            }
        } catch (Exception $e) {
            $this->addError('archivo', $e->getMessage());

            $this->dispatch('errorInfo', [
                'title'   => 'Error de Importación',
                'message' => $e->getMessage()
            ]);
            $this->limpiarPrevisualizacion();
        }
    }

    public function confirmarImportubicacionesPropias(FileImportService $importService)
    {
        if (empty($this->previewDataUbicacion)) {
            $this->mostrarErrorImportacion('No hay datos para importar.');
            $this->limpiarPrevisualizacion();
            return;
        }
        // Determinar el valor de propiedad según el tipo de datos seleccionado.
        // Por ejemplo: si es UbicacionesPropias, la propiedad es "Propio"; de lo contrario, "Cliente".
        $propiedad = $this->tipoDatos === 'UbicacionesPropias' ? 'Propio' : 'Cliente';

        $importService = $this->getUbicacionImporter();

        $resultado = $importService->confirmImportUbicacion(
            $this->previewDataUbicacion,
            $propiedad,
            $this->tipoOperacion ?? null,
            function ($errorMessage, $index) {
                $this->mostrarErrorImportacion($errorMessage);
            },
            $this->localTipoUbicacion,
            $this->globalTipoUbicacion,
            $this->localGestor ?? [],
            $this->globalGestor ?? '',
            $this->localPropietario ?? [],
            $this->globalPropietario ?? '',
            $this->local_multiples,
            $this->global_multiples,
            $this->localPiso,
            $this->localSubsuelo,
            $this->localEmpresa,
            $this->globalEmpresa,
        );

        if ($resultado === true) {
            $this->mostrarExitoImportacion('Los registros fueron importados exitosamente.');
            $this->closeCompleto();
        } elseif ($resultado === 'ubicacion_duplicada') {
            // Resetear todo solo si es ese caso
            $this->limpiarPrevisualizacion();
        }
    }

    private function normDigits(?string $v): string
    {
        return preg_replace('/\D+/', '', (string)$v);
    }

    private function normName(?string $v): string
    {
        $s = (string)$v;
        $s = \Normalizer::normalize($s, \Normalizer::FORM_D);      // requiere intl
        $s = preg_replace('/\p{Mn}+/u', '', $s);                   // quita acentos
        $s = mb_strtoupper($s, 'UTF-8');
        $s = preg_replace('/[^A-Z0-9 ]+/u', ' ', $s);              // deja letras, números y espacio
        $s = preg_replace('/\s+/u', ' ', trim($s));                // colapsa espacios
        return $s;
    }

    /**
     * Devuelve CUIT normalizado y un set de nombres normalizados de MI empresa.
     */
    private function misIdentidadesEmpresa(): array
    {
        $cuit = (string) IdHelper::idEmpresa();
        $cuitDigits = $this->normDigits($cuit);

        // Trae tu empresa para comparar por nombre también
        $miEmpresa = EmpresasModel::where(DB::raw('CAST(cuit AS VARCHAR(20))'), $cuitDigits)
            ->first(['cuit', 'razon_social']);

        $nombres = [];
        if ($miEmpresa) {
            if (!empty($miEmpresa->razon_social)) {
                $nombres[] = $this->normName($miEmpresa->razon_social);
            }
            if (!empty($miEmpresa->nombre_fantasia)) {
                $nombres[] = $this->normName($miEmpresa->nombre_fantasia);
            }
        }
        // evitá duplicados
        $nombres = array_values(array_unique(array_filter($nombres)));

        return [$cuitDigits, $nombres];
    }

    // Permite eliminar la previsualización de ubicaciones
    private function limpiarPrevisualizacion()
    {
        $this->previewDataUbicacion = [];
        $this->localTipoUbicacion = [];
        $this->localGestor = [];
        $this->globalTipoUbicacion = null;
        $this->globalGestor = null;
        $this->searchUbicaciones = '';
        $this->local_multiples = [];
        $this->global_multiples = '';
        $this->localPiso = [];
        $this->localSubsuelo = [];
        $this->localEmpresa = [];
    }
}
