<?php

namespace App\Livewire\Servicios\Prestadora;

use App\Models\SolicitudesServiciosModel;
use App\Models\UsuariosEmpresasModel;
use App\Helpers\IdHelper;
use Livewire\Component;
use App\Models\User;

class AsignarEncargadoServicios extends Component
{
    public $open = false;
    public $servicios, $usuarios, $representante, $prestadora, $cuil;

    public function mount()
    {
        $this->prestadora = IdHelper::idEmpresa();
        $this->servicios = SolicitudesServiciosModel::where('empresa_prestadora', $this->prestadora)
            ->where('estado_presupuesto', 'Confirmado por Cliente y esperando visita')->get();
    }

    public function buscarUsuario()
    {
        $this->usuarios = null;
        $id_usuario = User::where('cuil', $this->cuil)->first();

        if ($id_usuario) {
            $existUser = UsuariosEmpresasModel::where('id_usuario', $id_usuario->id)
                ->where('cuit', $this->prestadora)
                ->where('es_representante_tecnico', 'Si')
                ->first();

            if ($existUser) {
                $this->usuarios = User::find($existUser->id_usuario);
            } else {
                session()->flash('error', 'El usuario no es representante técnico de esta empresa.');
            }
        } else {
            session()->flash('error', 'No se encontró un usuario con ese CUIL.');
        }
    }

    public function render()
    {
        return view('livewire.servicios.prestadora.asignar-encargado-servicios');
    }

    public function close()
    {
        $this->open = false;
    }
}
