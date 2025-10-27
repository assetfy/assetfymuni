<?php

namespace App\Livewire\Activos\Bienes;

use Livewire\Component;
use App\Helpers\IdHelper;
use App\Models\ActivosCompartidosModel;
use App\Models\ActivosModel;
use App\Services\ServicioContextoUsuario;

class BienesEmpresa extends Component
{
    public $datos, $userId, $esApoderado, $admin, $bienesCount, $bienesDelegados, $bienesPendientes;
    public $tablaActual = 'tablas'; // Valor predeterminado

    protected ServicioContextoUsuario $servicioContextoUsuario;

    public function mount(ServicioContextoUsuario $servicioContextoUsuario)
    {
        $this->servicioContextoUsuario = $servicioContextoUsuario;
        $this->userId = IdHelper::identificador();
        // Obtengo “si es apoderado” desde caché
        $this->esApoderado = $this->servicioContextoUsuario->esApoderado();
        //  Obtengo “si es admin empresa” desde caché
        $this->admin = $this->servicioContextoUsuario->esAdminEmpresa();
        // Cálculo los totales una sola vez
        $this->bienesCount      = $this->activosRegistrados()->count();
        $this->bienesDelegados  = $this->activosDelegados()->count();
        $this->bienesPendientes = $this->activosPendientes()->count();
    }

    public function mostrarBienesPropios()
    {
        $this->tablaActual = 'tablas';
    }

    public function mostrarBienesPendientes()
    {
        $this->tablaActual = 'bienes-pendientes';
    }

    public function mostrarBienesAceptados()
    {
        $this->tablaActual = 'bienes-delegados';
    }
    //get + StudlyCase de tu nombre de variable + Property. Ejemplo: para $this->idsActivos el método es getIdsActivosProperty().Usarlo como propiedad 
    //Donde antes usabas un método o una variable, ahora escribes $this->idsActivos y Livewire llamará al getter.
    public function getIdsActivosProperty()
    {
        return ActivosModel::where('empresa_titular', IdHelper::idEmpresa())
            ->pluck('id_activo');
    }

    private function activosRegistrados()
    {
        $idsCompartidos = ActivosCompartidosModel::whereIn('id_activo', $this->idsActivos)
            ->where(function ($query) {
                $query->whereNotIn('estado_asignacion', ['Cancelado', 'En Revisión'])
                    ->whereNull('fecha_fin');
            })
            ->pluck('id_activo');

        // Filtramos todos los activos que NO están compartidos actualmente
        return ActivosModel::where('empresa_titular', IdHelper::idEmpresa())
            ->whereNotIn('id_activo', $idsCompartidos)
            ->get();
    }

    private function activosDelegados()
    {
        $idsCompartidos = ActivosCompartidosModel::whereIn('id_activo', $this->idsActivos)
            ->where(function ($query) {
                $query->where('estado_asignacion', 'Aceptado')
                    ->whereNull('fecha_fin');
            })
            ->pluck('id_activo');

        // Filtramos todos los activos que NO están compartidos actualmente
        return ActivosModel::where('empresa_titular', IdHelper::idEmpresa())
            ->whereIn('id_activo', $idsCompartidos)
            ->get();
    }

    private function activosPendientes()
    {
        $idsCompartidos = ActivosCompartidosModel::whereIn('id_activo', $this->idsActivos)
            ->where(function ($query) {
                $query->where('estado_asignacion', 'En Revisión')
                    ->whereNull('fecha_fin');
            })
            ->pluck('id_activo');

        // Filtramos todos los activos que NO están compartidos actualmente
        return ActivosModel::where('empresa_titular', IdHelper::idEmpresa())
            ->whereIn('id_activo', $idsCompartidos)
            ->get();
    }

    public function render()
    {
        return view('livewire.activos.bienes.bienes-empresa', ['apoderado' => $this->esApoderado, 'admin' => $this->admin]);
    }
}
