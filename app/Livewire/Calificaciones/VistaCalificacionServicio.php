<?php

namespace App\Livewire\Calificaciones;

use App\Models\CalificacionesModel;
use App\Models\EmpresasModel;
use App\Models\ServiciosActivosModel;
use Livewire\Component;

class VistaCalificacionServicio extends Component
{
    public $idServicioActivo, $idProveedor;
    public $calificacion, $general, $precio, $fecha, $diagnostico, $contratacion, $servicios;
    public $updateCalificacion, $updateGeneral, $updatePrecio, $updateDiagnostico, $updateContratacion;
    public $open = false;
    public $editMode = false; // Modo de edición
    protected $listeners = ['openEditarCalificacion'];

    protected $rules = [
        'updateCalificacion' => 'max:5',
        'updatePrecio' => 'max:5',
        'updateDiagnostico' => 'max:5',
        'updateContratacion' => 'required',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function toggleEditMode()
    {
        $this->editMode = !$this->editMode;
    }

    public function openEditarCalificacion($data)
    {
        $servicio = $data;
        if ($servicio) {
            $this->cargarDatos($servicio);
            $this->open = true;
        }
    }

    private function cargarDatos($value)
    {
        $this->editMode = false;
        $this->servicios = $value;
        $this->idServicioActivo = ServiciosActivosModel::where('solicitud', $this->servicios)->first()->id_serviciosActivos;
        $this->idProveedor = ServiciosActivosModel::where('solicitud', $this->servicios)->first()->proveedor;
        $this->calificacion = CalificacionesModel::where('id_serviciosActivos', $this->idServicioActivo)->first();

        $this->updateGeneral = $this->calificacion->general;
        $this->updateCalificacion = $this->calificacion->calificacion;
        $this->updateDiagnostico = (float)$this->calificacion->diagnostico;
        $this->updatePrecio = (float)$this->calificacion->precio;
        $this->updateContratacion = (float)$this->calificacion->contratacion;
        $this->fecha = $this->calificacion->fecha_resenia;
    }

    public function actualizar()
    {
        $this->validate();

        // Verificar si la contratación es sí y si todos los campos requeridos están completos
        if ($this->updateContratacion == 1) {
            if (empty($this->updateCalificacion) || empty($this->updateDiagnostico) || empty($this->updatePrecio)) {
                session()->flash('error', 'Por favor, califique todas las características del servicio.');
                return;
            }
        } else if ($this->updateContratacion == 0) {
            if (empty($this->updateCalificacion) && empty($this->updateDiagnostico) && empty($this->updatePrecio)) {
                session()->flash('error', 'Por favor, califique al menos una característica del servicio.');
                return;
            }
        }

        $this->calificacion->general = $this->updateGeneral;
        $this->calificacion->calificacion = $this->updateCalificacion;
        $this->calificacion->diagnostico = $this->updateDiagnostico;
        $this->calificacion->precio = $this->updatePrecio;
        $this->calificacion->contratacion = $this->updateContratacion;

        $this->calificacion->save();
        $this->dispatch('lucky');
        $this->dispatch('refreshLivewireTable');
        $this->cerrar();
    }
    
    public function cerrar()
    {
        $this->reset(['general', 'calificacion', 'precio', 'diagnostico', 'contratacion']);
        $this->open = false;
    }
    
    public function render()
    {
        $empresas = EmpresasModel::all();

        return view('livewire.calificaciones.vista-calificacion-servicio', 
        [
            'calificaciones' => $this->calificacion,
            'proveedor' => $this->idProveedor,
            'empresas' => $empresas,
        ]);
    }
}
