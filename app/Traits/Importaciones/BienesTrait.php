<?php

namespace App\Traits\Importaciones;

use Exception;
use App\Services\FileImport\FileImportService;
use App\Models\EmpresasModel;
use App\Services\FileImport\FileBienes;
use App\Helpers\IdHelper;
use App\Models\UbicacionesModel;

trait BienesTrait
{
    public $ubicacionCache;

    public function descargarEjemploActivo()
    {
        $this->descargarEjemplo('BienesArchivoEjemplo.xlsx', 'BienesArchivoEjemplo.xlsx');
    }

    public function descargarEjemploBienCliente()
    {
        $this->descargarEjemplo('BienesClientesArchivoEjemplo.xlsx', 'BienesClientesArchivoEjemplo.xlsx');
    }

    public function abrirModalDependencia(array $payload)
    {
        return $this->abrirDependencia($payload);
    }

    public function importarArchivoActivo()
    {
        // Verificar que se haya seleccionado un archivo
        if (!$this->validarArchivoSeleccionado()) return;

        try {

            $tipoOperacion = (string) ($this->tipoOperacion ?? '');

            if (trim($tipoOperacion) === 'Actualizar') {
                $this->dispatch('errorInfo', [
                    'title'   => 'Error de Importación',
                    'message' => 'Opción no disponible. En construcción.'
                ]);
                $this->limpiarPrevisualizacionActivo();
                return;
            }

            $requiredKeys = ['Nombre', 'Tipo', 'Categoria', 'Subcategoria', 'Estado General', 'Estado Alta'];

            $importService = new FileBienes($this->tipoDatos);

            $this->previewDataActivo = $importService->importFileActivo($this->archivo, $requiredKeys);

            // Si no se obtuvieron registros revisa 
            if (empty($this->previewDataActivo)) {
                $this->addError('archivo', 'El archivo no contiene registros válidos o faltan claves requeridas.');
                return;
            }

            if ($this->tipoDatos === 'Bienes') {
                // Obtener los CUITs únicos de las empresas titulares cargadas en el archivo
                $empresasTitulares = collect($this->previewDataActivo)
                    ->pluck('cuit_propietario') // Asegúrate de que coincida con el nombre de la columna en el archivo
                    ->map(function ($value) {
                        return trim($value); // Eliminar espacios en blanco
                    })
                    ->unique()
                    ->filter()
                    ->toArray();

                // dd($empresasTitulares);
                // Separar CUITs (números) y razones sociales (texto)
                $cuitList = array_filter($empresasTitulares, 'is_numeric'); // Solo valores numéricos
                $razonSocialList = array_filter($empresasTitulares, function ($value) {
                    return !is_numeric($value); // Solo valores que no sean números
                });

                // Buscar empresas en la tabla empresas_o_particulares
                $empresasEncontradas = EmpresasModel::where(function ($query) use ($cuitList, $razonSocialList) {
                    if (!empty($cuitList)) {
                        $query->whereIn('cuit', $cuitList);
                    }
                    if (!empty($razonSocialList)) {
                        $query->orWhereIn('razon_social', $razonSocialList);
                    }
                })->where('estado', 'Aceptado')->get();

                // Obtener el CUIT de la empresa del usuario autenticado y convertirlo a string
                $idEmpresaUsuario = (string) IdHelper::idEmpresa();

                // Verificar si alguna de las empresas encontradas pertenece al usuario
                $empresaUsuarioExiste = $empresasEncontradas->contains(function ($empresa) use ($idEmpresaUsuario) {
                    return (string) $empresa->cuit === $idEmpresaUsuario;
                });

                // Verificar si alguna de las empresas encontradas no le ha delegado ubicaciones
                $empresaUbicacionesDelegadas = $empresasEncontradas->contains(function ($empresa) use ($idEmpresaUsuario) {
                    return UbicacionesModel::where('cuit', $empresa->cuit)
                        ->where('cuit_empresa', $idEmpresaUsuario)
                        ->exists();
                });

                if ($empresaUsuarioExiste) {
                    $this->dispatch('errorInfo', [
                        'title'   => 'Error de Importación',
                        'message' => 'Aquí no puedes cargar bienes de tu propia empresa. Solo se permiten bienes de empresas externas.'
                    ]);
                    $this->limpiarPrevisualizacionActivo();
                    return;
                } else {

                    // Filtrar ubicaciones solo de esas empresas titulares que correspondan al usuario
                    $this->ubicaciones = UbicacionesModel::whereIn('cuit', $empresasEncontradas->pluck('cuit')->toArray())
                        ->where('cuit_empresa', $idEmpresaUsuario)
                        ->get();
                }

                // Si no se han delegado ubicaciones de alguna de las empresas, entonces se mostrará un mensaje de error
                if (!$empresaUbicacionesDelegadas) {
                    $this->dispatch('errorInfo', [
                        'title'   => 'Error de Importación',
                        'message' => 'No puedes cargar bienes de una empresa externa, sin tener sus ubicaciones delegadas.'
                    ]);
                    $this->limpiarPrevisualizacionActivo();
                    return;
                }
            } else {
                // Verifica si el archivo tiene la columna 'cuit_propietario'
                $tieneColumnaClientes = collect($this->previewDataActivo)
                    ->filter(fn($fila) => array_key_exists('cuit_propietario', $fila))
                    ->isNotEmpty();

                if ($tieneColumnaClientes) {
                    $this->mostrarErrorImportacion('No se pueden cargar bienes de clientes desde aquí. Solo se permiten bienes propios.');
                    $this->limpiarPrevisualizacionActivo();
                    return;
                }

                $idEmpresaUsuario = (int) IdHelper::idEmpresa();

                $this->ubicaciones = UbicacionesModel::where('cuit', $idEmpresaUsuario)
                    ->get();

                // Mapa Id => Nombre que usará la vista
                $mapNiveles = \App\Models\OrganizacionUnidadesModel::where('CuitEmpresa', $idEmpresaUsuario)
                    ->pluck('Nombre', 'Id')   // ['Id' => 'Nombre']
                    ->toArray();

                $this->nivelesEmpresa = $mapNiveles;      // <-- esto es lo que “llega” a la vista
                $idsValidos = array_map('intval', array_keys($mapNiveles)); // solo IDs para validar

                // Normalizar cada fila de la vista previa
                foreach ($this->previewDataActivo as &$row) {
                    $id = isset($row['depende_de']) ? (int) $row['depende_de'] : null;
                    $row['depende_de'] = ($id && in_array($id, $idsValidos, true)) ? $id : null;
                }
                unset($row);
            }

            // Aquí llamamos a un método que setee las subcategorías locales
            $this->actualizarLocalesActivos();
            $this->resetErrorBag('archivo');
        } catch (Exception $e) {
            $this->addError('archivo', $e->getMessage());

            $this->dispatch('errorInfo', [
                'title'   => 'Error de Importación',
                'message' => $e->getMessage()
            ]);

            $this->limpiarPrevisualizacionActivo();
        }
    }

    public function confirmarImporActivo(FileImportService $importService)
    {
        if (empty($this->previewDataActivo)) {
            $this->mostrarErrorImportacion('No hay datos para importar.');
            $this->limpiarPrevisualizacionActivo();
            return;
        }

        $propiedad = $this->tipoDatos === 'Activos' ? 'Propio' : 'Cliente';

        $importService = new FileBienes($this->tipoDatos);

        $resultado = $importService->confirmImportActivo(
            $this->previewDataActivo,       // Datos normalizados del archivo para activos
            $propiedad,
            $this->localSubcategoria,       // Valores locales para Subcategoría
            $this->globalSubcategoria,      // Valor global para Subcategoría
            $this->localCategoria,          // Valores locales para Categoría
            $this->globalCategoria,         // Valor global para Categoría
            $this->localTipo,               // Valores locales para Tipo
            $this->globalTipo,              // Valor global para Tipo
            $this->localSitAlta,            // Valores locales para Estado Sit Alta
            $this->globalSitAlta,           // Valor global para Estado Sit Alta
            $this->localSitGeneral,      // Valores locales para Estado Sit General
            $this->globalSitGeneral,     // Valor global para Estado Sit General
            $this->localCondicion,
            $this->globalCondicion,         // Valor global para Condición
            $this->localMarca,             // Valores locales para Marca
            $this->globalMarca,            // Valor global para Marca
            $this->localModelo,            // Valores locales para Modelo
            $this->globalModelo,           // Valor global para Modelo
            $this->atributos,          // Valores locales para Atributos (por fila)
            $this->localUbicacion,          // Valores locales para Ubicación
            $this->globalUbicacion,         // Valor global para Ubicación
            $this->localGestores,           // Valores locales para Gestor (por fila)
            $this->globalGestores,          // Valor global para Gestor
            $this->localResponsable,        // Valores locales para Responsable (por fila)
            $this->globalResponsable,       // Valor global para Responsable
            $this->localEmpresa,
            $this->globalEmpresa,
            $this->localPropietario ?? [],
            $this->globalPropietario ?? '',
            $this->tipoOperacion,

            function ($errorMessage, $index) {
                // Callback para manejar errores
                $this->mostrarErrorImportacion($errorMessage);
            }
        );

        if ($resultado) {
            $this->mostrarExitoImportacion('Los registros fueron importados exitosamente.');
            $this->closeCompleto();
        }
    }

    private function limpiarPrevisualizacionActivo()
    {
        $this->previewDataActivo = [];
        $tipoOperacion = null;
    }
}
