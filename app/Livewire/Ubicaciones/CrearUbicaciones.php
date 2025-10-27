<?php

namespace App\Livewire\Ubicaciones;

use App\Models\TiposUbicacionesModel;
use Illuminate\Support\Facades\Http;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\DB;
use App\Traits\VerificacionTrait;
use App\Models\UbicacionesModel;
use Illuminate\Support\Facades\Cache;
use App\Traits\SortableTrait;
use App\Helpers\IdHelper;
use App\Models\ClientesEmpresaModel;
use App\Models\EmpresasModel;
use App\Models\User;
use App\Models\UsuariosEmpresasModel;
use Livewire\Component;
use Livewire\WithFileUploads;
use Exception;
use Carbon\Carbon;

class CrearUbicaciones extends Component
{
    use VerificacionTrait;
    use SortableTrait;
    use WithFileUploads;
    public $modo = 'single'; // 'single' para carga única, 'bulk' para carga masiva
    public $open = false;
    public $openUbicacion = false;
    public $nombre, $pais, $provincia, $ciudad, $codigo_postal, $calle, $altura, $piso, $depto, $lat, $long, $propiedad, $tipo, $existe, $cuit, $cuil, $tiposUbicacion,
        $panel_actual, $selectedCuil, $cuilGestor, $searchCuil, $subsuelo;
    public $origen = 'default';
    public $propietario, $cuit_empresa, $cuil_gestor, $user, $searchGestionado;
    public $empleadosLista, $noEmpleadosEncontrados, $clientesLista, $noClientesEncontrados, $searchClientes, $selectedCuit, $fecha_carga, $MultiplePiso;

    protected $listeners = ['crearUbicacion' => 'abrirModalUbicacion', 'crearUbicacionDelegada'];

    protected function rules()
    {
        $rules = [
            'nombre' => 'required|max:30',
            'pais' => 'required|max:20',
            'provincia' => 'required',
            'ciudad' => 'required|max:20',
            'codigo_postal' => 'required',
            'calle' => 'required|max:200',
            'altura' => 'required|max:20',
            'tipo' => 'required',
            'lat' => 'required',
            'long' => 'required',
            'piso' => 'nullable|integer|min:1',
        ];

        if ($this->origen == 'ubicaciones_delegadas') {
            $rules['cuil_gestor'] = 'nullable';
            $rules['cuit_empresa'] = 'required';
        }

        if ($this->MultiplePiso) {
            $rules['piso'] = 'required|integer|min:1';
        }

        return $rules;
    }

    protected $messages = [
        'tipo.required'      => 'El campo Tipo de Ubicación es obligatorio.',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    /**
     * Guarda la ubicación mediante la creación del registro.
     */
    public function save()
    {
        $this->validate();

        DB::beginTransaction();

        try {
            if (!$this->existe) {
                $this->crearRegistro();

                DB::commit();

                $this->dispatch('ubicacionCreacion', ['message' => 'La ubicación se creó correctamente']);
            }
        } catch (Exception $e) {
            DB::rollBack();

            $this->dispatch('warning', ['message' => 'Ocurrió un error al crear la ubicación.']);
        }

        $this->close();
    }

    private function crearRegistro()
    {
        $identificadores = IdHelper::identificadorParcial();
        $this->cuit = $identificadores['cuit'];
        $this->cuil = $identificadores['user'];

        if ($this->origen == 'ubicaciones_delegadas') {
            $empresaFinal = $this->cuit_empresa !== $this->cuit ? $this->cuit_empresa : $this->cuit;
            $this->propietario = 'Cliente';
            $this->cuit_empresa = $this->cuit;
        } else {
            $empresaFinal = $this->cuit;
            $this->propietario = 'Propio';
        }

        $ubicacion = UbicacionesModel::create([
            'nombre'        => $this->nombre,
            'pais'          => $this->pais,
            'provincia'     => $this->provincia,
            'ciudad'        => $this->ciudad,
            'codigo_postal' => $this->codigo_postal,
            'calle'         => $this->calle,
            'altura'        => $this->altura,
            'piso'          => $this->piso,
            'depto'         => $this->depto,
            'cuil'          => $this->cuil ?? null,
            'cuit'          => $empresaFinal ?? null,
            'propiedad'     => $this->propietario,
            'tipo'          => $this->tipo,
            'lat'           => $this->lat,
            'long'          => $this->long,
            'cuit_empresa'  => $this->cuit_empresa ?? null,
            'cuil_gestor'   => $this->cuil_gestor ?? null,
            'fecha_carga'   => Carbon::parse($this->fecha_carga)->format('Y-m-d H:i:s'),
            'multipisos'    => $this->MultiplePiso ?? 0,
            'subsuelo'     => $this->subsuelo ?? null,
        ]);

        $this->dispatch('ubicacionCreada', $ubicacion->id_ubicacion, $ubicacion->nombre);
    }

    private function existeUbicacion()
    {
        if ($this->cuit) {
            $this->existe = UbicacionesModel::where('cuit', $this->cuit)
                ->where('nombre', $this->nombre)
                ->exists();
        } else {
            $this->existe = UbicacionesModel::where('cuil', $this->cuil)
                ->where('nombre', $this->nombre)
                ->exists();
        }
    }

    public function setAddress($lat, $long)
    {
        try {
            $apiKey = config('services.google_maps.api_key');

            // Construir la URL para la geocodificación inversa
            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$long}&key={$apiKey}";

            // Realizar la consulta a Google Maps
            $response = Http::withOptions(['verify' => false])->get($url);

            if ($response->successful()) {
                $results = $response->json('results');
                if (!empty($results)) {
                    // Tomamos el primer resultado
                    $result = $results[0];
                    $components = $result['address_components'];

                    // Inicializamos un arreglo para almacenar los valores
                    $address = [];

                    // Recorremos los componentes para extraer la información
                    foreach ($components as $component) {
                        if (in_array('country', $component['types'])) {
                            $address['country'] = $component['long_name'];
                        }
                        if (in_array('administrative_area_level_1', $component['types'])) {
                            $address['state'] = $component['long_name'];
                        }
                        if (in_array('locality', $component['types'])) {
                            $address['city'] = $component['long_name'];
                        }
                        // Si no se encontró 'locality', se puede usar 'sublocality'
                        if (!isset($address['city']) && in_array('sublocality', $component['types'])) {
                            $address['town'] = $component['long_name'];
                        }
                        if (in_array('route', $component['types'])) {
                            $address['road'] = $component['long_name'];
                        }
                        if (in_array('street_number', $component['types'])) {
                            $address['house_number'] = $component['long_name'];
                        }
                        if (in_array('postal_code', $component['types'])) {
                            $address['postcode'] = $component['long_name'];
                        }
                    }
                    // Asignar los valores a las variables usando la misma nomenclatura que usas
                    $this->pais = $address['country'] ?? '';
                    $this->provincia = $address['state'] ?? '';
                    $this->ciudad = $address['city'] ?? ($address['town'] ?? '');
                    $this->calle = $address['road'] ?? '';
                    $this->altura = $address['house_number'] ?? '';
                    $this->codigo_postal = $address['postcode'] ?? '';

                    $this->dispatch('addressUpdated');
                } else {
                    // No se encontraron resultados
                    $this->dispatch('error', ['message' => 'No se encontró ninguna dirección para las coordenadas proporcionadas.']);
                }
            }
        } catch (\Exception $e) {
            logger()->error('Exception occurred in setAddress', ['exception' => $e->getMessage()]);
        }
    }

    public function handleGeolocation($lat, $long)
    {
        $this->lat = $lat;
        $this->long = $long;
        $this->setAddress($lat, $long);
    }

    public function render()
    {
        $this->dispatch('mapModalShown', ['mapModal2']);
        $tipos = TiposUbicacionesModel::all();
        return view('livewire.ubicaciones.crear-ubicaciones', compact('tipos'));
    }

    public function close()
    {
        $this->reset([
            'nombre',
            'pais',
            'provincia',
            'ciudad',
            'codigo_postal',
            'calle',
            'piso',
            'depto',
            'lat',
            'long',
            'propiedad',
            'tipo',
            'modo',
            'altura',
            'cuit',
            'cuil_gestor',
            'selectedCuit',
            'selectedCuil',
            'cuit_empresa',
            'subsuelo',
            'MultiplePiso',
        ]);
        $this->dispatch('refreshLivewireTable');
        $this->open = false;

        $cacheData = Cache::get('abrirModalServicio');
        if (!empty($cacheData) && $cacheData['estado'] === true) {
            $activoId = $cacheData['id_activo'];
            $this->dispatch('openModalCambiarUbicacion', ['activo' => $activoId])
                ->to('ubicaciones.cambiar-ubicacion');
        }
    }

    public function abrirModalUbicacion($origen = 'default')
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->origen = $origen;
            $this->lat = null;
            $this->long = null;
            $this->open = true;
        }
    }

    public function mount()
    {
        $identificadores = IdHelper::identificadorParcial();
        $this->cuit = $identificadores['cuit'];
        $this->cuil = $identificadores['user'];

        $this->existeUbicacion();

        $this->tiposUbicacion = TiposUbicacionesModel::all();
        $this->panel_actual = auth()->user()->panel_actual;

        $this->user = auth()->user();

        // Configurar ubicaciones
        if ($this->panel_actual == 'Empresa' || $this->panel_actual == 'Prestadora') {
            $this->searchEmpleados();
            $this->updatedSearchGestionado();
            $this->updatedSearchClientes();
        }
    }

    // Para busqueda de empleados gestor
    public function updatedSearchGestionado()
    {
        $this->searchEmpleados();
    }

    // Busca los empleados de dicha empresa para ser mostrados
    private function searchEmpleados()
    {
        // Reiniciar la variable antes de cada búsqueda
        $this->noEmpleadosEncontrados = false;

        // Busca los Id de los empleados en UsuariosEmpresasModel
        $usuariosIds = UsuariosEmpresasModel::where('cuit', $this->cuit)
            ->where('cargo', '!=', 'Apoderado')
            ->where('estado', 'Aceptado')
            ->where('tipo_user', '!=', 2)
            ->whereNot('estado', 'Deshabilitado')
            ->pluck('id_usuario');

        // Validar si hay IDs, si no, no hace la consulta en User
        if ($usuariosIds->isEmpty()) {
            $this->empleadosLista = collect(); // Colección vacía
            $this->noEmpleadosEncontrados = true;
            $this->cuil_gestor = null;
            return;
        }

        $nombreBuscado = $this->searchGestionado ?: '';

        // Buscar en User nombre que coincida con los existentes
        $this->empleadosLista = User::whereIn('id', $usuariosIds)
            ->where('name', 'like', "%{$nombreBuscado}%")
            ->whereNotNull('cuil')
            ->get();

        // Verificar si hay resultados
        $this->noEmpleadosEncontrados = $this->empleadosLista->isEmpty();
    }

    public function setGestor($cuil)
    {
        $usuario = User::where('cuil', $cuil)->first();
        $this->selectedCuil = $usuario ? $usuario->name : null;
        $this->cuil_gestor = $cuil;
        $this->searchCuil = ''; // Limpiar el campo de búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'gestor']); // Cerrar el dropdown
    }


    // Para busqueda de empleados gestor
    public function updatedSearchClientes()
    {
        $this->searchClientes();
    }

    // Busca los empleados de dicha empresa para ser mostrados
    private function searchClientes()
    {
        // Reiniciar la variable antes de cada búsqueda
        $this->noClientesEncontrados = false;
        // Busca los Id de los empleados en UsuariosEmpresasModel
        $clientesIds = ClientesEmpresaModel::whereNotNull('cliente_cuit')
            ->where('empresa_cuit', $this->cuit)
            ->pluck('cliente_cuit')
            ->filter(); // Filtra valores nulos o vacíos

        // Validar si hay IDs, si no, no hace la consulta en User
        if ($clientesIds->isEmpty()) {
            $this->clientesLista = collect(); // Colección vacía
            $this->noClientesEncontrados = true;
            $this->cuit = null;
            return;
        }
        $clienteBuscado = $this->searchClientes ?: '';

        // Buscar en EmpresasModel nombre que coincida con los existentes
        $this->clientesLista = EmpresasModel::whereIn('cuit', $clientesIds)
            ->where(function ($query) use ($clienteBuscado) {
                $query->where('razon_social', 'like', "%{$clienteBuscado}%");

                // Solo agregar la condición por CUIT si el valor buscado es numérico
                if (is_numeric($clienteBuscado)) {
                    $query->orWhere('cuit', $clienteBuscado);
                }
            })
            ->get();

        // Verificar si hay resultados
        $this->noClientesEncontrados = $this->clientesLista->isEmpty();
    }

    //Busqueda de los empleados para ser asignados
    public function setClientes($cuit)
    {
        $cliente = EmpresasModel::find($cuit);
        $this->selectedCuit = $cliente ? $cliente->razon_social : null;
        $this->cuit_empresa = $cuit;
        $this->searchClientes = ''; // Limpiar el campo de búsqueda
        $this->dispatch('closeDropdown', ['dropdown' => 'clientes']); // Cerrar el dropdown
    }
}
