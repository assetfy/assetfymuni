<?php

namespace App\Livewire\Atributos\NuevosAtributos;

use App\Models\ActivosAtributosModel;
use App\Models\AtributosSubcategoriaModel;
use App\Models\AtributosModel;
use Livewire\Component;

class EditarNuevosAtributos extends Component
{
    public $open = false;
    public $update_campo, $update_numerico;
    public $resultado, $atributo;
    public $id_atributo, $id_activo, $id_tipo_activo, $id_categoria_activo, $id_subcategoria_activo, $atributoActivo;

    protected $listeners = ['guardado' => 'actualizarNuevoAtributo', 'cerrar' => 'cerrarModal'];

    protected $rules = [
        'update_campo' => 'required',
        'update_numerico' => 'required',
    ];

    public function mount(ActivosAtributosModel $atributo)
    {
        $this->atributo = $atributo;
        $this->id_activo = $atributo->id_activo;
        $this->id_atributo = $atributo->id_atributo;
        $this->id_tipo_activo = $atributo->id_tipo_activo;
        $this->id_categoria_activo = $atributo->id_categoria_activo;
        $this->id_subcategoria_activo = $atributo->id_subcategoria_activo;
        $this->update_campo = $atributo->campo;
        $this->update_numerico = $atributo->campo_numerico;

        $this->atributoActivo = ActivosAtributosModel::where('id_activo',  $atributo->id_activo)
            ->where('id_atributo', $atributo->id_atributo)
            ->where('etiqueta', $atributo->etiqueta)
            ->value('id_atributo');

        if ($this->atributoActivo !== null) {
            $this->resultado = AtributosSubcategoriaModel::where('id_tipo', $atributo->id_tipo_activo)
                ->where('id_categoria', $atributo->id_categoria_activo)
                ->where('id_subcategoria', $atributo->id_subcategoria_activo)
                ->where('unico', 'Si')
                ->where('id_atributo', $this->atributoActivo)
                ->value('id_atributo');

            if ($this->resultado !== null) {
                $this->resultado = ActivosAtributosModel::where('id_atributo', $this->resultado)
                    ->where('id_activo', $atributo->id_activo)
                    ->where('etiqueta', $atributo->etiqueta)
                    ->get();
            }
        }
    }

    public function actualizarNuevoAtributo()
    {
        $this->validate();

        // Obtener el atributo específico que estamos editando
        $atributoEspecifico = ActivosAtributosModel::where('id_activo', $this->atributo->id_activo)
            ->where('id_atributo', $this->atributo->id_atributo)
            ->where('etiqueta', $this->atributo->etiqueta)
            ->first();
        // Verificar si se encontró el atributo específico
        if ($atributoEspecifico) {
            // Actualizar el atributo específico
            $atributoEspecifico->campo = $this->update_campo;
            $atributoEspecifico->campo_numerico = $this->update_numerico;
            $atributoEspecifico->save();
            // Disparar un evento o realizar cualquier otra acción necesaria después de la actualización

            // Volver a cargar la vista
            $this->open = false;
            $this->dispatch('render');
        } else {
            // Manejar el caso en que no se encontró el atributo específico
        }
    }

    public function render()
    {
        $activos = ActivosAtributosModel::all();
        $subcategorias = AtributosSubcategoriaModel::all();
        $atributos = AtributosModel::all();

        return view('livewire.atributo.editar-nuevos-atributos', ['resultado' => $this->resultado], compact(['activos', 'subcategorias', 'atributos']));
    }

    public function cerrarModal()
    {
        $this->open = false;
    }
}
