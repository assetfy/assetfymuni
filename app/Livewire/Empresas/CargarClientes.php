<?php

namespace App\Livewire\Empresas;

use App\Helpers\IdHelper;
use App\Models\EmpresasModel;
use App\Models\User;
use App\Models\ClientesEmpresaModel;
use App\Models\ContratoClienteModel;
use App\Services\MiddlewareInvoker;
use App\Services\FileImport\GeoRefService;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\ControlesModel;

class CargarClientes extends Component
{
    // Listener para abrir el modal
    protected $listeners = ['CargarClientes'];

    public $open;
    public $terminoBusqueda, $mensajeBusqueda;
    public $nombre, $cuil, $email;
    public $razon_social, $cuit, $provincia, $localidad;
    public $tipo_clientes = 'empresa', $contrato; // 'individual' o 'empresa'
    public $mostrarAgregarFormulario = false;
    public $empresaEncontrada = false;
    public $contratos = [];
    public $ultClienteId;

    protected ?GeoRefService $geoRef = null;

    //provincias
    public array   $allProvincias   = [];    // todas, sin filtrar
    public array   $provincias      = [];    // lista que muestra el dropdown
    public string  $searchProvincia = '';
    //localidad
    public array  $allLocalidades    = [];
    public array  $localidades       = [];
    public string $searchLocalidad   = '';

    // Array para almacenar formularios manuales
    public $clientesManual = [];

    // Ejemplo de valor por defecto para la empresa principal
    public $defaultEmpresaCuit;

    public function CargarClientes()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        $this->open = true;
        $this->resetSearchState();
        $this->initGeoRef();
    }

    protected function initGeoRef(): void
    {
        // si ya está, no vuelve a instanciar
        if ($this->geoRef) {
            return;
        }

        $this->geoRef         = app(GeoRefService::class);
        $this->allProvincias  = $this->geoRef->provincias();
        $this->provincias     = $this->allProvincias;
    }

    public function updatedSearchProvincia(string $value)
    {
        // filtrar en memoria sin volver a la API
        $this->provincias = $value === ''
            ? $this->allProvincias
            : collect($this->allProvincias)
            ->filter(
                fn($p) =>
                Str::contains(Str::lower($p['nombre']), Str::lower($value))
            )
            ->values()
            ->toArray();
    }

    public function setProvincia(int $index, string $id)
    {
        // buscás la provincia en la lista cargada en memoria
        $prov = collect($this->allProvincias)->firstWhere('id', $id);
        if (! $prov) return;

        // guardás el nombre en el formulario
        $this->clientesManual[$index]['provincia'] = $prov['nombre'];

        // reset filtro provincias
        $this->searchProvincia = '';
        $this->provincias     = $this->allProvincias;

        // instanciás el servicio *al vuelo*, sin usar la propiedad
        $service = app(\App\Services\FileImport\GeoRefService::class);
        $this->allLocalidades = $service->localidades($prov['id']);
        $this->localidades    = $this->allLocalidades;

        // limpio campo localidad previo
        $this->clientesManual[$index]['localidad'] = '';
        $this->searchLocalidad                      = '';
    }


    public function updatedSearchLocalidad(string $value)
    {
        $this->localidades = $value === ''
            ? $this->allLocalidades
            : collect($this->allLocalidades)
            ->filter(
                fn($l) =>
                Str::contains(Str::lower($l['nombre']), Str::lower($value))
            )
            ->values()
            ->toArray();
    }

    public function setLocalidad(int $index, string $locId)
    {
        $loc = collect($this->allLocalidades)->firstWhere('id', $locId);
        if (! $loc) {
            return;
        }

        $this->clientesManual[$index]['localidad'] = $loc['nombre'];

        // reset filtro localidades
        $this->searchLocalidad = '';
        $this->localidades     = $this->allLocalidades;
    }

    public function resetSearchState()
    {
        $this->searchProvincia = '';
        $this->searchLocalidad = '';
        $this->terminoBusqueda = '';
        $this->mensajeBusqueda = '';
        $this->nombre = '';
        $this->cuil = '';
        $this->email = '';
        $this->razon_social = '';
        $this->cuit = '';
    }

    public function buscarUsuarios()
    {
        $this->validate(['terminoBusqueda' => 'required|string']);

        $usuario = User::where('name', 'like', '%' . $this->terminoBusqueda . '%')
            ->orWhere('email', 'like', '%' . $this->terminoBusqueda . '%')
            ->first();

        if ($usuario) {
            $this->mensajeBusqueda = 'Usuario encontrado';
            $this->nombre = $usuario->name;
            $this->cuil = $usuario->cuil;
            $this->email = $usuario->email;
        } else {
            $this->mensajeBusqueda = 'Usuario no encontrado';
            $this->nombre = '';
            $this->cuil = '';
            $this->email = '';
        }
    }

    public function buscarEmpresa()
    {
        $this->resetValidation();
        $this->mensajeBusqueda   = '';
        $this->razon_social      = '';
        $this->cuit              = '';
        $this->provincia         = '';
        $this->localidad         = '';
        $this->empresaEncontrada = false;

        $this->validate(['terminoBusqueda' => 'required|string']);

        $empresa = EmpresasModel::where('tipo', 1)
            ->where(function ($q) {
                $q->where('razon_social', 'like', '%' . $this->terminoBusqueda . '%');
                if (is_numeric($this->terminoBusqueda)) {
                    $q->orWhere('cuit', $this->terminoBusqueda);
                }
            })
            ->first();

        if ($empresa) {
            $this->empresaEncontrada = true;
            $this->mensajeBusqueda   = '¡Empresa encontrada en la plataforma!';
            $this->razon_social      = $empresa->razon_social;
            $this->cuit              = $empresa->cuit;
            $this->provincia         = $empresa->provincia;
            $this->localidad         = $empresa->localidad;
        } else {
            $this->mensajeBusqueda   = 'No se ha encontrado ninguna empresa con ese nombre o CUIT.';
        }
    }
    // Al activar el formulario manual, se agrega un formulario por defecto si aún no existe
    public function activarFormularioManual()
    {
        $this->mostrarAgregarFormulario = true;
        if (empty($this->clientesManual)) {
            if ($this->tipo_clientes === 'individual') {
                $this->clientesManual[] = [
                    'nombre' => '',
                    'cuil'   => '',
                    'email'  => '',
                ];
            } elseif ($this->tipo_clientes === 'empresa') {
                $this->clientesManual[] = [
                    'razon_social' => '',
                    'cuit'         => '',
                    'provincia'    => '',
                    'localidad'    => '',
                ];
            }
        }
    }

    // Método para agregar otro formulario manual
    public function agregarOtroCliente()
    {
        if ($this->tipo_clientes === 'individual') {
            $this->clientesManual[] = [
                'nombre' => '',
                'cuil'   => '',
                'email'  => '',
            ];
        } elseif ($this->tipo_clientes === 'empresa') {
            $this->clientesManual[] = [
                'razon_social' => '',
                'cuit'         => '',
                'provincia'    => '',
                'localidad'    => '',
            ];
        }
    }

    public function guardar()
    {
        $this->defaultEmpresaCuit = IdHelper::idEmpresa();

        DB::beginTransaction();

        try {
            if ($this->mostrarAgregarFormulario) {
                $this->guardarFormulariosManuales();
                $this->guardarContratos($this->ultClienteId);
            } else {
                $this->guardarPorBusqueda();
            }

            DB::commit();

            $this->dispatch('Exito', [
                'title'   => 'Éxito',
                'message' => 'Clientes guardados correctamente.'
            ]);
            $this->dispatch('refreshLivewireTable');
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('errorInfo', [
                'title'   => 'Error al guardar',
                'message' => $e->getMessage(),
            ]);
            return;
        }

        $this->cerrar();
    }

    private function guardarFormulariosManuales()
    {
        if (empty($this->clientesManual)) {
            return;
        }
        if ($this->tipo_clientes === 'individual') {
            $this->clienteCargaManual();
        } elseif ($this->tipo_clientes === 'empresa') {
            $this->empresaCargaManual();
        }
    }

    private function guardarPorBusqueda()
    {
        if (empty($this->mensajeBusqueda)) {
            return;
        }
        if ($this->tipo_clientes === 'individual') {
            $this->clienteCargaManualBusqueda();
        } elseif ($this->tipo_clientes === 'empresa') {
            $this->empresaCargaManualBusqueda();
        }
    }

    private function clienteCargaManualBusqueda()
    {
        // Verificar duplicado en búsqueda para cliente individual
        if (ClientesEmpresaModel::where('empresa_cuit', $this->defaultEmpresaCuit)
            ->where('cuil', $this->cuil)
            ->exists()
        )
            $this->validate([
                'nombre' => 'required|string',
                'cuil'   => 'required|numeric',
                'email'  => 'required|email',
            ]);

        // Si el usuario existe en el sistema, se marca como verificado = "Si"
        $verificado = 'No';
        if (\App\Models\User::where('cuil', $this->cuil)->exists()) {
            $verificado = 'Si';
        }

        $numero_contrato = !empty($this->contrato)
            ? $this->contrato
            : $this->defaultEmpresaCuit . '-' . uniqid();

        ClientesEmpresaModel::create([
            'empresa_cuit'    => $this->defaultEmpresaCuit,
            'verificado'      => $verificado,
            'cliente_cuit'    => null,
            'cuil'            => $this->cuil,
            'numero_cliente' => $numero_contrato,
        ]);
    }

    private function empresaCargaManualBusqueda()
    {
        if (ClientesEmpresaModel::where('empresa_cuit', $this->defaultEmpresaCuit)
            ->where('cliente_cuit', $this->cuit)
            ->exists()
        )
            $this->validate([
                'razon_social' => 'required|string',
                'cuit'         => 'required|numeric',
                'provincia'    => 'required|string',
                'localidad'    => 'required|string',
            ]);

        // Si la empresa existe en el sistema, se marca como verificado = "Si"
        $verificado = 'No';
        if (\App\Models\EmpresasModel::where('cuit', $this->cuit)->exists()) {
            $verificado = 'Si';
        }

        $numero_contrato = !empty($this->contrato)
            ? $this->contrato
            : $this->defaultEmpresaCuit . '-' . uniqid();

        ClientesEmpresaModel::create([
            'empresa_cuit'    => $this->defaultEmpresaCuit,
            'verificado'      => $verificado,
            'cliente_cuit'    => $this->cuit,
            'cuil'            => null,
            'numero_cliente' => $numero_contrato,
        ]);
    }

    private function clienteCargaManual()
    {
        foreach ($this->clientesManual as $index => $cliente) {
            $this->validate([
                "clientesManual.$index.nombre" => 'required|string',
                "clientesManual.$index.cuil"   => 'required|numeric',
                "clientesManual.$index.email"  => 'required|email',
            ]);

            // Verificar duplicado en ClientesEmpresaModel para cliente individual
            if (ClientesEmpresaModel::where('empresa_cuit', $this->defaultEmpresaCuit)
                ->where('cuil', $cliente['cuil'])
                ->exists()
            ) {
                throw new \Exception('El cliente individual ya existe.');
            }

            // Si el usuario existe en el sistema, se marca como verificado = "Si"
            $verificado = 'No';
            if (\App\Models\User::where('cuil', $cliente['cuil'])->exists()) {
                $verificado = 'Si';
            }

            // Si no se ingresó contrato, se genera uno único
            $numero_contrato = !empty($cliente['contrato'])
                ? $cliente['contrato']
                : $this->defaultEmpresaCuit . '-' . uniqid();

            ClientesEmpresaModel::create([
                'empresa_cuit'    => $this->defaultEmpresaCuit,
                'verificado'      => $verificado,
                'cliente_cuit'    => null,
                'cuil'            => $cliente['cuil'],
                'numero_cliente' => $numero_contrato,
            ]);
        }
    }

    private function empresaCargaManual()
    {
        foreach ($this->clientesManual as $index => $cliente) {
            $this->validate([
                "clientesManual.$index.razon_social" => 'required|string',
                "clientesManual.$index.cuit"         => 'required|numeric',
                "clientesManual.$index.provincia"    => 'required|string',
                "clientesManual.$index.localidad"    => 'required|string',
            ]);

            // Si la empresa existe en el sistema, se marca como verificado = "Si"
            $existeEmpresa = \App\Models\EmpresasModel::where('cuit', $cliente['cuit'])->exists();
            $verificado    = $existeEmpresa ? 'Si' : 'No';

            // 1) Si la empresa NO existe, la creamos
            if (!EmpresasModel::where('cuit', $cliente['cuit'])->exists()) {
                EmpresasModel::create([
                    'razon_social' => $cliente['razon_social'],
                    'cuit'         => $cliente['cuit'],
                    'provincia'    => $cliente['provincia'],
                    'localidad'    => $cliente['localidad'],
                    'tipo'         => 1,
                    'estado'       => 1,
                ]);
            }

            // 2) Duplicado en favoritos
            if (ClientesEmpresaModel::where('empresa_cuit', $this->defaultEmpresaCuit)
                ->where('cliente_cuit', $cliente['cuit'])
                ->exists()
            ) {
                throw new \Exception('La empresa ya fue cargada.');
            }

            // 3) Crear relación
            $numero_contrato = !empty($cliente['contrato'])
                ? $cliente['contrato']
                : $this->defaultEmpresaCuit . '-' . uniqid();

            $registro = ClientesEmpresaModel::create([
                'empresa_cuit'    => $this->defaultEmpresaCuit,
                'verificado'      => $verificado,
                'cliente_cuit'    => $cliente['cuit'],
                'cuil'            => null,
                'numero_cliente' => $numero_contrato,
            ]);
            $this->ultClienteId = $registro->id_clientes_empresa;
        }
    }

    public function cerrar()
    {
        $this->reset([
            'mostrarAgregarFormulario',
            'terminoBusqueda',
            'clientesManual',
            'mensajeBusqueda',
            'nombre',
            'cuil',
            'email',
            'razon_social',
            'cuit',
            'provincia',
            'localidad',
            'contrato',
            'tipo_clientes',
        ]);
        $this->open = false;
    }

    public function mostrarBuscador()
    {
        $this->mostrarAgregarFormulario = false;
        $this->clientesManual = [];
        $this->resetSearchState();
    }

    public function render()
    {
        return view('livewire.empresas.cargar-clientes');
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName, [
            'clientesManual.*.nombre'       => 'required|string',
            'clientesManual.*.cuil'         => 'required|numeric',
            'clientesManual.*.email'        => 'required|email',
            'clientesManual.*.razon_social' => 'required|string',
            'clientesManual.*.cuit'         => 'required|numeric',
            'clientesManual.*.provincia'    => 'required|string',
            'clientesManual.*.localidad'    => 'required|string',
        ]);
    }

    // Agregar un nuevo contrato vacío
    public function agregarContrato()
    {
        $this->contratos[] = ['numero' => ''];
    }
    // Eliminar el contrato en posición $index
    public function eliminarContrato(int $index)
    {
        unset($this->contratos[$index]);
        // reindexar para que los índices queden consecutivos
        $this->contratos = array_values($this->contratos);
    }
    // Y en tu método de guardado, justo después de crear el cliente/empresa:
    private function guardarContratos(int $idClienteEmpresa)
    {
        foreach ($this->contratos as $c) {
            if (! empty($c['numero'])) {
                ContratoClienteModel::create([
                    'id_clientes_empresa' => $idClienteEmpresa,
                    'contrato'            => $c['numero'],
                ]);
            }
        }
    }
}
