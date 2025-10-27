<?php

namespace App\Livewire\Empresas;

use App\Models\UsuarioActividadesRepresentadasModel;
use App\Models\ActividadesEconomicasModel;
use App\Models\EmpresasActividadesModel;
use App\Models\User;
use App\Models\UsuariosEmpresasModel;
use App\Services\MiddlewareInvoker;
use Livewire\Component;

class EmpresasActividadRepresentante extends Component
{
    public $id_usuario, $actividades, $usuario, $actividadesCargadas, $user, $datos;
    public $selectedActividades = [];
    public $open = false;

    protected $listeners = ['openModalEditarRepresentante'];

    protected $rules = [
        'selectedActividades' => 'required|array|min:1'
    ];

    public function openModalEditarRepresentante($data)
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->openModal($data);
        }
    }

    public function openModal($data)
    {
        $this->user = UsuariosEmpresasModel::find($data);
        $this->datos = User::find($this->user->id_usuario);
        $this->cargarDatosEmpresa();
        $this->cargarActividades();
        $this->open = true;
    }

    private function cargarDatosEmpresa()
    {
        $this->usuario = UsuariosEmpresasModel::where('id_usuario',  $this->user->id_usuario)
            ->select('cuit', 'id_relacion')
            ->first();
        if ($this->usuario) {
            $this->actividadesCargadas = UsuarioActividadesRepresentadasModel::where('id_relacion_usuario', $this->usuario->id_relacion)->get();
            $this->selectedActividades = $this->actividadesCargadas->pluck('cod_actividad')->toArray();
        } else {
            $this->actividadesCargadas = collect();
        }
    }

    private function cargarActividades()
    {
        if ($this->usuario) {
            $this->actividades = EmpresasActividadesModel::where('cuit', $this->usuario->cuit)
                ->pluck('cod_actividad')
                ->map(function ($cod_actividad) {
                    return ActividadesEconomicasModel::where('cod_actividad', $cod_actividad)->first();
                });
        } else {
            $this->actividades = collect();
        }
    }

    public function save()
    {
        $this->validate();

        // Validar que todas las actividades seleccionadas existen en la tabla ActividadesEconomicasModel
        $validActividades = ActividadesEconomicasModel::whereIn('cod_actividad', $this->selectedActividades)->pluck('cod_actividad')->toArray();
        $invalidActividades = array_diff($this->selectedActividades, $validActividades);

        if (count($invalidActividades) > 0) {
            session()->flash('error', 'Algunas actividades seleccionadas no existen en el sistema.');
            return;
        }

        $this->crearRegistro();
        $this->close();
    }

    private function crearRegistro()
    {
        foreach ($this->selectedActividades as $cod_actividad) {
            UsuarioActividadesRepresentadasModel::updateOrCreate(
                [
                    'id_usuario' => $this->id_usuario,
                    'cod_actividad' => $cod_actividad,
                    'cuit_usuario' => $this->usuario->cuit,
                    'id_relacion_usuario' => $this->usuario->id_relacion,
                ],
                [
                    'id_usuario' => $this->id_usuario,
                    'cod_actividad' => $cod_actividad,
                    'cuit_usuario' => $this->usuario->cuit,
                    'id_relacion_usuario' => $this->usuario->id_relacion,
                ]
            );
        }
    }

    public function close()
    {
        $this->reset(['selectedActividades']);
        $this->open = false;
    }

    public function eliminarRegistro($codigos)
    {
        $codigos = is_array($codigos) ? $codigos : [$codigos];
        foreach ($codigos as $codigo) {
            UsuarioActividadesRepresentadasModel::where('cod_actividad', $codigo)->delete();
        }
        $this->cargarDatosEmpresa();
    }

    public function toggleActividad($actividadId)
    {
        if (in_array($actividadId, $this->selectedActividades)) {
            $this->selectedActividades = array_diff($this->selectedActividades, [$actividadId]);
        } else {
            $this->selectedActividades[] = $actividadId;
        }
    }

    public function render()
    {
        return view('livewire.empresas.empresas-actividad-representante');
    }
}
