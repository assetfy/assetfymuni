<?php

namespace App\Livewire\Atributos;

use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use App\Models\TiposCamposModel;
use App\Models\AtributosModel;
use App\Models\UnidadModel;
use Livewire\Component;
use Livewire\Attributes\On;

class EditarAtributos extends Component
{
    use VerificacionTrait;
    public $open = false;
    public $atributo;
    public $updateTipo;
    public $updatedMedida;
    public $updatedNombre;
    public $updatedDescripcion;
    public $unidad2, $prueba;
    public $atributos;
    protected $listeners = ['openEditAtributos'];

    protected $rules = [
        'updateTipo' => 'required',
        'updatedNombre' => 'required|max:30',
        'updatedDescripcion' => 'required|max:30',
    ];

    public function openEditAtributos($data)
    {
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->datos($data);
            $this->open = true;
        }
    }

    private function datos($data)
    {
        $this->atributo = AtributosModel::find($data);
        $this->updateTipo = $this->atributo->tipo_campo;
        $this->updatedNombre = $this->atributo->nombre;
        $this->updatedDescripcion = $this->atributo->descripcion;
        $this->updatedMedida = $this->atributo->unidad_medidas;
        if ($this->updateTipo == '1') {
            $this->unidad2 = UnidadModel::all();
        } else {
            $this->unidad2 = null;
        }
    }

    #[On('guardado')]
    public function actualizar()
    {
        $this->actualizarAtributo();
    }

    protected function actualizarAtributo()
    {
        $campos = ['nombre', 'descripcion'];

        $valoresActualizados = [
            'tipo_campo' => $this->updateTipo,
            'unidad_medidas' => $this->updatedMedida,
            'nombre' => $this->updatedNombre,
            'descripcion' => $this->updatedDescripcion
        ];

        $this->verificar($this->atributo, $campos, $valoresActualizados);

        $this->dispatch('refreshLivewireTable');
    }

    public function render()
    {
        $tipos = TiposCamposModel::all();
        $atributos = AtributosModel::all();
        $unidades = UnidadModel::all();
        return view('livewire.Atributo.editar-atributos', compact(['unidades', 'tipos']));
    }

    public function close()
    {
        $this->open = false;
    }

    public function TipoId($value)
    {
        if ($value == '1') {
            $this->unidad2 = UnidadModel::all();
        } else {
            $this->unidad2 = null;
        }
    }
}
