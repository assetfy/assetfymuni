<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use App\Models\EmpresasModel;
use App\Models\MisProveedoresModel;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB; // Importar DB para transacciones
use App\Notifications\InvitacionProveedorNotification;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Validator;
use App\Helpers\IdHelper;
use App\Models\provedoresContratosModel;
use App\Models\User;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CargarMisProveedores extends Component
{
    public $open = false;

    // Campo de búsqueda
    public $terminoBusqueda = '';
    public $empresa;
    public $id, $nombre, $contrato, $numeroContrato, $fechaContrato;

    // Array de proveedores
    public $proveedores = [];

    // Indicadores
    public $encontrado = false;
    public $mensajeBusqueda = '';

    // Control para mostrar el formulario de agregar proveedor
    public $mostrarAgregarFormulario = false;

    // Listeners para eventos de Livewire
    protected $listeners = ['RegistrarProveedores', 'updateCoordinates'];

    // Reglas de validación
    protected function rules()
    {
        $rules = [
            'proveedores.*.razonSocial' => 'required|string|max:100',
            'proveedores.*.cuit' => 'required|numeric',
            'proveedores.*.localidad' => 'required|string|max:100',
            'proveedores.*.provincia' => 'required|string|max:100',
            'proveedores.*.lat' => 'nullable|numeric',
            'proveedores.*.long' => 'nullable|numeric',
            'proveedores.*.email' => 'required|email|max:100',
            'proveedores.*.url'   => 'nullable|url|max:255',
            'contrato'            => 'nullable|string',
            // Por defecto, numeroContrato es opcional:
            'numeroContrato'      => 'nullable|string|max:10',
        ];

        // Si se selecciona "Si", entonces numeroContrato es requerido
        if ($this->contrato === 'Si') {
            $rules['numeroContrato'] = 'required|string|max:10';
            $rules['fechaContrato'] = 'required|date';
        }
        return $rules;
    }


    // Mensajes de validación personalizados
    protected $messages = [
        'proveedores.*.email.email' => 'El email debe ser una dirección válida.',
        'proveedores.*.email.required' => 'El email  es obligatorio.',
        'proveedores.*.email.max' => 'El email no puede tener más de 100 caracteres.',
        'proveedores.*.razonSocial.required' => 'La Razón Social es obligatoria.',
        'proveedores.*.razonSocial.max' => 'La Razón Social no puede exceder los 100 caracteres.',
        'proveedores.*.cuit.required' => 'El CUIT es obligatorio.',
        'proveedores.*.cuit.numeric' => 'El CUIT solo puede contener números.',
        'proveedores.*.localidad.required' => 'La Localidad es obligatoria.',
        'proveedores.*.localidad.max' => 'La Localidad no puede exceder los 100 caracteres.',
        'proveedores.*.provincia.required' => 'La Provincia es obligatoria.',
        'proveedores.*.provincia.max' => 'La Provincia no puede exceder los 100 caracteres.',
        'proveedores.*.lat.numeric' => 'La Latitud debe ser un número válido.',
        'proveedores.*.long.numeric' => 'La Longitud debe ser un número válido.',
        'proveedores.*.url.url' => 'La URL debe ser válida.',
        'proveedores.*.url.max' => 'La URL no debe exceder 255 caracteres.',
    ];

    public function mount()
    {
        $this->resetProveedores();
        $this->id = auth()->user()->id;
        $this->nombre = auth()->user()->name;
    }

    /**
     * Reinicia el array de proveedores a su estado inicial.
     */
    private function resetProveedores()
    {
        $this->proveedores = [
            [
                'razonSocial' => '',
                'cuit' => '',
                'localidad' => '',
                'provincia' => '',
                'lat' => null,
                'long' => null,
            ]
        ];
    }

    /**
     * Valida las propiedades a medida que se actualizan.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    /**
     * Método para realizar la búsqueda de la empresa al hacer clic en el botón "Buscar".
     */
    public function buscar()
    {
        $this->resetValidation();
        $this->mostrarAgregarFormulario = false;

        $this->validate(['terminoBusqueda' => 'required|string']);

        // Busqueda de proveedores en tabla de empresas por razon_social / CUIT
        $empresa = EmpresasModel::where('tipo', 2)
            ->where(function ($query) {
                $query->where('razon_social', 'like', '%' . $this->terminoBusqueda . '%');
                if (is_numeric($this->terminoBusqueda)) {
                    $query->orWhere('cuit', $this->terminoBusqueda);
                }
            })
            ->first();

        if ($empresa) {
            // Rellenar los datos en el primer índice del array $proveedores
            $this->proveedores = [[
                'razonSocial' => $empresa->razon_social,
                'cuit' => $empresa->cuit,
                'localidad' => $empresa->localidad,
                'provincia' => $empresa->provincia,
                'lat' => is_numeric($empresa->lat) ? floatval($empresa->lat) : null,
                'long' => is_numeric($empresa->long) ? floatval($empresa->long) : null,
            ]];

            $this->mensajeBusqueda = '¡Se encontró la empresa en la plataforma!';
            $this->encontrado = true;

            // Inicializar mapa informativo si tiene coordenadas
            if ($this->proveedores[0]['lat'] !== null && $this->proveedores[0]['long'] !== null) {
                $this->dispatch('initializeInformativeMap', [
                    'lat' => $this->proveedores[0]['lat'],
                    'lng' => $this->proveedores[0]['long'],
                ]);
            } else {
                // Coordenadas no válidas
                $this->dispatch('initializeEditableMap');
            }
        } else {
            // Reiniciar proveedores y mostrar mensaje
            $this->resetProveedores();
            $this->mensajeBusqueda = 'No se ha encontrado ninguna empresa con ese nombre o CUIT.';
            $this->encontrado = false;

            $this->dispatch('initializeEditableMap');
        }
    }

    /**
     * Método para mostrar el formulario de agregar proveedor no registrado.
     */
    public function mostrarFormularioAgregar()
    {
        $this->mostrarAgregarFormulario = true;
        $this->resetValidation();
        $this->mensajeBusqueda = '';
        $this->encontrado = false;
        $this->resetProveedores();

        // Emitir evento para inicializar el mapa en modo editable
        $this->dispatch('initializeEditableMap');
    }

    /**
     * Reinicia el formulario para buscar nuevamente.
     */
    public function mostrarBuscador()
    {
        $this->reset([
            'terminoBusqueda',
            'mensajeBusqueda',
            'mostrarAgregarFormulario',
            'encontrado',
        ]);
        $this->resetProveedores();
    }

    /**
     * Lógica para guardar en mis proveedores favoritos.
     */
    public function guardar()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            foreach ($this->proveedores as $index => &$proveedor) {
                // 1) Si lat o long están vacíos, buscamos en EmpresasModel
                if (empty($proveedor['lat']) || empty($proveedor['long'])) {
                    $empresa = EmpresasModel::where('cuit', $proveedor['cuit'])->first();
                    if ($empresa) {
                        $proveedor['lat']         = $empresa->lat;
                        $proveedor['long']        = $empresa->long;
                        $proveedor['razonSocial'] = $empresa->razon_social;
                        $proveedor['localidad']   = $empresa->localidad;
                        $proveedor['provincia']   = $empresa->provincia;
                    }
                }

                // 2) Obtener el placeId si hay lat/long
                $placeId = null;
                if (!empty($proveedor['lat']) && !empty($proveedor['long'])) {
                    $placeId = $this->obtenerPlaceId($proveedor['lat'], $proveedor['long']);
                }

                // 3) Determinar si existe en la plataforma
                $existeEnLaPlataforma = EmpresasModel::where('cuit', $proveedor['cuit'])->exists();

                // 4) Si no existe, creamos la empresa y luego el usuario + relación
                if (! $existeEnLaPlataforma) {
                    EmpresasModel::updateOrCreate(
                        ['cuit' => $proveedor['cuit']],
                        [
                            'razon_social' => $proveedor['razonSocial'],
                            'localidad'    => $proveedor['localidad'],
                            'provincia'    => $proveedor['provincia'],
                            'tipo'         => 2,
                            'estado'       => 'No Registrado',
                        ]
                    );

                    // Crear usuario asociado a esta empresa
                    $count = User::count() + 1;
                    $user = User::create([
                        'name'          => 'Asset-fy' . $count,
                        'email'         => $proveedor['email'],
                        'cuil'          => mt_rand(10000000000, 99999999999),
                        'password'      => Hash::make(Str::random(16)),
                        'tipo'          => 1,
                        'estado'        => 1,
                        'panel_actual'  => 'Prestadora',
                        'entidad'       => $proveedor['cuit'],
                    ]);

                    // Crear relación en usuarios_empresas
                    UsuariosEmpresasModel::create([
                        'id_usuario'               => $user->id,
                        'cuit'                     => $proveedor['cuit'],
                        'cargo'                    => 'Apoderado',
                        'estado'                   => 'Aceptado',
                        'legajo'                   => null,
                        'tipo_user'                => 2,
                        'tipo_inter_exter'         => null,
                        'es_representante_tecnico' => 'No',
                    ]);
                }

                // 5) Verificar si ya está en favoritos
                $asociacionExistente = MisProveedoresModel::where('id_usuario', $this->id)
                    ->where('cuit', $proveedor['cuit'])
                    ->where('empresa', IdHelper::idEmpresa())
                    ->exists();

                // 6) Si NO, lo agregamos
                if (! $asociacionExistente) {
                    $data = [
                        'existe_en_la_plataforma' => $existeEnLaPlataforma ? 'Si' : 'No',
                        'razon_social'            => $proveedor['razonSocial'],
                        'cuit'                    => $proveedor['cuit'],
                        'localidad'               => $proveedor['localidad'],
                        'provincia'               => $proveedor['provincia'],
                        'id_usuario'              => auth()->id(),
                        'email'                   => $proveedor['email'] ?? null,
                        'url'                     => $proveedor['url'] ?? null,
                        'lat'                     => $proveedor['lat'] ?? null,
                        'long'                    => $proveedor['long'] ?? null,
                        'places'                  => $placeId,
                        'empresa'                 => IdHelper::idEmpresa(),
                    ];
                    $nuevoProveedor = MisProveedoresModel::create($data);

                    if ($this->contrato === 'Si') {
                        ProvedoresContratosModel::create([
                            'id_mis_proveedor' => $nuevoProveedor->id,
                            'numero'           => $this->numeroContrato,
                            'fecha'            => $this->fechaContrato,
                        ]);
                    }

                    $this->dispatch('lucky', 'Proveedor agregado a favoritos.');
                }

                // 7) Enviar notificación si hay email
                if (!empty($proveedor['email'])) {
                    $validator = Validator::make(['email' => $proveedor['email']], [
                        'email' => 'required|email|max:100',
                    ]);
                    if (! $validator->fails()) {
                        try {
                            Notification::route('mail', $proveedor['email'])
                                ->notify(new InvitacionProveedorNotification($proveedor, auth()->user()->name));
                        } catch (\Exception $e) {
                            Log::error('Error al enviar invitación: ' . $e->getMessage());
                        }
                    }
                }
            }

            DB::commit();
            $this->dispatch('refreshLivewireTable');
            $this->cerrar();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('errorInfo', [
                'title'   => 'Error',
                'message' => $e->getMessage(),
            ]);
            $this->cerrar();
        }
    }
    /**
     * Cierra el modal y resetea todas las variables.
     */
    public function cerrar()
    {
        $this->reset([
            'open',
            'terminoBusqueda',
            'mensajeBusqueda',
            'encontrado',
            'mostrarAgregarFormulario',
        ]);
        $this->resetProveedores();
        $this->open = false;
    }

    /**
     * Método existente para registrar proveedores.
     */
    public function RegistrarProveedores()
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
            // Emitir evento para inicializar el mapa en modo editable
            $this->dispatch('initializeEditableMap');
        }
    }

    /**
     * Función para obtener la dirección a partir de latitud y longitud usando Nominatim.
     */
    public function setAddress($lat, $long)
    {
        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Laravel/Livewire'
            ])->withOptions([
                'verify' => false,  // Desactiva la verificación SSL
            ])->get('https://nominatim.openstreetmap.org/reverse', [
                'lat' => $lat,
                'lon' => $long,
                'format' => 'json',
            ]);

            if ($response->successful()) {
                $address = $response->json('address');
                // Asigna los campos de dirección según tus necesidades
            } else {
                // Manejar errores en la respuesta
            }
        } catch (\Exception $e) {
            // Manejar excepciones
        }
    }

    /**
     * Método para agregar un nuevo proveedor al array.
     */
    public function agregarProveedor()
    {
        $this->proveedores[] = [
            'razonSocial' => '',
            'cuit' => '',
            'localidad' => '',
            'provincia' => '',
            'lat' => null,
            'long' => null,
        ];

        // Opcional: emitir evento para inicializar el mapa en modo editable para el nuevo proveedor
        $this->dispatch('initializeEditableMap');
    }

    /**
     * Método para eliminar un proveedor específico del array.
     */
    public function eliminarProveedor($index)
    {
        unset($this->proveedores[$index]);
        $this->proveedores = array_values($this->proveedores);
    }

    public function render()
    {
        return view('livewire.servicios.cargar-mis-proveedores');
    }

    private function obtenerPlaceId($lat, $long)
    {
        $apiKey = config('services.google_maps.api_key');
        //sin certificado 
        $response = Http::withoutVerifying()
            ->get('https://maps.googleapis.com/maps/api/geocode/json', [
                'latlng' => "{$lat},{$long}",
                'key'     => $apiKey,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['results'])) {
                return $data['results'][0]['place_id'] ?? null;
            }
        }
        return null;
    }
}
