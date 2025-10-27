<?php

namespace App\Livewire\Servicios\Prestadora;

use App\Models\ActivosModel;
use App\Models\EmpresasModel;
use App\Models\FotosServicioModel;
use App\Models\ServiciosActividadesEconomicasModel;
use App\Models\ServiciosActivosModel;
use App\Models\ServiciosModel;
use App\Models\SolicitudesServiciosModel;
use App\Models\User;
use App\Models\UsuariosEmpresasModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class ServiciosRealizarServicios extends Component
{
    use WithFileUploads;

    // Propiedades públicas
    public $servicioId;
    public $servicio;
    public $activoDelServicio;
    public $solicitante;
    public $servicios;
    public $activo;
    public $fechaHora;
    public $regulada;
    public $user;
    public $comentarios;
    public $tecnicoAsignado;

    public $fotos = []; // Todas las fotos (subidas y capturadas)
    public $nuevasFotos = []; // Nuevas fotos subidas
    public $capturedPhotos = []; // Fotos capturadas desde la cámara
    public $previousUrl;

    // Reglas de validación
    protected $rules = [
        'comentarios' => 'required|string',
        'fechaHora' => 'required|date',
        'nuevasFotos.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048', // Validar cada foto subida
        'capturedPhotos' => 'nullable|array|max:10',
        'capturedPhotos.*' => 'string', // Data URLs de las imágenes capturadas
    ];

    protected $listeners = ['visitar'];

    public function visitar($data)
    {
        $this->mount($data);
    }


    public function mount($servicio)
    {
        $this->servicio = SolicitudesServiciosModel::findOrFail($servicio);
        $this->servicios = ServiciosModel::where('id_servicio', $this->servicio->id_servicio)->get();
        $this->activo = ActivosModel::findOrFail($this->servicio->id_activo);
        $this->activoDelServicio = ActivosModel::where('id_activo', $this->servicio->id_activo)->get();
        $this->solicitante = $this->obtenerSolicitante($this->servicio);
        $this->fechaHora = $this->formatDatetimeForInput($this->servicio->fechaHora);
        if ($this->servicio->tecnico_id != null) {
            $this->tecnicoAsignado = User::where('id', $this->servicio->tecnico_id)->first();
        }
    }

    public function render()
    {
        $this->previousUrl = session('previous_url', url()->previous());

        return view('livewire.servicios.prestadora.servicios-realizar-servicios', [
            'servicio' => $this->servicio,
            'servicios' => $this->servicios,
            'activo' => $this->activo,
            'fechaHora' => $this->fechaHora,
            'previousUrl' => $this->previousUrl,
        ]);
    }

    public function obtenerSolicitante($servicio)
    {
        if ($servicio->empresa_solicitante) {
            return EmpresasModel::where('cuit', $servicio->empresa_solicitante)->first();
        } else {
            return User::find($servicio->id_solicitante);
        }
    }

    /**
     * Maneja las nuevas fotos añadidas al input de archivos
     */
    public function updatedNuevasFotos()
    {
        foreach ($this->nuevasFotos as $newFoto) {
            $this->fotos[] = $newFoto;
        }

        $this->reset('nuevasFotos');
    }

    /**
     * Guarda los datos del servicio y las fotos
     */
    public function save()
    {
        $this->validate();
        $this->guardarDatos();
    }

    private function guardarDatos()
    {
        $id_relacion = UsuariosEmpresasModel::where('id_usuario', Auth::user()->id)
            ->where('cuit', $this->servicio->empresa_prestadora)->first();
        $fechaHoraFormateada = $this->formatoFecha($this->fechaHora);
        $this->usuarios();
        $datosExtra = $this->tipoActividad() ? [
            'estado_vigencia' => $this->regulada->mensual_o_x_dias,
            'avalado' => $this->regulada->cuit_municipio,
            'representante_tecnico' => $this->user->id,
        ] : [];

        $servicioActivo = ServiciosActivosModel::create([
            'id_servicio' => $this->servicio->id_servicio,
            'id_activo' => $this->activo->id_activo,
            'id_subcategoria_activo' => $this->activo->id_subcategoria,
            'id_categoria_activo' => $this->activo->id_categoria,
            'id_tipo_activo' => $this->activo->id_tipo,
            'proveedor' => $this->servicio->empresa_prestadora,
            'fecha' => $fechaHoraFormateada,
            'comentarios' => $this->comentarios,
            'solicitud' => $this->servicio->id_solicitud,
            'id_usuario' => $this->user->id,
            'representante_tecnico' => $this->tecnicoAsignado->id ?? $this->user->id,
            'id_relacion_usuario' => $id_relacion->id_relacion,
        ] + $datosExtra);

        // Guardar las fotos subidas
        $this->guardarFotosSubidas($servicioActivo->id);

        // Guardar las fotos capturadas
        $this->guardarFotosCapturadas($servicioActivo->id);

        $this->actualizarEstado();
        $this->dispatch('lucky');
        $this->close();
    }

    private function guardarFotosSubidas($servicioActivoId)
    {
        foreach ($this->fotos as $foto) {
            if ($foto instanceof \Illuminate\Http\UploadedFile) {
                // en lugar de store('visitas','public')
                $path = $foto->store('StorageMvp/visitas', 's3');

                FotosServicioModel::create([
                    'id_solicitud'         => $this->servicio->id_solicitud,
                    'id_servicio_activo'   => $servicioActivoId,
                    // guardamos la ruta S3
                    'fotos'                => $path,
                ]);
            }
        }
    }

    private function guardarFotosCapturadas($servicioActivoId)
    {
        foreach ($this->capturedPhotos as $fotoDataUrl) {
            $data = explode(',', $fotoDataUrl);
            if (count($data) !== 2) continue;
            $decoded = base64_decode($data[1]);
            if ($decoded === false) continue;

            $filename = 'captured_' . uniqid() . '.png';
            $path     = 'StorageMvp/visitas/' . $filename;

            // en lugar de disk('public') usar disk('s3')
            Storage::disk('s3')->put($path, $decoded);

            FotosServicioModel::create([
                'id_solicitud'         => $this->servicio->id_solicitud,
                'id_servicio_activo'   => $servicioActivoId,
                'fotos'                => $path,
            ]);
        }
    }

    private function actualizarEstado()
    {
        $datos = SolicitudesServiciosModel::find($this->servicio->id_solicitud);
        if ($datos) {
            $datos->estado_presupuesto = 'Servicio Realizado, Solicitud Cerrada';
            $datos->save();
            $this->actualizarEstadoActivo();
        }
    }

    private function actualizarEstadoActivo()
    {
        $this->activo->id_estado_sit_general = '1';
        $this->activo->save();
    }

    public function tipoActividad()
    {
        $this->regulada = ServiciosActividadesEconomicasModel::where('id_servicio', $this->servicio->id_servicio)->first();

        return $this->regulada && $this->regulada->es_regulada === 'Si';
    }

    public function close()
    {
        $this->reset(['comentarios', 'fechaHora', 'servicio', 'fotos', 'capturedPhotos', 'nuevasFotos']);
        $this->redirect(route('servicios-activos-pendientes'));
    }

    public function usuarios()
    {
        $this->user = Auth::user();
    }

    public function formatoFecha($fecha)
    {
        $timestamp = strtotime($fecha);
        if ($timestamp === false) {
            return null;
        }
        return date('Y-m-d H:i:s', $timestamp);
    }

    public function formatDatetimeForInput($fecha)
    {
        return date('Y-m-d\TH:i', strtotime($fecha));
    }

    public function saveCapturedPhoto($imageData)
    {
        if ((count($this->fotos) + count($this->capturedPhotos)) < 10) {
            $this->capturedPhotos[] = $imageData;
        }
    }

    public function removeFoto($index)
    {
        if (isset($this->fotos[$index])) {
            unset($this->fotos[$index]);
            $this->fotos = array_values($this->fotos); // Reindexar el array
        }
    }

    public function removeCapturedPhoto($index)
    {
        if (isset($this->capturedPhotos[$index])) {
            unset($this->capturedPhotos[$index]);
            $this->capturedPhotos = array_values($this->capturedPhotos); // Reindexar el array
        }
    }
}
