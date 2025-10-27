<?php

namespace App\Livewire\Servicios\ActividadesEconomicas;

use App\Livewire\Servicios\Servicios;
use App\Models\ServiciosActividadesEconomicasModel;
use App\Models\EstadoActividadesEconomicasModel;
use App\Models\ActividadesEconomicasModel;
use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use App\Models\ServiciosModel;
use Livewire\Component;

class CreateServiciosActividadesEconomicas extends Component
{
    protected $listeners = ['CrearServicioActividadEconomica'];
    use VerificacionTrait;
    public $municipio, $localidad, $tiene_vencimiento, $mensual_o_x_dias, $cantidad_dias_o_meses, $es_regulada, $servicios,
        $servicios_estado, $actividades, $id_servicio, $id_actividad, $panel;
    public $open = false;

    public function mount()
    {
        $this->servicios = ServiciosModel::all();
        $this->actividades = ActividadesEconomicasModel::where('estado', 1)->get();
        $this->panel();
    }

    private function panel()
    {
        $user = auth()->user();
        if ($user->panel_actual == 'Estado') {
            $this->panel = 1;
        }
    }

    protected function rules()
    {
        $rules = [
            'id_servicio' => 'required',
            'id_actividad' => 'required',
        ];
        if ($this->panel == 1) {
            $rules = array_merge($rules, [
                'tiene_vencimiento' => 'required|max:2',
                'mensual_o_x_dias' => 'required_if:tiene_vencimiento,Si|max:10',
                'cantidad_dias_o_meses' => 'required_if:tiene_vencimiento,Si|integer|min:1|max:30',
            ]);
        }
        return $rules;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function save()
    {
        $this->validate();
        $this->datos();
        $this->crearRegistro();
        $this->dispatch('refreshLivewireTable');
        $this->close();
    }

    private function datos()
    {
        if ($this->panel == 1) {
            $this->verificaRegulacion();
        } else {
            $this->es_regulada = 'No';
        }
    }

    private function verificaRegulacion()
    {
        $this->municipio = EstadoActividadesEconomicasModel::where('cod_actividad', $this->id_actividad)->value('cuit');
        if ($this->municipio) {
            $this->localidad = 'Corrientes';
            $this->es_regulada = 'Si';
        }
    }
    private function crearRegistro()
    {
        $servicioAsociado = ServiciosActividadesEconomicasModel::where('id_servicio', $this->id_servicio)
            ->where('cod_actividad', $this->id_actividad)
            ->exists();

            // dd($servicioAsociado);
        if ($servicioAsociado) {
            $this->dispatch('warning', ['message' => 'El servicio ya está asociado a la actividad.']);
            return;
        } else {
            ServiciosActividadesEconomicasModel::create(['id_servicio' => $this->id_servicio,
            'cod_actividad' => $this->id_actividad,
            'cuit_municipio' => $this->municipio,
            'localidad' =>  $this->localidad,
            'tiene_vencimiento' => $this->tiene_vencimiento,
            'mensual_o_x_dias' => $this->mensual_o_x_dias,
            'cantidad_dias_o_meses' => $this->cantidad_dias_o_meses,
            'es_regulada' => $this->es_regulada]);

            $this->dispatch('Exito', ['message' => 'Servicio asociado a la actividad económica correctamente.']);
        }
    }

    public function close()
    {
        $this->reset(['id_servicio', 'id_actividad', 'tiene_vencimiento', 'mensual_o_x_dias', 'cantidad_dias_o_meses', 'es_regulada']);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.servicios.actividadeseconomicas.create-servicios-actividades-economicas');
    }

    public function CrearServicioActividadEconomica()
    {
        $this->openModal();
    }

    public function openModal()
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }
}
