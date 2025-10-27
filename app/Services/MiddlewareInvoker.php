<?php

namespace App\Services;

use App\Models\PermisoPorEmpresaModel;
use App\Models\AsignacionesRolesModel;
use Illuminate\Support\Facades\Auth;
use App\Models\PermisosRolesModel;
use App\Models\RutasModel;
use App\Helpers\IdHelper;

class MiddlewareInvoker
{
    public static function checkPermisosRoles()
    {
        $user = Auth::user();
        // Obtener el nombre de la clase llamante y formatearlo
        $nombreClase = str_replace('App\\Livewire\\', '', debug_backtrace()[1]['class']);
        if ($user->panel_actual == 'Usuario') {
            return true;
        } else {
            $Empresa = IdHelper::empresaActual();
            $UsuarioEmpresa = IdHelper::usuarioEmpresaActual();
            $rolesAsignados = AsignacionesRolesModel::where('usuario_empresa',     $UsuarioEmpresa->id_usuario)
                ->where('cuit', $UsuarioEmpresa->cuit)
                ->where('id_relacion_empresa', $UsuarioEmpresa->id_relacion)
                ->pluck('id_rol');
        }
        // Verificar si el cargo es apoderado no se piden permisos
        if ($UsuarioEmpresa->cargo == 'Apoderado') {
            return true;
        }
        //prueba para ruta
        if ($UsuarioEmpresa->tipo_user == 3) {
            return true;
        }
        // Verificar si la ruta actual tiene un permiso asignado
        $ruta = RutasModel::where('ruta', $nombreClase)->first();
        if (!$ruta) {
            return true;
        }
        $permisoPorEmpresa = PermisoPorEmpresaModel::where('id_ruta', $ruta->id_ruta)
            ->where('tipo_empresa', $Empresa->tipo)->get();

        if (!$permisoPorEmpresa) {
            return true;
        }

        foreach ($rolesAsignados as $rolAsignado) {
            $permisos = PermisosRolesModel::where('id_rol', $rolAsignado)
                ->whereIn('id_permiso', $permisoPorEmpresa->pluck('id_permiso')->toArray())
                ->exists();
            if ($permisos) {
                return true;
            }
        }

        session()->flash('error', 'No tiene permiso para acceder a esta página.');
        return false;
    }
}
