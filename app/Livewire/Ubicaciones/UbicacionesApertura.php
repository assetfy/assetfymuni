<?php

namespace App\Livewire\Ubicaciones;
use App\Models\AperturaModel1;
use App\Models\AperturaModel2;
use App\Models\AperturaModel3;
use App\Models\AperturaModel;
use Livewire\Component;
use App\Models\UbicacionesModel;
use App\Traits\SortableTrait; 

class UbicacionesApertura extends Component
{
    use SortableTrait;
    public $ubicacion;
    public $open = false;
    public $nombre,$id_1,$id_apertura1,$id_apertura2,$id_apertura3,$id_apertura4,$value,$aperturas1,$aperturas2,$aperturas3;

    protected $rules = [
        'nombre' => 'required|max:50',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }
    
    public function save()
{
    if ($this->id_apertura1 === null && $this->id_apertura2 === null && $this->id_apertura3 === null&& $this->id_apertura4 === null) {
        // Si id_apertura1, id_apertura2 y id_apertura3 son nulos, crear en AperturaModel1
        $nombre_apertura = $this->ubicacion->nombre_apertura_1;
        $numero_apertura = AperturaModel1::where('id_ubicacion', $this->ubicacion->id_ubicacion)->count() + 1;
        $this->eventos();
        AperturaModel1::create([
            'nombre' =>  $nombre_apertura . '_' . $numero_apertura, // Concatenar el nombre con el número
            'id_ubicacion' =>$this->ubicacion->id_ubicacion,
            'nombre_apertura_2' => $this->nombre,
        ]);
        $this->eventos();
    } elseif ($this->id_apertura2 !== null && $this->id_apertura3 === null) {
        // Si id_apertura2 no es nulo pero id_apertura3 es nulo, crear en AperturaModel2
        $apertura1 = AperturaModel1::findOrFail($this->id_apertura2);
        $nombre_apertura = $apertura1->nombre_apertura_2;
        $numero_apertura = AperturaModel2::where('id_apertura_1', $apertura1->id_apertura_1)->count() + 1;
        AperturaModel2::create([
            'nombre' => $nombre_apertura . '_' . $numero_apertura,
            'id_ubicacion' =>$this->ubicacion->id_ubicacion,
            'id_apertura_1' => $apertura1->id_apertura_1,
            'nombre_apertura_3' => $this->nombre,
        ]);
        $this->eventos();
    } elseif ($this->id_apertura3 !== null && $this->id_apertura4 === null) {
        // Si id_apertura3 no es nulo pero id_apertura4 es nulo, crear en AperturaModel3
        $apertura2 = AperturaModel2::findOrFail($this->id_apertura3);
        $nombre_apertura = $apertura2->nombre_apertura_3;
        $numero_apertura = AperturaModel2::where('id_apertura_1', $apertura2->id_apertura_2)->count() + 1;
        AperturaModel3::create([
            'nombre' => $nombre_apertura . '_' . $numero_apertura,
            'id_ubicacion' =>$this->ubicacion->id_ubicacion,
            'id_apertura_1' => $apertura2->id_apertura_1,
            'id_apertura_2' => $apertura2->id_apertura_2,
            'nombre_apertura_4' => $this->nombre,
        ]);
        $this->eventos();
    } elseif($this->id_apertura4 !== null) {
        // Si id_apertura4 no es nulo, crear en AperturaModel4
        $apertura3 = AperturaModel3::findOrFail($this->id_apertura4);
        $nombre_apertura = $apertura3->nombre_apertura_4;
        $numero_apertura = AperturaModel3::where('id_apertura_3', $apertura3->id_apertura_3)->count() + 1;
        AperturaModel::create([
            'nombre' => $nombre_apertura . '_' . $numero_apertura,
            'id_ubicacion' =>$this->ubicacion->id_ubicacion,
            'id_apertura_1' => $apertura3->id_apertura_1,
            'id_apertura_2' => $apertura3->id_apertura_2,
            'id_apertura_3' => $apertura3->id_apertura_3,
        ]);
        $this->eventos();
    }
}

    public function mount(UbicacionesModel $value = null)
    {
        if ($value) {
            $this->aperturas($this->ubicacion);
            $this->ubicacion = $value;
        }
    }

   
    public function render()
    {
        return view('livewire.ubicaciones.ubicaciones-apertura');
    }

    public function close(){
        $this->reset(['nombre']);
        $this->open = false;
    }

    public function openModal($data){
        if ($data) {
            $this->ubicacion= UbicacionesModel::find($data);
            $this->aperturas($this->ubicacion);
            $this->open = true;
        }
    }

    private function aperturas($ubicacion){
        $this->aperturas1 = AperturaModel1::where('id_ubicacion',$this->ubicacion->id_ubicacion)->get();
        $this->aperturas2 = AperturaModel2::where('id_ubicacion',$this->ubicacion->id_ubicacion)->get();
        $this->aperturas3 = AperturaModel3::where('id_ubicacion',$this->ubicacion->id_ubicacion)->get();
    }
}
