<?php

namespace App\Livewire\Controles;

use Livewire\Component;
use App\Models\ControlesModel;
use App\Models\ActivosModel;
use App\Models\ActivosControlesModel;
use Illuminate\Support\Facades\Session;
use App\Models\ControlesSubcategoriaModel;

class ControlesVistaDetalle extends Component
{
    public $id_activo, $id_subcategoria, $id_categoria, $id_tipo, $controlesObligatorios, $activosControles, $controlesFaltantes = [], $controles, 
           $controlesModelo, $activos, $controlesSubcategoria, $previousUrl;

    public function setIdActivo($id_activo)
    {
        // Obtener los controles asociados al activo
        Session::put('id_activo',  $id_activo); 
        $activosControles = ActivosControlesModel::where('id_activo', $id_activo)->get();

        // Inicializar la variable $controlesFaltantes como array vacío
        $this->controlesFaltantes = [];

        // Obtener información del activo
        $activo = ActivosModel::where('id_activo', $id_activo)->first();

        // Verificar si no hay controles asociados al activo
        if (count($activosControles) == 0) {
            // Obtener controles obligatorios basados en subcategoría, categoría, tipo o marcados como obligatorios al inicio
            $controlesObligatorios = ControlesSubcategoriaModel::where('id_subcategoria', $activo->id_subcategoria)
                ->where('id_categoria', $activo->id_categoria)
                ->where('id_tipo',  $activo->id_tipo)
                ->Where('obligatorio_carga_ini', 'Si')
                ->pluck('id_control');

            // Obtener controles faltantes
            $this->controlesFaltantes = ControlesModel::whereIn('id_control', $controlesObligatorios)->get();
        } else {
            // Obtener controles obligatorios basados en los controles asociados al activo
            $controlesObligatorios = ActivosControlesModel::where('id_activo',  $activo->id_activo)->pluck('id_control');

            // Verificar si no hay controles obligatorios
            if ($controlesObligatorios->count() === 0) {
                $this->controlesFaltantes = [];
            } else {
                // Obtener controles faltantes basados en subcategoría y marcados como obligatorios al inicio
                $this->controlesFaltantes = ControlesSubcategoriaModel::whereNotIn('id_control', $controlesObligatorios)
                    ->where('obligatorio_carga_ini', 'Si')
                    ->pluck('id_control');

                // Obtener detalles de los controles faltantes
                $this->controlesFaltantes = ControlesModel::whereIn('id_control', $this->controlesFaltantes)->get();
            }
        }
        // Procesar y organizar los controles
        $controles = $activosControles->unique('id_control')->sortByDesc('created_at')->values();
        $controlIDs = $controles->pluck('id_control');
        Session::put('controlId',  $controlIDs ); 
        // Obtener detalles de los controles
        $controlesModelo = ControlesModel::whereIn('id_control', $controles->pluck('id_control'))->get();

        // Obtener todos los activos, controles subcategoría y controles
        $activos = ActivosModel::all();
        $controlesSubcategoria = ControlesSubcategoriaModel::all();

        $this->previousUrl = session('previous_url', url()->previous());

        return view('livewire.controles.controles-vista-detalle', [
            'controlesFaltantes' => $this->controlesFaltantes,
            'controlesModelo' => $controlesModelo,
            'controles' => $controles,
            'activos' => $activos,
            'controlesSubcategoria' => $controlesSubcategoria,
            'previousUrl' => $this->previousUrl,
        ]);
    }
} 
