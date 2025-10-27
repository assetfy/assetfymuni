<?php

namespace App\Livewire\Empresas;

use App\Models\EstadoActividadesEconomicasModel;
use Illuminate\Validation\ValidationException;
use App\Models\ActividadesEconomicasModel;
use App\Http\Controllers\AfipController;
use App\Models\EmpresasActividadesModel;
use Illuminate\Support\Facades\Storage;
use App\Models\TiposUbicacionesModel;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB; //  Facade para manejar transacciones
use App\Models\TiposEmpresaModel;
use App\Models\UbicacionesModel;
use App\Models\AuditoriasModel;
use Livewire\WithPagination;
use App\Models\EmpresasModel;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use App\Traits\SortableTrait;
use Livewire\WithFileUploads;
use Livewire\Component;

class CreateEmpresas extends Component
{
    use SortableTrait, WithFileUploads, WithPagination;

    protected $listeners = ['dispatchSuccessAndClose'];

    // Propiedades públicas que representan los campos del formulario y otros estados
    public $open = false;
    // Propiedades del formulario
    public $step = 1;
    public $isStepValid = false; // Variable para habilitar o deshabilitar el botón "Siguiente"
    public $maxStep;
    public $cuit, $razon_social, $estado, $constancia_afip, $id_usuario, $empresa, $rutaFoto,
        $mensaje, $Afip, $actividades, $codActividad, $Actividadprovincia, $provincia, $piso, $localidad, $codigo_postal,
        $domicilio, $actividad, $cod, $datos, $selectedActividad, $autoriza, $empresa_reguladora, $search, $tipo, $tipoSeleccionado, $logo, $descripcion, $lat, $long, $opcion = 'No';
    public $nombre, $propiedad, $tipoUbicacion, $pais, $ciudad, $calle, $altura, $tipoUbicaciones, $usuariosempresas, $tiposEmpresas, $empresas;
    public $tipoDescripcion = '';
    public $constancia, $actividadesSinServicios, $userData, $NuevoUsuario;
    public $url;      // Para almacenar la URL ingresada por el usuario
    public $places; // Para almacenar el Place ID generado automáticamente (o '24 horas' para pruebas)

    // Variables para almacenar rutas de archivos subidos temporalmente
    public $constancia_afip_path, $logo_path;

    // Reglas de validación para los campos del formulario
    protected $rules = [
        'lat' => 'required',
        'long' => 'required',
        'provincia' => 'required|max:100',
        'localidad' => 'required|max:100',
        'domicilio' => 'required|max:100',
        'codigo_postal' => 'required|max:100',
        'opcion' => 'required',
        'url' => 'nullable|url|max:255',
    ];

    // Mensajes personalizados para las validaciones
    protected $messages = [
        'nombre.required' => 'El nombre del inmueble es obligatorio.',
        'tipoUbicacion.required' => 'El tipo de ubicación es obligatorio.',
        'propiedad.required' => 'El campo propiedad es obligatorio.',
        'descripcion.required' => 'La descripción de la actividad es obligatoria.',
        'selectedActividad.required' => 'La actividad económica es obligatoria.',
        'cuit.required' => 'El CUIT es obligatorio.',
        'cuit.numeric' => 'El CUIT debe ser numérico.',
        'razon_social.required' => 'La razón social es obligatoria.',
        'url.url' => 'La URL debe ser válida.',
        'url.max' => 'La URL no debe exceder 255 caracteres.',
    ];
    /**
     * Método que se ejecuta al montar el componente.
     * Carga los datos necesarios para los selectores y establece el usuario actual.
     */
    public function mount()
    {
        $this->userData = session('user_data', []);
        //prueba
        // Si el user_type no está definido o es vacío, disparar el evento y redirigir.
        if (empty($this->userData['user_type'])) {
            // Redirigir a la ruta de registro (asegúrate de tener definida la ruta 'register')
            return redirect()->route('register');
        }

        // Si user_type existe, asignar automáticamente tipoSeleccionado basado en su valor
        if ($this->userData['user_type'] === 'empresa') {
            $this->tipoSeleccionado = 1;
        } elseif ($this->userData['user_type'] === 'proveedora') {
            $this->tipoSeleccionado = 2;
        }

        // Cargar datos necesarios para los selectores
        $this->tipoUbicaciones = TiposUbicacionesModel::all();
        $this->usuariosempresas = UsuariosEmpresasModel::all();
        $this->tiposEmpresas = TiposEmpresaModel::where('id_tipo_empresa', '!=', 4)->get();
        $this->empresas = EmpresasModel::all();

        // Ajustar maxStep para que coincida con la vista Blade
        $this->updateMaxStep();
    }


    private function updateMaxStep()
    {
        if ($this->tipoSeleccionado == 1 && $this->opcion == 'Si') {
            $this->maxStep = 5;
        } elseif ($this->opcion == 'Si') {
            $this->maxStep = 5;
        } else {
            $this->maxStep = 4;
        }

        // Asegurar que el paso actual no exceda maxStep
        if ($this->step > $this->maxStep) {
            $this->step = $this->maxStep;
        }
    }
    /**
     * Método que se ejecuta cuando cambia la propiedad 'opcion'.
     * Ajusta las reglas de validación y el número máximo de pasos según la opción.
     */
    public function updatedOpcion()
    {
        if ($this->opcion == 'Si') {
            // Agregar reglas de validación para los detalles del inmueble
            $this->rules['nombre'] = 'required|max:100';
            $this->rules['tipoUbicacion'] = 'required';
            $this->rules['propiedad'] = 'required';
            $this->maxStep = 6;
        } else {
            // Eliminar reglas de validación para los detalles del inmueble
            unset($this->rules['nombre']);
            unset($this->rules['tipoUbicacion']);
            unset($this->rules['propiedad']);
            $this->maxStep = 5;
        }
    }

    /**
     * Método para avanzar al siguiente paso del formulario.
     * Valida el paso actual y aumenta el contador de pasos.
     * Si es el último paso, guarda los datos.
     */
    public function nextStep()
    {
        // Validar los campos del paso actual
        $this->validateCurrentStep();

        if ($this->step == 1) {
            if ($this->tipoSeleccionado == 1 && $this->opcion == 'No') {
                // Tipo 1 y opción 'No': avanzar al Paso 3
                $this->step = 3;
            } else {
                // Otros casos: avanzar al siguiente paso
                $this->step++;
            }
        } elseif ($this->step == 2) {
            if ($this->tipoSeleccionado == 1 && $this->opcion == 'Si') {
                // Tipo 1 y opción 'Si': saltar al Paso 4
                $this->step = 4;
            } else {
                // Otros casos: avanzar al siguiente paso
                $this->step++;
            }
        } elseif ($this->step == 3) {
            if ($this->tipoSeleccionado == 2 && $this->opcion == 'Si') {
                // Tipo 2 y opción 'Si': avanzar al Paso 4
                $this->step++;
            } else {
                // Otros casos: avanzar al siguiente paso
                $this->step++;
            }
        } elseif ($this->step < $this->maxStep) {
            $this->step++;
        } else {
            $this->save();
        }
    }

    /**
     * Método para retroceder al paso anterior del formulario.
     */
    public function previousStep()
    {
        // Caso: Del paso 3 al 1 si es tipo=1 y opcion=No
        if ($this->step == 3 && $this->tipoSeleccionado == 1 && $this->opcion == 'No') {
            $this->step = 1;
            // Forzar a que se muestre el mapa de nuevo
            $this->dispatch('viewMapShown');
            return;
        }

        if ($this->step == 5 && $this->tipoSeleccionado == 1 && $this->opcion == 'Si') {
            $this->step = 4;
        } elseif ($this->step == 4 && $this->tipoSeleccionado == 1 && $this->opcion == 'Si') {
            $this->step = 2;
        } elseif ($this->step == 4 && ($this->tipoSeleccionado == 2 || $this->opcion == 'No')) {
            $this->step = 3;
        } elseif ($this->step > 1) {
            $this->step--;
        }

        // Por si acaso también forzamos el redibujado si vuelves a step=1
        if ($this->step == 1) {
            $this->dispatch('viewMapShown');
        }
    }

    public function updatedStep()
    {
        if ($this->step == 1) {
            $this->dispatch('viewMapShown');
        }
    }

    /**
     * Método que valida los campos según el paso actual.
     */
    private function validateCurrentStep()
    {
        if ($this->step == 1) {
            // Validación del Paso 1: Ubicación
            $this->validate([
                'lat' => 'required|numeric',
                'long' => 'required|numeric',
            ]);
        } elseif ($this->step == 2) {
            if ($this->opcion == 'Si') {
                // Validación del Paso 2: Detalles del Inmueble
                $this->validate([
                    'nombre' => 'required|string|max:100',
                    'tipoUbicacion' => 'required|integer',
                    'propiedad' => 'required|string|max:50',
                ]);
            }
        } elseif ($this->step == 3) {
            if ($this->tipoSeleccionado == 2 && $this->opcion == 'Si') {
                // Validación del Paso 3: Selección de Actividad Económica para tipo 2
                $this->validate([
                    'selectedActividad' => 'required|integer',
                ]);
            } elseif ($this->tipoSeleccionado == 1 && $this->opcion == 'No') {
                // Validación del Paso 3: Buscar Empresa para tipo 1 con opción 'No'
                $this->validate([
                    'cuit' => 'required|numeric|digits:11',
                    'razon_social' => 'required|string|max:255',
                    'descripcion' => 'required|string|max:1000',
                ]);
            }
            // No validar nada si es tipo 1 y opción 'Si' (ya saltamos al Paso 4)
        } elseif ($this->step == 4) {
            if ($this->tipoSeleccionado == 1 && $this->opcion == 'Si') {
                // Validación del Paso 4: Buscar Empresa para tipo 1 con opción 'Si'
                $this->validate([
                    'cuit' => 'required|numeric|digits:11',
                    'razon_social' => 'required|string|max:255',
                    'descripcion' => 'required|string|max:1000',
                ]);
            }
            // No se necesita validar nada si opcion='No' o tipo 2 en Paso 4 (Resumen)
        } elseif ($this->step == 5) {
            // Paso 5: Resumen de la empresa para 'proveedora'
            // No se necesita validar aquí si ya se validaron los pasos anteriores
        }

        // Si la validación pasa, habilitar el botón de siguiente
        $this->isStepValid = true;
    }


    /**
     * Método que se ejecuta cuando se actualiza cualquier propiedad.
     * Valida individualmente el campo actualizado y vuelve a validar el paso actual.
     */
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        // Después de cada actualización de un campo, volver a validar el paso
        $this->validateCurrentStep();
    }

    /**
     * Método para guardar los datos del formulario.
     * Utiliza una transacción para asegurar la integridad de los datos.
     */
    public function save()
    {
        // Validación final antes de guardar
        $this->validate([
            'cuit' => 'required|numeric|digits_between:1,20',
            'razon_social' => 'required',
            'descripcion' => 'required',
        ]);

        DB::beginTransaction();

        try {
            // Inicializar rutas como null
            $constancia = $this->handleFileUpload($this->constancia_afip, $this->constancia_afip_path, 'constancia_afip');
            $logo = $this->handleFileUpload($this->logo, $this->logo_path, 'logos');

            // Verificar si la empresa ya existe por CUIT
            if ($this->ExisteEmpresa()) {
                throw ValidationException::withMessages(['cuit' => 'La empresa ya existe.']);
            }
            $this->createUser();
            // Crear registros usando las rutas obtenidas
            $this->crearRegistros($constancia, $logo);

            $this->updatePanel();
            $this->autorizacionAutomatica();
            $this->dispatch('terminarregistro');
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            // Elimina archivos si la transacción falla
            $this->deleteFileIfExists($constancia);
            $this->deleteFileIfExists($logo);
            session()->forget('user_data');
            // Capturamos el error y lo enviamos a través del evento "errorInfo"
            $this->dispatch('errorInfo', [
                'title' => 'Error de creación',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function createUser()
    {
        $this->NuevoUsuario = User::create([
            'name' => $this->userData['name'],
            'email' => $this->userData['email'],
            'cuil' => $this->userData['cuil'],
            'password' => Hash::make($this->userData['password']),
            'tipo' => $this->userData['tipo'] ?? 2,
            'estado' => $this->userData['estado'] ?? 1,
        ]);
    }

    private function handleFileUpload($file, $tempPath, $folder)
    {
        if ($file && !$tempPath) {
            $tempPath = $file->store('temp', 'public');
        }

        if ($tempPath) {
            $newPath = $folder . '/' . basename($tempPath);
            Storage::disk('public')->move($tempPath, $newPath);
            return 'storage/' . $newPath;
        }

        return null;
    }

    private function deleteFileIfExists($filePath)
    {
        if ($filePath && Storage::disk('public')->exists($filePath)) {
            Storage::disk('public')->delete($filePath);
        }
    }

    /**
     * Método para mover un archivo de una carpeta a otra.
     *
     * @param string|null $path Ruta actual del archivo
     * @param string $folder Carpeta destino
     * @return string|null Nueva ruta del archivo
     */
    private function moveFile($path, $folder)
    {
        if ($path && Storage::disk('public')->exists($path)) {
            $newPath = $folder . '/' . basename($path);
            Storage::disk('public')->move($path, $newPath);
            return 'storage/' . $newPath;
        }
        return null;
    }

    /**
     * Método que crea todos los registros relacionados dentro de una transacción.
     *
     * @param string|null $constancia Ruta de la constancia de AFIP
     * @param string|null $logo Ruta del logo de la empresa
     */
    private function crearRegistros($constancia, $logo)
    {
        $this->createEmpresa($constancia, $logo);
        // Crear la relación usuario-empresa
        $this->createUsuarioEmpresa();
        // Crear la actividad económica de la empresa
        $this->createEmpresaActividad();
        // Crear un registro en auditoría
        $this->auditoria();
    }

    /**
     * Método que verifica si una actividad económica ya está regulada.
     *
     * @return bool
     */
    private function esActividad_regulada()
    {
        return EstadoActividadesEconomicasModel::where('cod_actividad', $this->selectedActividad)->exists();
    }

    /**
     * Método que verifica si una empresa ya existe por CUIT.
     *
     * @return bool
     */
    private function ExisteEmpresa()
    {
        return EmpresasModel::where('cuit', $this->cuit)->exists();
    }

    /**
     * Método que despliega una advertencia y cierra el formulario.
     */
    private function dispatchWarningAndClose()
    {
        // Desplegar una notificación de advertencia al usuario
        $this->dispatch('warning', ['message' => 'La empresa ya existe o hay un error en los datos.']);
        // Resetear y cerrar el formulario
        $this->resetAndClose();
    }

    /**
     * Método que despliega una notificación de éxito y cierra el formulario.
     */
    public function dispatchSuccessAndClose()
    {
        // Enviar notificación de verificación de correo electrónico
        $user = User::find($this->NuevoUsuario->id);
        if ($user && !$user->hasVerifiedEmail()) {
            $user->sendEmailVerificationNotification(); // Enviar notificación de verificación
        }
        $this->resetAndClose();
    }

    /**
     * Método que resetea las propiedades del componente y redirige a la ruta 'login'.
     */
    private function resetAndClose()
    {
        // Resetear todas las propiedades relevantes del componente
        $this->reset([
            'cuit',
            'razon_social',
            'tipoSeleccionado',
            'descripcion',
            'constancia_afip_path',
            'logo_path',
            'nombre',
            'propiedad',
            'tipoUbicacion',
            'pais',
            'ciudad',
            'calle',
            'altura',
            'tipoUbicaciones',
            'usuariosempresas',
            'tiposEmpresas',
            'empresas',
            'actividad',
            'codActividad',
            'Actividadprovincia',
            'provincia',
            'piso',
            'localidad',
            'codigo_postal',
            'domicilio',
            'lat',
            'long',
            'opcion',
            'mensaje',
            'Afip'
        ]);

        // Reiniciar el contador de pasos
        $this->step = 1;

        session()->forget('user_data');

        // Redirigir a la página de inicio de sesión
        return redirect()->route('login');
    }
    /**
     * Método para registrar una auditoría.
     *
     * @param string $cuit CUIT de la empresa
     */
    private function auditoria()
    {
        AuditoriasModel::create([
            'cuit' => $this->cuit,
            'razon_social' => $this->razon_social,
            'estado' => 'En revisión',
            'fecha_creacion' => today(),
            'id_usuario' => $this->id_usuario,
        ]);
    }

    /**
     * Método para crear una empresa en la base de datos.
     *
     * @param string|null $rutaFoto Ruta de la constancia de AFIP
     * @param string|null $logo Ruta del logo de la empresa
     */
    private function createEmpresa($rutaFoto, $logo)
    {
        // Luego, obtienes el place_id desde lat/long
        $placeId = $this->obtenerPlaceIdDesdeCoordenadas($this->lat, $this->long);
        if ($this->opcion == 'Si') {
            // Crear la ubicación si la opción es 'Si'
            $this->crearUbicacion();
        }
        EmpresasModel::create([
            'cuit' => $this->cuit,
            'razon_social' => $this->razon_social,
            'tipo' => $this->tipoSeleccionado,
            'constancia_afip' => $rutaFoto,
            'provincia' => $this->provincia,
            'localidad' => $this->localidad,
            'piso' => $this->piso,
            'domicilio' => $this->domicilio,
            'codigo_postal' => $this->codigo_postal,
            'lat' => $this->lat,
            'long' => $this->long,
            'COD_ACTIVIDAD' => $this->selectedActividad ?? 12, // Actividad por defecto si no se selecciona
            'logo' => $logo,
            'descripcion_actividad' => $this->descripcion,
            'url' => $this->url,
            'places' => $placeId,
        ]);
    }

    /**
     * Método para crear una ubicación en la base de datos.
     */
    private function crearUbicacion()
    {
        UbicacionesModel::create([
            'nombre' => $this->nombre,
            'pais' => $this->pais,
            'provincia' => $this->provincia,
            'ciudad' => $this->ciudad,
            'codigo_postal' => $this->codigo_postal,
            'calle' => $this->calle,
            'altura' => $this->altura,
            'piso' => $this->piso,
            'cuit' => $this->cuit,
            'propiedad' => $this->propiedad,
            'tipo' => $this->tipoUbicacion,
            'lat' => $this->lat,
            'long' => $this->long,
        ]);
    }

    /**
     * Método para crear la relación usuario-empresa en la base de datos.
     */
    private function createUsuarioEmpresa()
    {
        UsuariosEmpresasModel::create([
            'id_usuario' => $this->NuevoUsuario->id,
            'cuit' => $this->cuit,
        ]);
    }
    /**
     * Método para crear la actividad económica de la empresa en la base de datos.
     */
    private function createEmpresaActividad()
    {
        EmpresasActividadesModel::create([
            'cuit' => $this->cuit,
            'cod_actividad' => $this->selectedActividad ?? '12',
            'provincia' => $this->provincia,
            'localidad' => $this->localidad,
        ]);
    }
    /**
     * Método para eliminar la constancia de AFIP subida temporalmente.
     */
    public function removeConstanciaAfip()
    {
        if ($this->constancia_afip_path) {
            Storage::disk('public')->delete($this->constancia_afip_path);
            $this->constancia_afip_path = null;
        }
    }
    /**
     * Método para eliminar el logo subido temporalmente.
     */
    public function removeLogo()
    {
        if ($this->logo_path) {
            Storage::disk('public')->delete($this->logo_path);
            $this->logo_path = null;
        }
    }
    /**
     * Método que se ejecuta cuando se actualiza el archivo de constancia de AFIP.
     */
    public function updatedConstanciaAfip()
    {
        $this->validate([
            'constancia_afip' => 'file|mimes:pdf,jpeg,png,svg|max:10240', // Acepta PDF e imágenes hasta 10MB
        ]);
        // Eliminar el archivo anterior si existe
        if ($this->constancia_afip_path) {
            Storage::disk('public')->delete($this->constancia_afip_path);
        }

        // Guardar el nuevo archivo temporalmente
        $this->constancia_afip_path = $this->constancia_afip->store('temp', 'public');
    }
    /**
     * Método que se ejecuta cuando se actualiza el logo.
     */
    public function updatedLogo()
    {
        $this->validate([
            'logo' => 'file|mimes:jpeg,png,svg|max:5120', // Solo imágenes hasta 5MB
        ]);
        // Eliminar el archivo anterior si existe
        if ($this->logo_path) {
            Storage::disk('public')->delete($this->logo_path);
        }
        // Guardar el nuevo logo temporalmente
        $this->logo_path = $this->logo->store('temp', 'public');
    }

    /**
     * Método para establecer la dirección basada en latitud y longitud utilizando la API de Nominatim.
     *
     * @param float $lat Latitud
     * @param float $long Longitud
     */
    public function setAddress($lat, $long)
    {
        try {
            $apiKey = config('services.google_maps.api_key');
            // Construir la URL para la geocodificación inversa con Google Maps
            $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng={$lat},{$long}&key={$apiKey}";

            // Realizar la consulta a Google Maps (sin verificar el certificado, si es necesario)
            $response = Http::withOptions(['verify' => false])->get($url);

            if ($response->successful()) {
                $data = $response->json();
                // Comprobar que el estado es OK y existen resultados
                if (isset($data['status']) && $data['status'] === 'OK' && !empty($data['results'])) {
                    // Usamos el primer resultado
                    $result = $data['results'][0];

                    // La respuesta de Google devuelve un arreglo de address_components.
                    // Se busca cada componente según su tipo.
                    $getComponent = function ($components, $type) {
                        foreach ($components as $component) {
                            if (in_array($type, $component['types'])) {
                                return $component['long_name'];
                            }
                        }
                        return '';
                    };

                    $components = $result['address_components'] ?? [];

                    $this->lat = $lat;
                    $this->long = $long;
                    $this->pais = $getComponent($components, 'country');
                    $this->provincia = $getComponent($components, 'administrative_area_level_1');
                    // Para localidad se puede buscar 'locality' y de no encontrar, 'sublocality'
                    $this->localidad = $getComponent($components, 'locality') ?: $getComponent($components, 'sublocality');
                    $this->ciudad = $this->localidad;
                    $this->calle = $getComponent($components, 'route');
                    $this->altura = $getComponent($components, 'street_number');
                    $this->domicilio = $this->calle;
                    $this->codigo_postal = $getComponent($components, 'postal_code');

                    $this->dispatch('addressUpdated');
                } else {
                    throw new \Exception('Error al obtener la dirección con Google Maps.');
                }
            } else {
                throw new \Exception('Error al realizar la solicitud a Google Maps.');
            }
        } catch (\Exception $e) {
            $this->dispatch('error', ['message' => 'No se pudo obtener la dirección.']);
        }
    }
    /**
     * Método para manejar la geolocalización y establecer la dirección.
     *
     * @param float $lat Latitud
     * @param float $long Longitud
     */
    public function handleGeolocation($lat, $long)
    {
        $this->setAddress($lat, $long);
    }

    /**
     * Método para verificar el CUIT con AFIP utilizando el AfipController.
     */
    public function verificarCuitAfip()
    {
        $this->codActividad = [];

        if (empty($this->cuit)) {
            $this->resetearVariables();
            return;
        }

        $afipController = new AfipController();
        $this->Afip = $afipController->verificarAfip($this->cuit);

        if ($this->esErrorAfip()) {
            $this->procesarErrorAfip();
        } else {
            $this->procesarDatosAfip();
        }
    }

    /**
     * Método para resetear ciertas variables.
     */
    private function resetearVariables()
    {
        $this->mensaje = null;
        $this->codActividad = [];
    }

    /**
     * Método para verificar si la respuesta de AFIP es un error.
     *
     * @return bool
     */
    private function esErrorAfip()
    {
        return is_string($this->Afip);
    }

    /**
     * Método para procesar el error recibido de AFIP.
     */
    private function procesarErrorAfip()
    {
        $this->mensaje = $this->Afip;
    }

    /**
     * Método para procesar los datos recibidos de AFIP.
     */
    private function procesarDatosAfip()
    {
        $this->mensaje = $this->Afip->datosGenerales->estadoClave;
        $this->razon_social = $this->Afip->datosGenerales->razonSocial;
        $this->Actividadprovincia = $this->Afip->datosGenerales->domicilioFiscal->descripcionProvincia;
        $this->procesarActividad();
    }

    /**
     * Método para procesar las actividades económicas según el tipo de empresa seleccionada.
     */
    private function procesarActividad()
    {
        if ($this->tipoSeleccionado == 1) {
            $this->codActividad = null;
            return;
        }

        foreach ($this->Afip->datosRegimenGeneral->actividad as $actividad) {
            $this->codActividad[] = [
                'descripcion' => $actividad->descripcionActividad
            ];
        }

        $this->BuscarActividad();
    }

    /**
     * Método para buscar actividades económicas basadas en un código.
     */
    public function BuscarActividad()
    {
        $this->actividad = null;
        if (!empty($this->cod)) {
            $this->actividad = ActividadesEconomicasModel::where('nombre', 'like', '%' . $this->cod . '%')
                ->where('estado', 1)
                ->get();
        } else {
            $this->actividad = ActividadesEconomicasModel::where('estado', 1)->get();
        }
    }

    /**
     * Método que actualiza la descripción del tipo de empresa.
     *
     * @param int $tipo Tipo de empresa seleccionado
     */
    private function setTipoDescripcion($tipo)
    {
        $tipoEmpresa = $this->tiposEmpresas->where('id_tipo_empresa', $tipo)->first();
        $this->tipoDescripcion = $tipoEmpresa ? $tipoEmpresa->nombre : '';
    }

    /**
     * Método para actualizar el panel actual del usuario.
     */
    private function updatePanel()
    {
        $user = User::find($this->NuevoUsuario->id,);
        if ($user) {
            if ($this->tipoSeleccionado == 1) {
                $user->panel_actual = 'Empresa';
                $user->save();
            } else {
                $user->panel_actual = 'Prestadora';
                $user->save();
            }
            $user->entidad = $this->cuit;
            $user->save();
        }
    }

    /**
     * Método para autorizar automáticamente la empresa después del registro.
     */
    private function autorizacionAutomatica()
    {
        $this->empresa = EmpresasModel::where('cuit', $this->cuit)->first();
        if ($this->empresa) {
            $this->empresa->autorizacion_empresa_reg = '1';
            $this->empresa->autorizacion_estado = '1';
            $this->empresa->save();
        }
    }

    public function render()
    {
        $this->dispatch('viewMapShown');
        $actividadesConServicios = ActividadesEconomicasModel::whereHas('serviciosActividades')->where('estado', 1)->paginate(6);
        $this->actividadesSinServicios = ActividadesEconomicasModel::whereDoesntHave('serviciosActividades')->where('estado', 1)->get();
        return view('livewire.empresas.create-empresas', [
            'actividadesSinServicios' => $this->actividadesSinServicios,
            'actividadesConServicios' => $actividadesConServicios,
            'tiposEmpresas' => $this->tiposEmpresas,
            'usuariosempresas' => $this->usuariosempresas,
            'tipoUbicaciones' => $this->tipoUbicaciones,
        ]);
    }

    /**
     * Método para crear un Place en Google Places y obtener el place_id.
     */
    private function obtenerPlaceIdDesdeCoordenadas($lat, $long)
    {
        $apiKey = config('services.google_maps.api_key');

        $response = Http::withOptions(['verify' => false])->get('https://maps.googleapis.com/maps/api/geocode/json', [
            'latlng' => "{$lat},{$long}",
            'key'    => $apiKey,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['results'])) {
                $placeId = $data['results'][0]['place_id'] ?? null;
                return $placeId;
            }
        }

        return null;
    }
}
