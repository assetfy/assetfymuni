<?php

namespace App\Livewire\Servicios;

use App\Helpers\IdHelper;
use App\Livewire\Servicios\Activos\ActivosServicios;
use App\Models\CalificacionesModel;
use App\Models\EmpresasModel;
use App\Models\MisProveedoresModel;
use App\Models\ServiciosActivosModel;
use App\Models\SolicitudesServiciosModel;
use App\Services\CalificacionesService;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ProveedoresDetalle extends Component
{
    protected $listeners = ['PrestadoraDetalle'];
    public $open, $nombreEmpresa, $descripcion, $logo, $actividadEconomica, $Esprovedor,
        $provincia, $localidad, $empresa, $lat, $long, $url, $places, $rating, $userRatingsTotal, $porcentajeContratacion1, $porcentajeContratacion0, $puntajeAssetFly, $serviciosContratados, $activosServicios;
    public $activeTab = 'assetfy';
    public $filtro = 'positiva';
    protected ?CalificacionesService $calificacionesService = null;


    public function render()
    {
        return view('livewire.servicios.proveedores-detalle');
    }

    public function mount(CalificacionesService $calificacionesService)
    {
        $this->calificacionesService = $calificacionesService;
    }

    public function hydrate()
    {
        if (is_null($this->calificacionesService)) {
            $this->calificacionesService = app(CalificacionesService::class);
        }
    }

    public function datos(EmpresasModel $empresa)
    {
        $this->empresa            = $empresa;
        $this->nombreEmpresa      = $this->empresa->razon_social;
        $this->descripcion        = $this->empresa->descripcion_actividad;
        $this->actividadEconomica = $this->empresa->actividades->nombre  ?? '';
        $this->provincia          = $this->empresa->provincia;
        $this->localidad          = $this->empresa->localidad;
        $this->logo               = $this->empresa->logo;
        $this->lat                = $this->empresa->lat;   // Latitud
        $this->long               = $this->empresa->long;  // Longitud
        $this->url                = $this->empresa->url;
        $this->places             = $this->empresa->places;
        $this->rating          = null;
        $this->userRatingsTotal = null;
        if (!empty($this->places)) {
            $this->getGoogleRating();
        }
        $this->calificaciones();
        $this->serviciosContratados = $this->ServiciosContratados();
        $this->reseñasPrestadora();
        $this->Esprovedor = MisProveedoresModel::where('cuit', $empresa->cuit)
            ->where('empresa', IdHelper::empresaActual()->cuit)->first();
    }

    public function eliminarfav()
    {
        $eliminar = MisProveedoresModel::where('cuit', $this->Esprovedor->cuit)->first();
        $eliminar->delete;
        $this->dispatch('eliminado');
        $this->dispatch('refreshLivewireTable');
        $this->open = false;
    }

    // Método para actualizar el filtro de reseñas:
    public function setReviewFilter($filtro)
    {
        $this->filtro = $filtro;
    }

    private function calificaciones()
    {
        $this->porcentajeContratacion1 = $this->calificacionesService->obtenerPorcentajeContratacion1($this->empresa->cuit);
        $this->porcentajeContratacion0 = $this->calificacionesService->obtenerPorcentajeContratacion0($this->empresa->cuit);
        // 2. Puntaje en Asset Fly (local):
        // Se calcula a partir de la diferencia neta entre % recomendados y no recomendados.
        // Fórmula: ((%Recomendado - %NoRecomendado) + 100) / 200 * 5
        $this->puntajeAssetFly = (($this->porcentajeContratacion1 - $this->porcentajeContratacion0) + 100) / 200 * 5;
        $this->puntajeAssetFly = round($this->puntajeAssetFly, 2);
    }

    private function ServiciosContratados()
    {
        return SolicitudesServiciosModel::where('empresa_prestadora', $this->empresa->cuit)
            ->where('estado_presupuesto', 'Servicio Realizado, Solicitud Cerrada')->count();
    }

    private function reseñasPrestadora()
    {
        $this->activosServicios = ServiciosActivosModel::where('proveedor', $this->empresa->cuit)->get();
        $idServicios = $this->activosServicios->pluck('id_serviciosActivos');
        $this->activosServicios = CalificacionesModel::whereIn('id_serviciosActivos', $idServicios)->get();
    }


    public function PrestadoraDetalle($data)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        $this->empresa = EmpresasModel::find($data);
        $this->datos($this->empresa);
        $this->open = true;

        $this->dispatch('showProveedorMap', [
            // Convertimos a float o double para que JS reciba un número, no una string
            'lat' => floatval($this->lat),
            'lng' => floatval($this->long),
        ]);
    }

    public function agregarFav()
    {
        if ($this->empresa) {
            MisProveedoresModel::create([
                'existe_en_la_plataforma' => 'Si',
                'cuit' => $this->empresa->cuit,
                'razon_social' => $this->empresa->razon_social,
                'localidad' => $this->empresa->localidad,
                'provincia' => $this->empresa->provincia,
                'id_usuario' => auth()->id(),
            ]);
            $this->dispatch('lucky', 'Proveedor agregado a favoritos.');
            $this->dispatch('refreshLivewireTable');
        } else {
            $this->dispatch('notificacion', 'No se encontró la empresa.');
        }
        $this->open = false;
    }
    /**
     * Llama a la API de Google Places Details para obtener rating y user_ratings_total
     */
    private function getGoogleRating()
    {
        $apiKey = config('services.google_maps.api_key'); // clave API en .env / config/services.php
        $fields = 'rating,user_ratings_total';            // Campos que necesitas

        // para saltar la comprovacion SSL
        $response = Http::withoutVerifying()->get('https://maps.googleapis.com/maps/api/place/details/json', [
            'key'      => $apiKey,
            'place_id' => $this->places,
            'fields'   => $fields,
        ]);

        if ($response->successful()) {
            $data = $response->json();
            if (!empty($data['result'])) {
                $result = $data['result'];
                $this->rating           = $result['rating'] ?? null;
                $this->userRatingsTotal = $result['user_ratings_total'] ?? 0;
            } else {
                $this->rating           = null;
                $this->userRatingsTotal = 0;
            }
        } else {
            // Manejo de error en la solicitud
            $this->rating           = null;
            $this->userRatingsTotal = 0;
        }
    }


    public function PedirCotizacion()
    {

        $this->dispatch('solicitarCotizacion', ['data' =>  $this->empresa->cuit])->to('servicios.prestadora-solicitud-cotizacion');
    }
}
