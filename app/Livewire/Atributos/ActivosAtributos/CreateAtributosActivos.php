<?php

namespace App\Livewire\Atributos\ActivosAtributos;

use Livewire\Component;

use App\Models\ActivosAtributosModel;
use App\Models\AtributosModel;
use App\Models\AtributosSubcategoriaModel;

class CreateAtributosActivos extends Component
{
    public $id_categoria, $id_subcategoria, $id_tipo, $atributos = [], $id_atributo, $id_activo, $id_subcategoria_activo, 
    $id_categoria_activo, $id_tipo_activo, $campo, $campo_numerico,$atribu,$categoria,$tipo,$atributosDisponibles;

    protected $rules = 
    ['id_atributo' => 'required',
     'id_activo' => 'required',
     'id_subcategoria_activo' => 'required',
     'id_categoria_activo' => 'required',
     'id_tipo_activo' => 'required',
     'campo' => 'required',
     'campo_numerico' => 'required'];

     protected $listeners = ['openModal'];

     public function openModal($data)
     {
         $activoId = $data['activoId'];
         $activo = ActivosAtributosModel::find($activoId);
 
     }

    public function value(ActivosAtributosModel $value)
    {
        $this->atribu = $value;
        $this->id_atributo = $value->id_atributo;
        $this->categoria = $value->id_categoria;
        $this->tipo = $value->id_tipo;
        $this->atributosDisponibles = AtributosSubcategoriaModel::where([
            'id_subcategoria' => $this->id_subcategoria,
            'id_categoria' => $this->id_categoria,
            'id_tipo' => $this->id_tipo
        ])->get();
        $this->id_subcategoria_activo = $this->subcategoria;
        $this->id_categoria_activo = $this->categoria;
        $this->id_tipo_activo = $this->tipo;
    }

    public function render()
    {
        $atributos = AtributosModel::all();
        $atributosDisponibles = AtributosSubcategoriaModel::where([
            'id_subcategoria' => $this->id_subcategoria,
            'id_categoria' => $this->id_categoria,
            'id_tipo' => $this->id_tipo
        ])->pluck('id_atributo');

        $this->atributos = AtributosModel::whereIn('id_atributo', $atributosDisponibles)->get();

        return view('livewire.atributos.activosatributos.create-nuevo-atributos-activos', ['atributos' => $this->atributos]);
    }

    public function save()
    {
        $this->validateInput();
         // Crear la relación entre el activo y el atributo en la tabla intermedia
        $this->CrearRegistro();
        // Limpiar los campos después de guardar
        $this->close();
    }

    private function CrearRegistro(){
        ActivosAtributosModel::create([
            'id_atributo' => $this->id_atributo,
            'id_activo' => $this->id_activo,
            'id_subcategoria_activo' => $this->id_subcategoria_activo,
            'id_categoria_activo' => $this->id_categoria_activo,
            'id_tipo_activo' => $this->id_tipo_activo,
            'campo' => $this->campo,
            'campo_numerico' => $this->campo_numerico
        ]);
    }

    public function close(){
        $this->reset(['id_atributo', 'id_activo', 'id_subcategoria_activo', 
        'id_categoria_activo', 'id_tipo_activo', 'campo', 'campo_numerico']);
        $open = false;
    }

    private function validateInput() // Cambia el nombre del método
    {
        $this->validate( 
        ['id_atributo' => 'required',
        'id_activo' => 'required',
        'id_subcategoria_activo' => 'required',
        'id_categoria_activo' => 'required',
        'id_tipo_activo' => 'required',
        'campo' => 'required',
        'campo_numerico' => 'required']);
    }
}