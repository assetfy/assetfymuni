<?php

namespace App\Livewire\Actividad;

use App\Models\EstadoActividadesEconomicasModel;
use App\Models\ActividadesEconomicasModel;
use App\Traits\VerificacionTrait;
use App\Traits\SortableTrait;
use App\Models\EmpresasModel;
use Livewire\Component;

class CargarReguladora extends Component
{
    use VerificacionTrait;
    use SortableTrait;

    public $empresas = [], $actividades, $value, $cuit, $selectedProvincia, $selectedActividad, $renovacion;
    public $open = false;
    public $search;
    public $noEmpresas = false;
    public $allFieldsComplete = false;
    protected $listeners = ['CargarReguladora'];

    protected $rules = [
        'selectedActividad' => 'required',
        'renovacion' => 'required|integer|min:1',
        'cuit' => 'required',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
        $this->checkAllFieldsComplete();
        if ($propertyName === 'selectedActividad') {
            $this->loadEmpresas();
        }
    }

    public function mount()
    {
        $this->actividades = ActividadesEconomicasModel::where('estado', '1')->get();
    }

    public function loadEmpresas()
    {
        if (empty($this->selectedActividad)) {
            $this->empresas = []; // Resetear empresas si no hay actividad seleccionada
            $this->noEmpresas = false;
            return;
        }

        $this->empresas = EmpresasModel::where('COD_ACTIVIDAD', $this->selectedActividad)
                                    ->where('estado', 'Aceptado')
                                    ->where('tipo', '3')
                                    ->get();

    }

    public function selectEmpresa($razon_social, $cuit)
    {
        $this->search = $razon_social;
        $this->cuit = $cuit;
        $this->empresas = [];
    }

    public function checkAllFieldsComplete()
    {
        $this->allFieldsComplete = $this->selectedProvincia && $this->selectedActividad && $this->renovacion && $this->cuit;
    }

    public function render()
    {
        $estados = EmpresasModel::where('cuit', '303030')->get();
        return view('livewire.actividad.cargar-reguladora', compact('estados'));
    }

    public function save()
    {
        $this->validate();

        if ($this->noEmpresas) {
            session()->flash('message', 'No existe controladora para ese código de actividad');
            return;
        }

        $this->registro();

        $this->dispatch('lucky');
        $this->close();
    }

    private function registro()
    {
        $valoresNuevos = [
            'cuit' => '303030',
            'cod_actividad' => $this->selectedActividad,
            'entidad_reguladora' => $this->cuit,
            'renovacion_cada_x_dias' => $this->renovacion,
        ];

        EstadoActividadesEconomicasModel::create($valoresNuevos);
        $this->dispatch('refreshLivewireTable');
    }

    public function close()
    {
        $this->reset([
            'cuit',
            'selectedActividad',
            'selectedProvincia',
            'renovacion',
            'search',
            'empresas',
            'noEmpresas',
            'allFieldsComplete'
        ]);

        $this->open = false;
    }

    public function CargarReguladora(){
        $this->open = true;
    }
}
