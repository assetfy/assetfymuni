<?php

namespace App\Livewire\Servicios;

use Livewire\Component;
use App\Helpers\IdHelper;
use App\Models\SolicitudesServiciosModel;
use App\Models\UsuariosEmpresasModel;
use App\Services\MiddlewareInvoker;
use Illuminate\Support\Facades\DB;

class AsignarTecnicoEncargado extends Component
{
    // Control del modal
    public $open = false;

    // Datos de la solicitud
    public $solicitud;

    // Colección de representantes técnicos (con relación "usuarios" para obtener el name)
    public $tecnicos = [];

    // Texto del buscador
    public $searchTecnico = '';

    // Valores seleccionados
    public $selectedTecnicoId;
    public $selectedTecnicoName;

    // Escuchamos el evento para abrir el modal
    protected $listeners = ['openASignarTecnicoEncargado'];

    public function render()
    {
        return view('livewire.servicios.asignar-tecnico-encargado');
    }

    public function openASignarTecnicoEncargado($payload)
    {
        $servicioId = $payload['servicioId'] ?? null;
        if (! $servicioId) {
            return;
        }

        // Verifica permisos
        if (! MiddlewareInvoker::checkPermisosRoles()) {
            $this->dispatch('no-permission', ['message' => 'No tiene permiso para realizar esta acción.']);
            return;
        }
        // Carga la solicitud
        $this->solicitud = SolicitudesServiciosModel::find($servicioId);
        // Carga lista inicial de técnicos
        $this->cargarTecnicos();
        // Si ya hay un técnico asignado, precárgalo
        if ($this->solicitud->tecnico_id) {
            $this->selectedTecnicoId   = $this->solicitud->tecnico_id;
            // Busca el registro en usuarios_empresas para obtener el nombre
            $ue = UsuariosEmpresasModel::with('usuarios')
                ->where('cuit', IdHelper::idEmpresa())
                ->where('id_usuario', $this->solicitud->tecnico_id)
                ->first();
            $this->selectedTecnicoName = $ue->usuarios->name ?? 'N/A';
        }
        // Reinicia buscador y abre modal
        $this->searchTecnico      = '';
        $this->open               = true;
    }

    protected function cargarTecnicos()
    {
        $this->tecnicos = UsuariosEmpresasModel::with('usuarios')
            ->where('cuit', IdHelper::idEmpresa())
            ->where('es_representante_tecnico', 'Si')
            ->whereNot('estado', 'Deshabilitado')
            ->get();
    }

    public function updatedSearchTecnico($value)
    {
        $query = UsuariosEmpresasModel::with('usuarios')
            ->where('cuit', IdHelper::idEmpresa())
            ->where('es_representante_tecnico', 'Si')
            ->whereNot('estado', 'Deshabilitado');

        if ($value) {
            $query->whereHas('usuarios', function ($q) use ($value) {
                $q->where('name', 'like', "%{$value}%");
            });
        }

        $this->tecnicos = $query->get();
    }

    public function setTecnico($idUsuario)
    {
        $usuarioEmpresa = UsuariosEmpresasModel::with('usuarios')
            ->where('cuit', IdHelper::idEmpresa())
            ->where('id_usuario', $idUsuario)
            ->where('es_representante_tecnico', 'Si')
            ->first();

        if ($usuarioEmpresa) {
            $this->selectedTecnicoId = $usuarioEmpresa->id_usuario;
            $this->selectedTecnicoName = $usuarioEmpresa->usuarios->name ?? 'N/A';
        }
    }
    public function asignarTecnico()
    {
        if (! $this->solicitud || ! $this->selectedTecnicoId) {
            $this->dispatch('errorInfo', [
                'title'   => 'Error en la Asignación',
                'message' => 'Debe seleccionar un técnico antes de asignarlo.'
            ]);
            return;
        }

        DB::beginTransaction();
        try {
            $this->solicitud->tecnico_id = $this->selectedTecnicoId;
            $this->solicitud->save();
            DB::commit();

            $this->dispatch('Exito', [
                'title'   => 'Asignación Exitosa',
                'message' => 'Técnico asignado correctamente.'
            ]);

            $this->open = false;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('errorInfo', [
                'title'   => 'Error al Asignar Técnico',
                'message' => $e->getMessage()
            ]);
        }
        $this->dispatch('refreshLivewireTable');
    }
}
