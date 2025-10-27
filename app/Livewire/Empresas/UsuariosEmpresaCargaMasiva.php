<?php

namespace App\Livewire\Empresas;

use App\Models\EmpresasModel;
use App\Services\FileImportService;
use Exception;
use Livewire\Component;
use Illuminate\Pagination\LengthAwarePaginator;
use Livewire\WithPagination;
use Livewire\WithFileUploads;

class UsuariosEmpresaCargaMasiva extends Component
{
    use WithPagination;
    use WithFileUploads;

    protected $listeners = [
        'updateAllLocalTipoUsuario',
        'updateAllLocalRepTecnico',
    ];

    // Datos importados y archivo
    public $vistaPreviaUsuario;
    public $archivo, $empresas, $selectedEmpresaNombre, $cuit, $searchEmpresa;
    public $tipoDatos = 1;
    public $tipoOperacion = 1;

    // Para manejar la selección de Empresa (columna CUIT)
    public $globalEmpresa = '';
    public $localEmpresa = [];

    // Si falta la columna "cuit" en el archivo
    public $missingCuit = false;
    public $columnsForCuitSelection = [];
    public $selectedCuitColumn = '';

    // Para Tipo Usuario
    public $Global_tipo_usario = ''; // Valor global para Tipo Usuario
    public $local_tipo_usario = [];  // Array local para cada registro

    // Para Representante Técnico
    public $representante_tecnico_global = ''; // Valor global para Representante Técnico
    public $representante_tecnico_local = [];  // Array local para cada registro

    // Variables para paginación y búsqueda global en la previsualización (Usuarios)
    public $searchUsuarios = '';
    public $pageUsuarios = 1;

    protected $queryString = [
        'pageUsuarios'  => ['except' => 1],
        'searchUsuarios' => ['except' => ''],
        // Si conservas otras variables para ubicaciones, activos, etc., puedes seguir teniéndolas
    ];

    public function setPage($page, $pageName = 'page')
    {
        if ($pageName === 'pageUsuarios') {
            $this->pageUsuarios = $page;
        }
    }

    public function mount()
    {
        $this->empresas = EmpresasModel::all();
        // Inicializamos los valores globales como vacíos para mostrar "Seleccione una opción"
        $this->Global_tipo_usario = '';
        $this->representante_tecnico_global = '';
        $this->globalEmpresa = '';
    }

    /**
     * Actualiza todos los locales de Tipo Usuario al valor global.
     */
    public function updateAllLocalTipoUsuario($value)
    {
        if (!empty($this->vistaPreviaUsuario)) {
            foreach ($this->vistaPreviaUsuario as $index => $row) {
                $this->local_tipo_usario[$index] = $value;
            }
        }
    }

    /**
     * Actualiza todos los locales de Representante Técnico al valor global.
     */
    public function updateAllLocalRepTecnico($value)
    {
        if (!empty($this->vistaPreviaUsuario)) {
            foreach ($this->vistaPreviaUsuario as $index => $row) {
                $this->representante_tecnico_local[$index] = $value;
            }
        }
    }
    public function updatedGlobalEmpresa($value)
    {
        if (!empty($this->vistaPreviaUsuario)) {
            foreach ($this->vistaPreviaUsuario as $index => $row) {
                if (isset($this->localEmpresa[$index]) && $this->localEmpresa[$index] !== '') {
                    $this->localEmpresa[$index] = $value;
                }
            }
        }
    }


    public function setEmpresa($cuit)
    {
        $empresa = EmpresasModel::find($cuit);
        $this->selectedEmpresaNombre = $empresa ? $empresa->nombre : null;
        $this->cuit = $cuit;
        $this->searchEmpresa = '';
        $this->dispatch('closeDropdown', ['dropdown' => 'empresa']);
    }

    /**
     * Importa y normaliza el archivo de usuarios.
     */
    public function importarArchivoUsuario(FileImportService $importService)
    {
        if (!$this->archivo) {
            $this->addError('archivo', 'No se ha seleccionado ningún archivo.');
            return;
        }
        try {
            $requiredKeys = [
                'name',
                'email',
                'password',
                'cuil'
            ];
            $this->vistaPreviaUsuario = $importService->importFileUsuario($this->archivo, $requiredKeys);
            if (empty($this->vistaPreviaUsuario)) {
                $this->addError('archivo', 'El archivo no contiene registros válidos o faltan claves requeridas.');
                return;
            }

            foreach ($this->vistaPreviaUsuario as $index => $row) {
                $cuitFromFile = $row['cuit'] ?? '';
                // Verificamos si existe la empresa con el cuit del documento
                if (!empty($cuitFromFile) && EmpresasModel::find($cuitFromFile)) {
                    $this->localEmpresa[$index] = $cuitFromFile;
                } else {
                    $this->localEmpresa[$index] = '';
                }
                $this->local_tipo_usario[$index] = '';
                $this->representante_tecnico_local[$index] = '';
            }


            $firstRecord = reset($this->vistaPreviaUsuario);
            if (!array_key_exists('cuit', $firstRecord)) {
                $this->missingCuit = true;
                $this->columnsForCuitSelection = array_keys($firstRecord);
                return;
            }
            $this->resetErrorBag('archivo');
        } catch (Exception $e) {
            $this->addError('archivo', $e->getMessage());
            $this->dispatch('errorInfo', [
                'title'   => 'Error de Importación',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function setCuitColumn()
    {
        if ($this->selectedCuitColumn && !empty($this->vistaPreviaUsuario)) {
            foreach ($this->vistaPreviaUsuario as &$row) {
                $row['cuit'] = $row[$this->selectedCuitColumn] ?? null;
            }
            $this->missingCuit = false;
            $this->selectedCuitColumn = '';
        }
    }

    public function updatingSearchUsuarios()
    {
        $this->pageUsuarios = 1;
    }

    public function render()
    {
        $perPage = 50;
        $dataUsuarios = collect($this->vistaPreviaUsuario);
        if ($this->searchUsuarios) {
            $dataUsuarios = $dataUsuarios->filter(function ($item) {
                return stripos($item['razon_social'] ?? '', $this->searchUsuarios) !== false;
            });
        }
        $paginatedUsuarios = new LengthAwarePaginator(
            $dataUsuarios->forPage($this->pageUsuarios, $perPage),
            $dataUsuarios->count(),
            $perPage,
            $this->pageUsuarios,
            [
                'pageName' => 'pageUsuarios',
                'path' => url()->current(),
            ]
        );

        return view('livewire.empresas.usuarios-empresa-carga-masiva', [
            'paginatedUsuarios' => $paginatedUsuarios,
        ]);
    }

    // Otros métodos de confirmación y descarga se mantienen...
    public function confirmarUsuarios(FileImportService $importService)
    {
        if (empty($this->vistaPreviaUsuario)) {
            $this->dispatch('errorCreacion', [
                'title'   => 'Error de Importación',
                'message' => 'No hay datos para importar.'
            ]);
            return;
        }

        $resultado = $importService->confirmarUsuarios(
            $this->vistaPreviaUsuario,
            $this->local_tipo_usario,
            $this->Global_tipo_usario,
            $this->representante_tecnico_local,
            $this->representante_tecnico_global,
            $this->localEmpresa,
            $this->globalEmpresa,
            $this->tipoOperacion,
            function ($errorMessage, $index) {
                $this->dispatch('errorInfo', [
                    'title'   => 'Error de Importación',
                    'message' => $errorMessage
                ]);
            }
        );

        if ($resultado) {
            $this->dispatch('Exito', [
                'title'   => 'Importación Exitosa',
                'message' => 'Los registros fueron importados a la Bd.'
            ]);
            $this->vistaPreviaUsuario = [];
        }

        $this->close();
    }

    public function close()
    {
        $this->reset([
            'archivo',
            'tipoDatos',
            'vistaPreviaUsuario',
            // Otras propiedades que necesites resetear...
        ]);
    }


    public function updatedTipoDatos($value)
    {
        // Reinicia la previsualización de datos
        $this->reset(['vistaPreviaUsuario', 'archivo', 'tipoOperacion', 'representante_tecnico_global', 'Global_tipo_usario', 'globalEmpresa', 'tipoOperacion']);
    }

    public function descargarEjemploUsuarios()
    {
        // Ruta completa en el servidor
        $filePath = public_path('storage/EjemploDescarga/UsuarioArchivoEjemplo.xlsx');
        if (!file_exists($filePath)) {
            $this->dispatch('error', ['message' => 'El archivo de ejemplo no existe.']);
            return;
        }
        // Genera la URL pública del archivo
        $url = asset('storage/EjemploDescarga/UsuarioArchivoEjemplo.xlsx');

        // Despacha el evento con la URL y el nombre del archivo
        $this->dispatch('download-template', [
            'url'      => $url,
            'filename' => 'UsuarioArchivoEjemplo.xlsx'
        ]);
    }
}
