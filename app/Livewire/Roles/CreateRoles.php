<?php

namespace App\Livewire\Roles;

use App\Services\MiddlewareInvoker;
use App\Traits\SortableTrait;
use App\Models\EmpresasModel;
use App\Models\RolesModel;
use App\Helpers\IdHelper;
use App\Models\TiposEmpresaModel;
use Livewire\Component;
use Illuminate\Support\Facades\DB;

class CreateRoles extends Component
{
    use SortableTrait;
    public $open = false;
    public $nombre, $cuit, $tipoEmpresa, $id_tipo, $tipoUsuario;

    protected $listeners = ['CrearRoles'];

    protected $rules =
    [
        'nombre' => 'required|max:100|min:3',
    ];

    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    public function mount()
    {
        $this->cuit  = IdHelper::empresaActual()->cuit;
        $this->tipoEmpresa = TiposEmpresaModel::all();
        $ue = IdHelper::usuarioEmpresaActual();
        $this->tipoUsuario = ((int)($ue?->tipo_user) === 3) ? 3 : null;

        // if($id == null){
        //     $user = Auth::user();
        //     $this->cuit = $user->entidad;
        // }else{
        //     $this->cuit = $id;
        // }
    }

    public function save()
    {
        DB::beginTransaction();
        try {

            $this->validate();

            RolesModel::create([
                'nombre' => $this->nombre,
                'cuit' => $this->cuit,
                'tipo_empresa' => $this->id_tipo,
            ]);

            DB::commit();

            $this->eventos();
        } catch (\Exception $e) {
            DB::rollBack();

            $this->dispatch('errorInfo', [
                'title'   => 'Error al guardar',
                'message' => $e->getMessage()
            ]);
        }

        $this->close();
    }

    public function render()
    {
        $empresas = EmpresasModel::all();
        return view('livewire.roles.create-roles', compact(['empresas']));
    }

    public function close()
    {
        $this->reset(['nombre', 'id_tipo']);
        $this->open = false;
    }

    public function CrearRoles()
    {
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if (!MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        } else {
            $this->open = true;
        }
    }
}
