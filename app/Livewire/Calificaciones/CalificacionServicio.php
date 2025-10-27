<?php

namespace App\Livewire\Calificaciones;

use App\Models\CalificacionesModel;
use App\Models\ServiciosActivosModel;
use App\Models\User;
use App\Helpers\IdHelper;
use Livewire\Component;

class CalificacionServicio extends Component
{
    public $open = false;
    public $calificacion, $general, $precio, $diagnostico, $contratacion, $servicios;
    public $id, $empresa_titular, $usuario_titular, $id_user, $idServicioActivo;
    protected $listeners = ['openDetalleCalificacion'];

    protected $rules = [
        'calificacion' => 'max:5',
        'precio' => 'max:5',
        'diagnostico' => 'max:5',
        'contratacion' => 'required',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function openDetalleCalificacion($data)
    {
        $servicio = $data;
        if ($servicio) {
            $this->cargarDatos($servicio);
            $this->open = true;
        }
    }

    private function cargarDatos($servicio)
    {
        $this->servicios = $servicio;
        $this->idServicioActivo = ServiciosActivosModel::where('solicitud', $this->servicios)->first()->id_serviciosActivos;
        $id = IdHelper::identificadorParcial();
        $this->empresa_titular = $id['cuit'];
        $this->usuario_titular = $id['user'];
    }

    public function save()
    {
        $this->validate();

        // Verificar si la contratación es sí y si todos los campos requeridos están completos
        if ($this->contratacion == 1) {
            if (empty($this->calificacion) || empty($this->diagnostico) || empty($this->precio)) {
                session()->flash('error', 'Por favor, califique todas las características del servicio.');
                return;
            }
        } else if ($this->contratacion == 0) {
            if (empty($this->calificacion) && empty($this->diagnostico) && empty($this->precio)) {
                session()->flash('error', 'Por favor, califique al menos una característica del servicio.');
                return;
            }
        }

        CalificacionesModel::create([
            'cuit' => $this->empresa_titular,
            'id_usuario' => Auth()->user()->id,
            'calificacion' => $this->calificacion,
            'general' => $this->general,
            'id_serviciosActivos' => $this->idServicioActivo,
            'diagnostico' => $this->diagnostico,
            'precio' => $this->precio,
            'contratacion' => $this->contratacion,
        ]);
        $this->cerrar();
        $this->dispatch('refreshLivewireTable');
        $this->dispatch('lucky');
        $this->open = false;
    }

    public function cerrar()
    {
        $this->reset(['general', 'calificacion', 'precio', 'diagnostico', 'contratacion']);
        $this->open = false;
    }

    public function clearError()
    {
        session()->forget('error');
    }

    public function render()
    {
        return view('livewire.calificaciones.calificacion-servicio');
    }
}
