<?php
namespace App\Livewire\Actividad;

use App\Models\User;
use Livewire\Component;
use App\Helpers\IdHelper;
use App\Traits\SortableTrait;
use App\Models\EmpresasModel;
use App\Models\UbicacionesModel;
use App\Http\Controllers\afipController;
use App\Models\EmpresasActividadesModel;
use App\Models\ActividadesEconomicasModel;
use App\Models\EstadoActividadesEconomicasModel;
use App\Services\MiddlewareInvoker;

class ActividadNuevaActividadEmpresa extends Component
{
    public $actividad,$cuit,$cod_actividad,$provincia,$localidad,$Afip,$codActividad,$datos,$datosEmpresa,$ubicaciones,$reguladora,$selectedActividad,
    $razonSocial,$piso,$codigo_postal,$entidad;
    use SortableTrait;
    public $open = false;
    protected $listeners = ['CrearNuevaActividadEmpresa'];

    protected $rules = 
    [
        'selectedActividad' => 'required',
        'ubicaciones'  => 'required',
    ];

    public function CrearNuevaActividadEmpresa()
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->manejarDatos();
        }
    }

    public function render()
    {
        return view('livewire.actividad.actividad-nueva-actividad-empresa');
    }

    public function mount()
    {
        $this->manejarDatos();
    }

    public function ubicacionesEmpresa($datosEmpresa){
        $this->ubicaciones = UbicacionesModel::where('cuit',$datosEmpresa)->get();
    }

    public function manejarDatos()
    {
        $this->datos = IdHelper::identificador();
        $cuit_default = $this->datos;
        
        $user = User::where('cuil', $this->datos)->first();
        if ($user) {
            $cuit_default = $user->entidad;
        }
        $this->cargarDatosEmpresa($cuit_default);
    }

    private function cargarDatosEmpresa($cuit)
    {
        $this->datosEmpresa = EmpresasModel::where('cuit', $cuit)->get();

        foreach ($this->datosEmpresa as $empresa) {
            $this->cuit = $empresa->cuit;
            $this->razonSocial = $empresa->razon_social;
            $this->provincia = $empresa->provincia;
            $this->localidad = $empresa->localidad;
            $this->piso = $empresa->piso;
            $this->codigo_postal = $empresa->codigo_posta;

            $this->ubicacionesEmpresa($this->cuit);
            $this->verificarCuitAfip($this->cuit);
        }
    }

    public  function reguladora(){
        $this->reguladora = EstadoActividadesEconomicasModel::where('cod_actividad',$this->selectedActividad)->get();
        foreach ($this->reguladora as $reg) {
            // Acceder a los datos de cada empresa
            $this->reguladora = $reg->cuit;
            $this->entidad = $reg->empresa_reguladora_autorizante;
        }
    }

    public function save(){
        $this->validate();
        $this->crearActividad();
        $this->eventos();
        $this->close();
    }

    private function crearActividad(){
        $this->reguladora();
        EmpresasActividadesModel::create([
            'cuit' =>$this->cuit,
            'razon_social' => $this->razonSocial,  
            'provincia' =>   $this->provincia,
            'localidad' =>   $this->localidad,
            'cod_actividad' => $this->selectedActividad,
            'estado_autorizante' => $this->reguladora,
            'empresa_reguladora_autorizante' => $this->entidad ,
            'domicilio'=>  $this->ubicaciones,
            'piso' => $this->piso,
            'codigo_postal' => $this->codigo_postal,
        ]);
    }

    public function close(){
        $this->reset(['cuit']);
        $this->open = false;
    }

    private function verificarCuitAfip($cuit)
    {   
        $afipController = new afipController();
        $this->Afip = $afipController->verificarAfip($cuit);

        if(!isset($this->Afip->datosRegimenGeneral->actividad)) {
            // Si no hay actividad asociada para la empresa registrada, no hacer nada
            return;
        }
        $codActividadNoExistentes = $this->filtrarActividadesNoExistentes();
        // Pasar los códigos de actividad filtrados a la función BuscarActividad
        $this->BuscarActividad($codActividadNoExistentes);
    }

    private function filtrarActividadesNoExistentes() {
        $actividadesExistentesNormalized = $this->obtenerActividadesExistentesNormalized();
        // Filtrar $this->codActividad para obtener solo aquellos que no están en $actividadesExistentes
        $codActividadNoExistentes = collect($this->Afip->datosRegimenGeneral->actividad)
            ->reject(function ($actividad) use ($actividadesExistentesNormalized) {
                // Normalizamos el valor de la actividad para hacer la comparación insensible a mayúsculas y minúsculas
                $actividadNormalized = strtolower($actividad->descripcionActividad);
                // Verificar si la descripción de la actividad no está en $actividadesExistentesNormalized
                return in_array($actividadNormalized, $actividadesExistentesNormalized);
            })->pluck('descripcionActividad')->toArray();
        return $codActividadNoExistentes;
    }

    private function obtenerActividadesExistentesNormalized() {
        // Obtener los códigos de actividad existentes para esta empresa en EmpresasActividadesModel
        $codigosExistentes = EmpresasActividadesModel::where('cuit', $this->cuit)
            ->pluck('cod_actividad')->toArray();
        // Obtener las descripciones de actividad existentes para esta empresa en ActividadesEconomicasModel
        $actividadesExistentes = ActividadesEconomicasModel::whereIn('COD_ACTIVIDAD', $codigosExistentes)
            ->pluck('descripcion')->toArray();
        // Normalizamos los valores de las actividades existentes para hacer la comparación insensible a mayúsculas y minúsculas
        $actividadesExistentesNormalized = array_map('strtolower', $actividadesExistentes);

        return $actividadesExistentesNormalized;
    }

    private function BuscarActividad($codActividadNoExistentes)
    {
        if (!empty($codActividadNoExistentes)) {
            // Filtrar las actividades económicas en base a los valores del array $this->codActividad
            $actividadPorCodigoActividad = ActividadesEconomicasModel::whereIn('nombre', $codActividadNoExistentes)
                ->where('estado', 1)
                ->get();
            // Convertir $actividadPorCodigoActividad a una colección si no lo es
            $this->actividad = ($actividadPorCodigoActividad instanceof \Illuminate\Support\Collection) ? $actividadPorCodigoActividad : collect($actividadPorCodigoActividad);
        } else {
            // Si no hay término de búsqueda por actividad específica o por código, reiniciar la variable $this->actividad a null
            $this->actividad = null;
        }
    }
}
