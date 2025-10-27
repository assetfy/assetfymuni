<?php

namespace App\Livewire\Servicios\Prestadora;

use App\Helpers\IdHelper;
use App\Models\ActivosAtributosModel;
use App\Models\ActivosModel;
use App\Models\ServiciosModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\UbicacionesModel;
use App\Models\UsuariosEmpresasModel;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\Http;
use Livewire\WithFileUploads;
use Livewire\Component;

class PrestadoraServiciosSolicitudesEditarEstado extends Component
{
    use WithFileUploads;

    const ESTADO_ACEPTADO = 'Si';
    const ESTADO_ESPERANDO_CONFIRMACION = 'Esperando confirmacion del Cliente';
    const ESTADO_RECHAZADO = 'Rechazado';
    const MOTIVO_CANCELACION = 'Rechazado por la prestadora';
    protected $listeners = ['cotizar'];
    public $activeTab  = 'solicitud'; // Por defecto muestra solicitud
    public $open = false;
    public $servicios, $presupuesto, $precio, $activo, $ubicacion, $servicio, $aceptacion, $rutaDocumento, $fechaHora, $garantia, $fecha_garantia, $nombreActivo, $pais, $provincia,
        $localidad, $ciudad, $lat, $long, $ubicacionesNombre, $garantiaActivo, $fechaGarantia, $serviciosNombre, $fecha_modificada, $fechaServicio, $fecha_finalizacion, $representantesTecnicos;
    public $atributos = [];
    public $serviciosRealizados = [];
    public $realizacionDia = 'Si';
    public $fechaModificadaMensaje = false;

    public function rules()
    {
        if ($this->aceptacion === self::ESTADO_ACEPTADO) {
            return [
                'presupuesto' => 'required|file|mimes:pdf',
                'precio' => 'required|numeric',
                'aceptacion' => 'required|in:Si,No',
                'garantia' => 'nullable|in:Si,No',
                'fecha_garantia' => 'nullable|numeric|min:1',
            ];
        } else {
            return [
                'aceptacion' => 'required|in:Si,No',
            ];
        }
    }

    public function actualizar()
    {
        $this->validate();
        $this->guardarDatos();
        $this->close();
    }

    private function guardarDatos()
    {
        try {
            if ($this->aceptacion === self::ESTADO_ACEPTADO) {
                // Formatear la fecha
                $fechaHoraFormateada = $this->formatoFecha($this->fechaHora);
                // Validar y guardar el archivo de presupuesto
                if ($this->presupuesto instanceof \Illuminate\Http\UploadedFile) {
                    $filename = 'presupuesto_' . uniqid() . '.' . $this->presupuesto->extension();
                    // 1) Sube a S3 dentro de StorageMvp/presupuesto 
                    $path = $this->presupuesto->storeAs(
                        'StorageMvp/presupuesto',  // carpeta en S3
                        $filename,
                        's3'
                    );
                    // 2) misma ruta en la bd
                    $this->servicios->presupuesto = $path;
                }
                // Actualizar los datos del servicio
                $this->servicios->precio = $this->precio;
                $this->servicios->fechaHora = $fechaHoraFormateada;
                $this->servicios->estado_presupuesto = self::ESTADO_ESPERANDO_CONFIRMACION;
                $this->servicios->garantia = $this->garantia ?? 'No';
                if ($this->realizacionDia == 'No') {
                    $this->servicios->fecha_finalizacion =  $this->formatoFecha($this->fecha_finalizacion);
                } else {
                    $this->servicios->fecha_finalizacion =  $this->formatoFecha($this->fechaHora);
                }
                // Guardar la fecha de garantía directamente si está disponible
                $this->servicios->dias_garantia =   $this->fecha_garantia;
            } else {
                // Manejo de estado rechazado
                $this->servicios->presupuesto = null;
                $this->servicios->precio = null;
                $this->servicios->motivo_cancelacion = self::MOTIVO_CANCELACION;
                $this->servicios->estado_presupuesto = self::ESTADO_RECHAZADO;
                $this->actualizarEstadoActivo();
            }
            // Actualizar el estado general del servicio
            $this->servicios->estado = $this->aceptacion;
            $this->servicios->save();
            $this->dispatch('lucky');
        } catch (\Exception $e) {
            // Manejo de errores
            $this->dispatch('errorServicio');
        }
    }

    private function actualizarEstadoActivo()
    {
        $this->activo = ActivosModel::where('id_activo', $this->servicios->id_activo)->get()->first();
        $this->activo->id_estado_sit_general = '1';
        $this->activo->save();
    }

    private function modificacionFecha($fechaHoraFormateada)
    {
        return $fechaHoraFormateada === $this->servicios->fechaHora;
    }

    private function formatoFecha($fecha)
    {
        return date('Y-m-d H:i:s', strtotime($fecha));
    }

    public function close()
    {
        $this->reset(['presupuesto', 'precio', 'aceptacion', 'fechaHora']);
        $this->dispatch('refreshLivewireTable');
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.servicios.prestadora.prestadora-servicios-solicitudes-editar-estado');
    }

    public function cotizar($data)
    {
        $this->servicios = SolicitudesServiciosModel::find($data);
        if ($this->servicios instanceof \Illuminate\Database\Eloquent\Collection && $this->servicios->count() > 0) {
            $this->servicios = $this->servicios->first();
        }

        if ($this->servicios && $this->servicios instanceof SolicitudesServiciosModel) {
            // Formatea la fecha para input datetime-local
            $this->fechaHora = $this->servicios->fechaHora
                ? date('Y-m-d\TH:i', strtotime($this->servicios->fechaHora))
                : null;
            $this->serviciosNombre =  ServiciosModel::where('id_servicio', $this->servicios->id_servicio)->value('nombre');
            $this->activo = ActivosModel::where('id_activo', $this->servicios->id_activo)->get();
            $this->garantiaActivo($this->activo);
            $this->nombreActivo = $this->activo->value('nombre');
            $this->garantiaActivo = $this->activo->value('nombre');
            $this->ubicacion = $this->activo->pluck('id_ubicacion');
            $this->UbicacionActivo($this->ubicacion);
            $this->servicio = $this->servicios->servicios;
            $this->atributos = $this->atributosActivos($this->servicios->id_activo);


            // Cargar atributos del activo

            // Obtener servicios realizados por la prestadora actual para este activo
            $prestadoraActual = IdHelper::idEmpresa(); // O usa auth() si corresponde
            $this->representantesTecnicos = $this->UsuariosTecnicos($prestadoraActual);
            $this->serviciosRealizados = SolicitudesServiciosModel::where('id_activo', $this->activo->pluck('id_activo'))
                ->where('empresa_prestadora', $prestadoraActual)
                ->where('estado_presupuesto', 'Servicio Realizado, Solicitud Cerrada')
                ->get();
        }
        $this->open = true;
        $this->setAddress($this->lat, $this->long);
        $this->dispatch('showActivoMap', [
            'lat'   => $this->lat,
            'lng'   => $this->long,
            'mapId' => 'mapActivosUbicacion',
        ]);
    }

    private function UsuariosTecnicos($prestadoraActual)
    {
        return UsuariosEmpresasModel::with('usuarios')
            ->where('cuit', $prestadoraActual)
            ->where('es_representante_tecnico', 'Si')
            ->get();
    }

    private function garantiaActivo($activo)
    {
        $this->garantiaActivo = $activo->pluck('garantia_vigente');
        if ($this->garantiaActivo == 'Si') {
            $this->fecha_garantia = $activo->pluck('fecha_garantia');
        }
    }

    private function atributosActivos($activo)
    {
        return ActivosAtributosModel::where('id_activo', $activo)
            ->with([
                'atributo.tiposCampos',
                'atributo.unidadMedida',
            ])
            ->get();
    }

    private function UbicacionActivo($ubicacionId)
    {
        // Obtener la ubicación esperada
        $ubicacion = UbicacionesModel::where('id_ubicacion', $ubicacionId)->get()->first();
        if ($ubicacion) {
            $this->lat = floatval($ubicacion->lat);
            $this->long = floatval($ubicacion->long);
            $this->ubicacionesNombre =  $ubicacion->nombre;
        } else {
            $this->lat = null;
            $this->long = null;
        }
    }

    public function setAddress($lat, $long)
    {
        try {
            // Realizar una solicitud HTTP a la API de Nominatim para obtener la dirección inversa
            $response = Http::withHeaders([
                'User-Agent' => 'Laravel/Livewire'
            ])->withoutVerifying() // Desactivar la verificación SSL
                ->get('https://nominatim.openstreetmap.org/reverse', [
                    'lat' => $lat,
                    'lon' => $long,
                    'format' => 'json',
                ]);
            if ($response->successful()) {
                $address = $response->json('address');
                // Asignar los valores obtenidos a las propiedades correspondientes
                $this->pais = $address['country'] ?? '';
                $this->provincia = $address['state'] ?? '';
                $this->localidad = $address['city'] ?? $address['town'] ?? $address['village'] ?? '';
                $this->ciudad = $this->localidad;
                // Disparar un evento para actualizar la dirección en el frontend
                $this->dispatch('addressUpdated');
            } else {
                // Manejar el error de la solicitud HTTP
                throw new \Exception('Error al obtener la dirección.');
            }
        } catch (\Exception $e) {
            // Manejar excepciones y registrar el error
            logger()->error('Exception occurred', ['exception' => $e->getMessage()]);
            $this->dispatch('error', ['message' => 'No se pudo obtener la dirección.']);
        }
    }

    public function updatedFechaHora($value)
    {
        if ($this->servicios->fechaHora !== $value) {
            $this->fechaModificadaMensaje = true;
        }
    }
}
