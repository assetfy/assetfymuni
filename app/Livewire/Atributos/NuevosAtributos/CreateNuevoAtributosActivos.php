<?php

namespace App\Livewire\Atributos\NuevosAtributos;

use App\Models\AtributosSubcategoriaModel;
use Illuminate\Support\Facades\Session;
use App\Models\ActivosAtributosModel;
use App\Models\SubcategoriaModel;
use App\Models\TiposCamposModel;
use App\Models\CategoriaModel;
use App\Models\AtributosModel;
use App\Models\ActivosModel;
use App\Models\TiposModel;
use Livewire\Component;


class CreateNuevoAtributosActivos extends Component
{
    public $open = false;
    public $selectedAtributos = [];
    public $id_categoria, $id_subcategoria, $id_tipo, $id_atributo, $obligatorio_carga_ini;
    public $atributos;
    public $atributosFaltantes = [];
    public $id_activo, $id_subcategoria_activo, $id_categoria_activo, $id_tipo_activo;
    public $campo = [];
    public $campo_numerico = [];

    protected $listeners = ['render' => 'render','openModal'];
    protected $rules = 
    ['id_activo' => 'required',
     'id_categoria' => 'required',
     'id_tipo' => 'required',
     'id_subcategoria' => 'required'];


     public function openModal($data)
     {
        $activoId = $data['activoId']['id_activo'];
        $activo =ActivosModel::find($activoId);
        $this->asignarAtributo($activo);
        $this->open = true;
     }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount(ActivosModel $activo)
    {
        $this->asignarAtributo($activo);
    }
    
    private function asignarAtributo($activo){
        $this->id_activo = $activo->id_activo;
        $this->id_tipo = $activo->id_tipo;
        $this->id_categoria = $activo->id_categoria;
        $this->id_subcategoria = $activo->id_subcategoria;
        
        $atributosActivos = ActivosAtributosModel::where('id_activo', $activo->id_activo)->pluck('id_atributo');
        
        if($atributosActivos->isEmpty()){
            $this->cargaAtributosSinActivos($activo);
        } else {
            $this->cargaAtributosConActivos($atributosActivos, $activo);
        }
    }
    
    private function cargaAtributosSinActivos($activo){
        $this->cargarAtributosFaltantes(
            AtributosSubcategoriaModel::where([
                'id_tipo' => $activo->id_tipo,
                'id_categoria' => $activo->id_categoria,
                'id_subcategoria' => $activo->id_subcategoria,
                'obligatorio_carga_ini' => 'No'
            ])->pluck('id_atributo')->toArray()
        );
    }
    
    private function cargaAtributosConActivos($atributosActivos, $activo){
        $atributosExistentes = AtributosModel::whereIn('id_atributo', $atributosActivos)->pluck('id_atributo');
            
        $this->cargarAtributosFaltantes(
            AtributosSubcategoriaModel::where([
                'id_tipo' => $activo->id_tipo,
                'id_categoria' => $activo->id_categoria,
                'id_subcategoria' => $activo->id_subcategoria,
                'obligatorio_carga_ini' => 'No'
            ])->whereNotIn('id_atributo', $atributosExistentes)->pluck('id_atributo')->toArray()
        );
    }
    
    private function cargarAtributosFaltantes($atributos){
        $this->atributos = $atributos;
        $this->atributosFaltantes = AtributosModel::whereIn('id_atributo', $atributos)->get();
    }
    
    public function render()
    {
        $tipos = TiposModel::all();
        $categorias = CategoriaModel::all();
        $subcategorias = SubcategoriaModel::all();
        $atributos = AtributosModel::all();
        $atributosSubcategorias = AtributosSubcategoriaModel::all();
        $activos = ActivosModel::all();
        $activosAtributos = ActivosAtributosModel::all();
        $campos = TiposCamposModel::all();
        
        return view('livewire.atributo.activosatributos.create-nuevo-atributos-activos', compact(['activos', 'atributos', 'atributosSubcategorias', 'activosAtributos', 'tipos', 'categorias', 'subcategorias', 'campos']));
    }

    public function save()
    {
        $this->validate();

        foreach($this->selectedAtributos as $idAtributo => $seleccionado) {
            if ($seleccionado) {
                ActivosAtributosModel::create([
                    'id_atributo' => $idAtributo,
                    'id_activo' => $this->id_activo,
                    'id_subcategoria_activo' => $this->id_subcategoria,
                    'id_categoria_activo' => $this->id_categoria,
                    'id_tipo_activo' => $this->id_tipo,
                    'campo' => isset($this->campo[$idAtributo]) ? $this->campo[$idAtributo] : null,
                    'campo_numerico' => isset($this->campo_numerico[$idAtributo]) ? $this->campo_numerico[$idAtributo] : null
                ]);
            }
        }
        $this->dispatch('lucky', 'Los atributos se han creado correctamente');
        $this->close();
    }

    public function close()
    {
        $this->dispatch('render');
        $this->reset(['id_atributo', 'id_activo', 'id_subcategoria_activo', 'id_categoria_activo', 'id_tipo_activo', 'campo', 'campo_numerico','atributosFaltantes']);
        $this->selectedAtributos = [];
        $this->campo = [];
        $this->campo_numerico = [];
        $this->open = false;
        $this->recargarPagina();
    }

    private function recargarPagina(){
        $tipos = Session::get('tipo');
        $this->id_tipo = $tipos;
    }
}