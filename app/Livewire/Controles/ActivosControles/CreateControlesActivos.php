<?php

namespace App\Livewire\Controles\ActivosControles;

use Livewire\Component;
use App\Models\TiposModel;
use App\Models\ActivosModel;
use Livewire\WithFileUploads;
use App\Models\ControlesModel;
use App\Models\CategoriaModel;
use App\Models\SubcategoriaModel;
use App\Models\ActivosControlesModel;
use App\Models\ControlesSubcategoriaModel;
use Illuminate\Validation\ValidationException;

class CreateControlesActivos extends Component
{
    use WithFileUploads; // Agrega el trait a la clase
    public $open = false;
    public $id_control, $id_activo, $id_tipo_activo, $id_categoria_activo, $id_subcategoria_activo, $fecha_inicio, $fecha_final,
     $categoria, $tipo, $cat, $subcat, $tip, $activo, $subcategoria, $control, $obligatorio_carga_ini, $opciones, $op, $carga_inicial, 
     $opcion, $imagen, $controlesFaltantes;

    protected $listeners = ['openModal'];

    protected $rules = [
        'id_control' => 'required',
        'id_activo' => 'required',
        'id_tipo_activo' => 'required',
        'id_categoria_activo' => 'required',
        'id_subcategoria_activo' => 'required',
        'fecha_inicio' => 'required|date|before_or_equal:today',
        'fecha_final' => 'required|date|after_or_equal:fecha_inicio',
        'imagen.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg'
    ];
    

    public function openModal($data)
    {
        $activoId = $data['activoId'];
        $activo = ActivosModel::find($activoId);
        $this->open = true;
    }   

    public function save()
    {
        $this->validate();
        // Obtener la fecha actual
        $this->validarFechas();
        $imagePaths = $this->storeImages();
        $this->createActivosControles($imagePaths);
        $this->dispatch('lucky');
        $this->dispatch('render');
        $this->close();
    }

    private function validarFechas()
    {
        $fechaActual = now()->format('Y-m-d');
        if ($this->fecha_final < $this->fecha_inicio || $this->fecha_final > $fechaActual) {
            throw ValidationException::withMessages([
                'fecha_final' => 'La fecha final debe ser mayor o igual que la fecha de inicio y no puede ser mayor que la fecha actual.',
            ]);
        }

        if ($this->fecha_inicio > $fechaActual) {
            throw ValidationException::withMessages([
                'fecha_inicio' => 'La fecha de inicio no puede ser mayor que la fecha actual.',
            ]);
        }
    }

    private function storeImages()
    {
        $imagePaths = [];
        if ($this->imagen) {
            foreach ($this->imagen as $index => $image) {
                $imagePaths["foto" . ($index + 1)] = $image->store("public/images");
            }
        }
        return $imagePaths;
    }

    private function createActivosControles($imagePaths)
    {
        ActivosControlesModel::create([
            'id_control' => $this->id_control,
            'id_activo' => $this->id_activo,
            'id_tipo_activo' => $this->id_tipo_activo,
            'id_categoria_activo' => $this->id_categoria_activo,
            'id_subcategoria_activo' => $this->id_subcategoria_activo,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_final
        ] + $imagePaths);
    }

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount(ActivosModel $activo)
    {
        $this->value($activo);
    }

    public function value(ActivosModel $activo)
    {
        // Asigna los datos del activo
        $this->assignarDatosActivo($activo);
        // Carga los controles faltantes
        $this->CargarControlesFaltantes();
    }

    private function assignarDatosActivo(ActivosModel $activo)
    {
        $this->id_activo = $activo->id_activo;
        $this->id_subcategoria_activo = $activo->id_subcategoria;
        $this->id_categoria_activo = $activo->id_categoria;
        $this->id_tipo_activo = $activo->id_tipo;
    }

    private function CargarControlesFaltantes()
    {
         // Obtiene los controles asociados al activo
        $controlesActivo = ActivosControlesModel::where('id_activo', $this->id_activo)->get();
        // Si no hay controles asociados al activo
        if ($controlesActivo->isEmpty()){
            // Carga los controles faltantes desde la subcategoría
            $this->ControlesFaltanteSubcategoria();
        } else {
            // Carga los controles faltantes desde los controles asociados al activo
            $this->ControlesFaltantesActivosControles($controlesActivo);
        }
    }

    private function ControlesFaltanteSubcategoria()
    {
        // Obtiene los controles disponibles desde la subcategoría
        $controlesDisponibles = ControlesSubcategoriaModel::where('id_subcategoria', $this->id_subcategoria_activo)
            ->where('id_categoria', $this->id_categoria_activo)
            ->where('id_tipo', $this->id_tipo_activo)
            ->where('obligatorio_carga_ini', 'Si')
            ->pluck('id_control');
        // Si hay controles disponibles
        if ($controlesDisponibles->isNotEmpty()) {
            // Obtiene los controles faltantes
            $this->controlesFaltantes = ControlesModel::whereIn('id_control', $controlesDisponibles)->get();
        } else {
            // No hay controles disponibles
            $this->controlesFaltantes = collect(); // No hay controles faltantes
        }
    }

    private function ControlesFaltantesActivosControles($controlesActivo)
    {
        // Obtiene los IDs de los controles asociados al activo
        $controlesDisponibles = $controlesActivo->pluck('id_control');
        // Obtiene los controles faltantes desde la subcategoría
        $this->controlesFaltantes = ControlesSubcategoriaModel::whereNotIn('id_control', $controlesDisponibles)
            ->where('obligatorio_carga_ini', 'Si')
            ->pluck('id_control');
        // Obtiene los detalles de los controles faltantes desde el modelo de controles
        $this->controlesFaltantes = ControlesModel::whereIn('id_control', $this->controlesFaltantes)->get();
        // Si no hay controles faltantes
        if ($this->controlesFaltantes->isEmpty()) {
            // Carga los controles faltantes desde la subcategoría
            $controlesDisponibles = ControlesSubcategoriaModel::where('id_subcategoria', $this->id_subcategoria_activo)
                ->where('id_categoria', $this->id_categoria_activo)
                ->where('id_tipo', $this->id_tipo_activo)
                ->orWhere('obligatorio_carga_ini', 'Si')
                ->pluck('id_control');
            // Obtiene los detalles de los controles faltantes desde el modelo de controles
            $this->controlesFaltantes = ControlesModel::whereIn('id_control', $controlesDisponibles)->get();
        }
    }

    public function filtro(ControlesSubcategoriaModel $value)
    {
        // Asigna la opción de acuerdo al valor de req_foto del modelo ControlesSubcategoriaModel
        $this->opciones = $value->req_foto;
        // Establece la opción como verdadera si req_foto es 'Si', de lo contrario, la establece como falsa
        $this->opcion = $this->opciones === 'Si';
    }

    public function render()
    {
        $tipos = TiposModel::all();
        $subcategoria = SubcategoriaModel::all();
        $categoria = CategoriaModel::all();
        $categoriasConTipos = $subcategoria->pluck('id_tipo');
        $categoriasConSubcategoria = $subcategoria->pluck('id_categoria');
        $controles = ControlesModel::all();
        $activos = ActivosModel::all();
        return view('livewire.controles.activoscontroles.create-controles-activos', compact(['activos','controles', 'subcategoria', 'tipos', 'categoria','categoriasConTipos','categoriasConSubcategoria']));
    }

    public function close(){
        $this->reset(['id_control','id_activo', 'id_tipo_activo', 'id_categoria_activo', 'id_subcategoria_activo', 'fecha_inicio', 'fecha_final', 'imagen']);
        $this->open = false;
     }
}