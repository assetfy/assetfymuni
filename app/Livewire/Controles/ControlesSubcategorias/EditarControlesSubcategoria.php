<?php

namespace App\Livewire\Controles\ControlesSubcategorias;

use App\Models\ControlesSubcategoriaModel;
use App\Services\MiddlewareInvoker;
use App\Models\SubcategoriaModel;
use App\Traits\VerificacionTrait;
use App\Models\CategoriaModel;
use App\Models\ControlesModel;
use Livewire\Attributes\On;
use App\Models\TiposModel;
use Livewire\Component;

class EditarControlesSubcategoria extends Component
{
    use VerificacionTrait;
    public $open = false;
    public $upunico, $upobligatorio_carga_ini, $id_control,
        $id_categoria, $id_subcategoria,
        $upes_periodico, $upfrecuencia_control,
        $upcantidad_estandar, $upreq_foto, $id_tipo;
    public $datos, $categoria, $cat, $subcat;
    public $controlessub;
    public $ctrl, $cont;
    public $control;
    protected $listeners = ['openModal', 'openControlesSubcategoria'];

    public $rules =
    [
        'id_control' => 'required',
        'upunico' => 'required|max:50|min:2',
        'upobligatorio_carga_ini' => 'required',
        'id_tipo' => 'required',
        'id_categoria' => 'required',
        'id_subcategoria' => 'required',
        'upes_periodico' => 'required',
        'upfrecuencia_control' => 'required',
        'upcantidad_estandar' => 'required',
        'upreq_foto' => 'required'
    ];

    public function mount(ControlesSubcategoriaModel $value)
    {
        $this->controlessub = $value;
        $this->id_control = $value->id_control;
        $this->id_tipo = $value->id_tipo;
        $this->id_categoria = $value->id_categoria;
        $this->id_subcategoria = $value->id_subcategoria;
        $this->upunico = $value->unico;
        $this->upobligatorio_carga_ini = $value->obligatorio_carga_ini;
        $this->upes_periodico = $value->es_periodico;
        $this->upfrecuencia_control = $value->frecuencia_control;
        $this->upcantidad_estandar = $value->cantidad_estandar;
        $this->upreq_foto = $value->req_foto;
    }

    public function openControlesSubcategoria($data)
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->openModal($data);
        }
    }

    public function openModal($data)
    {
        $this->control =  ControlesSubcategoriaModel::find($data);
        if ($this->control) {
            $this->mount($this->control);
            $this->actualizar();
            $this->open = true;
        }
    }

    public function guardarCambios()
    {
        $this->dispatch('check');
    }

    #[On('guardado')]
    public function actualizar()
    {
        $this->actualizarControlSub();
    }

    protected function actualizarControlSub()
    {
        $this->validate();

        $this->controlessub->unico = $this->upunico;
        $this->controlessub->obligatorio_carga_ini = $this->upobligatorio_carga_ini;
        $this->controlessub->es_periodico = $this->upes_periodico;
        $this->controlessub->frecuencia_control = $this->upfrecuencia_control;
        $this->controlessub->cantidad_estandar = $this->upcantidad_estandar;
        $this->controlessub->req_foto = $this->upreq_foto;

        $this->controlessub->save();
        $this->dispatch('refreshLivewireTable');
        $this->close();
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        $subcategorias = SubcategoriaModel::all();
        $categorias = CategoriaModel::all();
        $tipos = TiposModel::all();
        $controles = ControlesModel::all();
        return view(
            'livewire.controles.controlessubcategorias.editar-controles-subcategoria',
            ['controlessub' => $this->controlessub],
            compact('subcategorias', 'categorias', 'tipos', 'controles')
        );
    }
}
