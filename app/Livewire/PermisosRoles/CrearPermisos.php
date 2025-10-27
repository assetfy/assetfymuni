<?php

namespace App\Livewire\PermisosRoles;

use App\Helpers\IdHelper;
use App\Models\TiposEmpresaModel;
use Illuminate\Support\Facades\Auth;
use App\Services\MiddlewareInvoker;
use App\Traits\VerificacionTrait;
use App\Models\PermisosModel;
use App\Models\EmpresasModel;
use Livewire\Component;

class CrearPermisos extends Component
{
    use VerificacionTrait;
    protected $listeners = ['CrearPermisos'];

    public $nombre;
    public $tipo_permisos;
    public $cuit_empresa;
    public $open = false;

    protected $rules = [
        'nombre' => 'required|max:50',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function render()
    {
        return view('livewire.permisos-roles.crear-permisos');
    }

    public function save()
    {
        $this->validate();

        $this->tipo_permisos = $this->getTipoPermisos();

        $valoresNuevos = [
            'nombre' => $this->nombre,
            'cuit_empresa' => $this->cuit_empresa,
        ];

        $this->create(PermisosModel::class, ['nombre'], $valoresNuevos);
        $this->dispatch('refreshLivewireTable');
        $this->close();
    }

    public function close()
    {
        $this->reset(['nombre', 'tipo_permisos']);
        $this->open = false;
    }

    private function getTipoPermisos()
    {
        $user = Auth::user();
        if ($user->panel_actual == 'Empresa') {
            return $this->getEmpresaTipo($user->entidad);
        }
        return null;
    }

    private function getEmpresaTipo($cuit)
    {
        $empresaTipo = EmpresasModel::where('cuit', $cuit)->value('tipo');
        return TiposEmpresaModel::where('id_tipo_empresa', $empresaTipo)->value('tipo_empresa');
    }

    public function CrearPermisos()
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->cuit_empresa = IdHelper::idEmpresa();
            $this->open =  true;
        }
    }
}
