<?php

namespace App\Livewire\Ubicaciones;

use App\Models\TiposUbicacionesModel;
use Livewire\Component;
use App\Models\UbicacionesModel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class EditarUbicaciones extends Component
{
    public function render()
    {
        return view('livewire.ubicaciones.editar-ubicaciones');
    }

    public $open = false;
    public $ubicacionId;
    public $nombre, $pais, $provincia, $ciudad, $codigo_postal;
    public $calle, $altura, $piso, $depto;
    public $lat, $long;
    public $propiedad, $tipo;
    public $cuit_empresa, $cuil_gestor, $fecha_carga, $tiposUbicacion, $MultiplePiso;

    protected $listeners = ['abrirModalEditar'];

    protected function rules()
    {
        $rules = [
            'nombre'       => 'required|max:30',
            'pais'         => 'required|max:20',
            'provincia'    => 'required',
            'ciudad'       => 'required|max:20',
            'codigo_postal' => 'required',
            'calle'        => 'required|max:200',
            'altura'       => 'required|max:20',
            'tipo'         => 'required',
            'lat'          => 'required',
            'long'         => 'required',
            'piso'         => 'nullable|numeric',
        ];
        return $rules;
    }

    public function abrirModalEditar($data)
    {
        $this->open = true;
        $this->ubicacionId = $data;
        $u = UbicacionesModel::findOrFail($data);
        $this->tiposUbicacion = TiposUbicacionesModel::all();

        // Cargar datos al formulario
        $this->nombre        = $u->nombre;
        $this->pais          = $u->pais;
        $this->provincia     = $u->provincia;
        $this->ciudad        = $u->ciudad;
        $this->codigo_postal = $u->codigo_postal;
        $this->calle         = $u->calle;
        $this->altura        = $u->altura;
        $this->piso          = $u->piso;
        $this->depto         = $u->depto;
        $this->lat           = $u->lat;
        $this->long          = $u->long;
        $this->propiedad     = $u->propiedad;
        $this->tipo          = $u->tipo;
        $this->cuit_empresa  = $u->cuit_empresa;
        $this->cuil_gestor   = $u->cuil_gestor;
        $this->fecha_carga   = Carbon::parse($u->fecha_carga)->format('Y-m-d H:i:s');
        $this->MultiplePiso = $u->MultiplePiso;
        $this->setAddress($this->lat, $this->long);
        $this->dispatch('mapModalShown', ['mapModal1']);
    }

    public function handleGeolocation($lat, $long)
    {
        $this->lat  = $lat;
        $this->long = $long;
        $this->setAddress($lat, $long);
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

    public function actualizar()
    {
        $this->validate();

        DB::beginTransaction();
        try {
            $u = UbicacionesModel::findOrFail($this->ubicacionId);

            $u->update([
                'nombre'        => $this->nombre,
                'pais'          => $this->pais,
                'provincia'     => $this->provincia,
                'ciudad'        => $this->ciudad,
                'codigo_postal' => $this->codigo_postal,
                'calle'         => $this->calle,
                'altura'        => intval($this->altura),
                'piso'          => $this->piso,
                'depto'         => $this->depto,
                'lat'           => $this->lat,
                'long'          => $this->long,
                'tipo'          => $this->tipo,
                'multipisos'    => $this->MultiplePiso ?? 0,
                // no tocamos propiedad ni cuit_empresa/cuil_gestor desde aquí
            ]);

            DB::commit();

            $this->dispatch('Exito', [
                'title'   => 'Ubicación actualizada',
                'message' => 'Los cambios se guardaron correctamente.'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('errorInfo', [
                'title'   => 'Error al actualizar',
                'message' => $e->getMessage()
            ]);
        }
        $this->close();
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
            'altura',
            'piso',
            'depto',
            'lat',
            'long',
            'propiedad',
            'tipo',
            'cuit_empresa',
            'cuil_gestor',
            'fecha_carga'
        ]);
        $this->open = false;
        $this->dispatch('refreshLivewireTable');
    }
}
