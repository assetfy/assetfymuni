<?php

namespace App\Livewire\Controles;

use Livewire\Component;
use Livewire\WithFileUploads; // Asegúrate de tener este trait
use App\Models\ActivosControlesModel;
use App\Models\ControlesSubcategoriaModel;
class Foto extends Component
{
    use WithFileUploads; // Agrega el trait a la clase

    public $open = false;
    public $id_controles;

    public $listeners = ['cerrar' => 'close', 'render' => 'render'];

    public function mount(ActivosControlesModel $controles){
        $this->id_controles = $controles;
    }

    public function render()
    {   
        $activosControles = ActivosControlesModel::all();
        return view('livewire.controles.mostrarfoto', ['controles' => $this->id_controles ], compact(['activosControles']));
    }

    public function close(){
        $this->reset(['imagen']);
        $this->open = false;
    }
}