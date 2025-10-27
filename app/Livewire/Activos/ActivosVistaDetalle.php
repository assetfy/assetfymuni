<?php

namespace App\Livewire\Activos;

use Livewire\Component;
use App\Models\ActivosModel;
class ActivosVistaDetalle extends Component
{
    public $previousUrl,$etiqueta ;
    
    public function render($etiqueta){
        $this->previousUrl = session('previous_url', url()->previous());
        $this->etiqueta = $etiqueta;
        // Buscar el activo usando $this->etiqueta
        $activo = ActivosModel::with([
            'tipo',
            'categoria',
            'subcategoria',
            'ubicacion',
            'estadoAlta',
            'estadoGeneral',
        ])->where('etiqueta', $this->etiqueta)->first();

        return view('livewire.activos.activos-vista-detalle', [
            'activo' => $activo,
            'previousUrl' => $this->previousUrl,
        ]);    
    }
}