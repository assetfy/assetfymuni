<?php

namespace App\Livewire\Subcategoria\AtributosSubcategorias;

use App\Models\AtributosModel;
use App\Models\AtributosSubcategoriaModel;
use App\Models\SubcategoriaModel;
use App\Models\CategoriaModel;
use App\Models\TiposModel;
use App\Traits\VerificacionTrait;
use Livewire\Attributes\On;
use Livewire\Component;
use App\Services\MiddlewareInvoker;

class EditAtributosSubcategoria extends Component
{
    use VerificacionTrait;
    public $open = false;
    public $updateObligatorio, $updateUnico, $subcategorias, $categoria, $tipos, $atributoNombre;
    public $atributoSubcategoria;
    public $atributoSubcategorias;
    protected $listeners = ['atributoSubcategoria'];

    protected $rules =
    [
        'updateObligatorio' => 'required',
        'updateUnico' => 'required'
    ];

    public function atributoSubcategoria($data)
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
        $this->atributoSubcategoria = AtributosSubcategoriaModel::find($data);
        if ($this->atributoSubcategoria) {
            $this->atributoNombre = AtributosModel::find($this->atributoSubcategoria->id_atributo);
            // Inicializa las propiedades necesarias
            $this->updateObligatorio = $this->atributoSubcategoria->obligatorio_carga_ini;
            $this->updateUnico = $this->atributoSubcategoria->unico;
            $this->subcategorias = SubcategoriaModel::find($this->atributoSubcategoria->id_subcategoria);
            $this->categoria = CategoriaModel::find($this->atributoSubcategoria->id_categoria);
            $this->tipos = TiposModel::find($this->atributoSubcategoria->id_tipo);
            $this->open = true;
        }
    }


    public function guardarCambios()
    {
        $this->dispatch('check');
    }

    #[On('guardado')]
    public function actualizarAtributoSubcategoria()
    {
        $this->actualizar();
    }

    protected function actualizar()
    {
        $this->validate();

        $this->atributoSubcategoria->unico = $this->updateUnico;
        $this->atributoSubcategoria->obligatorio_carga_ini = $this->updateObligatorio;

        $this->atributoSubcategoria->save();
        $this->dispatch('refreshLivewireTable');
        $this->close();
    }

    public function render()
    {
        return view('livewire.subcategoria.edit-atributos-subcategoria', [
            'atributoSubcategoria' => $this->atributoSubcategoria,
            'tipos' => $this->tipos,
            'categoria' => $this->categoria,
            'subcategorias' => $this->subcategorias,
        ]);
    }

    protected function close()
    {
        $this->open = false;
        $this->dispatch('render');
    }
}
