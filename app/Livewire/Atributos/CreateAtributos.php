<?php

namespace App\Livewire\Atributos;

use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use App\Models\TiposCamposModel;
use App\Models\AtributosModel;
use App\Models\AtributosValoresModel;
use App\Models\UnidadModel;
use Livewire\Component;

class CreateAtributos extends Component
{
    use VerificacionTrait;
    public $open = false;
    public $nombre, $tipo_campo, $descripcion, $unidad_medida, $categorias2, $atributo,$updatedTipoCampo,$esMultiple;
    public $esPredefinido;
    public $valores = [];

    protected $listeners = ['crearAtributos'];

    protected $rules = [
        'nombre' => 'required|max:30',
        'tipo_campo' => 'required',
    ];

    public function save()
    {
        $this->validate();
        $atributo = AtributosModel::create([
            'nombre' => $this->nombre,
            'tipo_campo' => $this->tipo_campo,
            'descripcion' => $this->descripcion,
            'unidad_medida' => $this->unidad_medida,
            'predefinido' => $this->esPredefinido  ?? 'No',
            'SelectM'  => $this->esMultiple ?? 'No',
        ]);

        if ($this->esPredefinido === 'Si' && !empty($this->valores)) {
            $this->guardarValoresPredefinidos($atributo->id_atributo);
        }

        $this->reset(['nombre', 'tipo_campo', 'descripcion', 'unidad_medida', 'esPredefinido', 'valores']);
        $this->dispatch('refreshLivewireTable');
        $this->dispatch('lucky');
        $this->open = false;
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        $atributos = AtributosModel::all();
        $tiposCampos = TiposCamposModel::all();
        return view('livewire.Atributo.create-atributos', compact(['atributos', 'tiposCampos']));
    }

    public function agregarValor()
    {
        $this->valores[] = '';
    }

    public function eliminarValor($index)
    {
        unset($this->valores[$index]);
        $this->valores = array_values($this->valores);
    }

    public function guardarValoresPredefinidos($idAtributo)
    {
        foreach ($this->valores as $valor) {
            if (!empty($valor)) {
                AtributosValoresModel::create([
                    'id_atributo' => $idAtributo,
                    'valor' => $valor,
                ]);
            }
        }
    }

    public function TipoId($value)
    {
        if ($value == '1') {
            $this->categorias2 = UnidadModel::all();
        } else {
            $this->categorias2 = null;
        }
    }

    public function close()
    {
        $this->reset(['nombre', 'tipo_campo', 'descripcion', 'unidad_medida']);
        $this->open = false;
    }

    public function crearAtributos()
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
