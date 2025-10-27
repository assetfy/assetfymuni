<?php

namespace App\Livewire\Permisosroles;

use App\Helpers\IdHelper;
use App\Models\PermisoPorEmpresaModel;
use App\Models\PermisosModel;
use App\Models\RutasModel;
use App\Models\TiposEmpresaModel;
use App\Models\EmpresasModel;
use App\Services\MiddlewareInvoker;
use Livewire\Component;

class CrearPermisoTipoEmpresa extends Component
{
    public $permisos, $tipo_empresa, $rutas, $cuit_empresa, $tipoEmpresa, $configuracion;
    public $selectedPermiso = null;
    public $selectedRutas = [];
    public $configuracionPorRuta = [];
    public $open = false;

    protected $listeners = ['permisosRolescargarRutas'];

    public function permisosRolescargarRutas()
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        $this->open = true;
    }

    public function mount()
    {
        $this->cuit_empresa = IdHelper::idEmpresa();
        $this->tipoEmpresa = EmpresasModel::where('cuit', $this->cuit_empresa)->pluck('tipo')->first();
        $this->permisos = PermisosModel::where('cuit_empresa', $this->cuit_empresa)->get();
        $this->tipo_empresa = TiposEmpresaModel::all();
        $this->rutas = collect(); // Inicializamos rutas como colección vacía
    }

    public function cargarRutas($value)
    {
        $this->selectedPermiso = $value;

        if ($value) {
            $rutasAsignadas = PermisoPorEmpresaModel::where('id_permiso', $value)
                ->where('tipo_empresa',  $this->tipoEmpresa)
                ->where('cuit_empresa', $this->cuit_empresa)
                ->pluck('id_ruta')
                ->toArray();

            $this->rutas = RutasModel::whereNotIn('id_ruta', $rutasAsignadas)->get();

            // Opcional: Inicializar el valor "No" para cada ruta que se cargue
            foreach ($this->rutas as $ruta) {
                $this->configuracionPorRuta[$ruta->id_ruta] = 'No';
            }
        } else {
            $this->rutas = collect();
        }
    }

    public function save()
    {
        if ($this->selectedPermiso && !empty($this->selectedRutas)) {
            foreach ($this->selectedRutas as $rutaId) {
                // Para cada ruta seleccionada, usamos la configuración elegida;
                // Si no se ha seleccionado ninguna configuración, se asume "No".
                $config = isset($this->configuracionPorRuta[$rutaId]) ? $this->configuracionPorRuta[$rutaId] : 'No';
                PermisoPorEmpresaModel::create([
                    'id_permiso'      => $this->selectedPermiso,
                    'tipo_empresa'    => $this->tipoEmpresa,
                    'id_ruta'         => $rutaId,
                    'cuit_empresa'    => $this->cuit_empresa,
                    'con_configuracion' => $config,
                ]);
            }
            $this->dispatch('lucky');
            $this->rutas = collect();
            $this->dispatch('refreshLivewireTable');
            $this->close();
        }
    }

    public function close()
    {
        $this->reset(['selectedPermiso', 'selectedRutas', 'configuracionPorRuta']);
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.permisos-roles.crear-permiso-tipo-empresa');
    }
}
