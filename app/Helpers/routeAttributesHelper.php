<?php
// app/Helpers/RouteAttributesHelper.php

namespace App\Helpers;

use App\Models\PermisosRolesModel;
use App\Models\PermisoPorEmpresaModel;
use App\Models\RutasModel;
use App\Models\ConfiguracionRutasModel;
use App\Helpers\IdHelper;
use App\Models\AsignacionesRolesModel;

class RouteAttributesHelper
{
    /**
     * Obtiene los atributos configurados para la ruta actual, validando que el usuario tenga los permisos asignados.
     *
     * Este método obtiene:
     * - El id del usuario desde la sesión (auth()->id())
     * - El cuit de la empresa desde IdHelper::idEmpresa()
     * - La clase que invoca el helper para determinar la "ruta" actual mediante un mapeo
     *
     * @return mixed
     */
    public static function getRouteAttributes()
    {
        // 1. Obtener el id del usuario y el cuit de la empresa
        $user = auth()->id();
        $cuit = IdHelper::idEmpresa();

        // 2. Obtener el nombre de la clase que invoca el helper mediante debug_backtrace
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $callerClass = isset($backtrace[1]['class']) ? $backtrace[1]['class'] : null;
        if (!$callerClass) {
            return null;
        }
        // Remover el namespace, por ejemplo "App\Livewire\"
        $className = str_replace('App\\Livewire\\', '', $callerClass);
        // 3. Consultar los permisos asignados al usuario en la empresa
        $permisosRolesUsuario = AsignacionesRolesModel::where('cuit', $cuit)
            ->where('usuario_empresa', $user)
            ->get();

        $permisosRoles = PermisosRolesModel::where('id_rol', $permisosRolesUsuario->pluck('id_rol')->toArray())
            ->get();

        if ($permisosRoles->isEmpty()) {
            return null;
        }

        // Extraer todos los id_permiso asignados al usuario
        $idsPermisos = $permisosRoles->pluck('id_permiso')->toArray();

        $permisos = PermisoPorEmpresaModel::whereIn('id_permiso', $idsPermisos)
            ->where('cuit_empresa', $cuit)
            ->where('con_configuracion', 'Si')
            ->get();

        // 4. Consultar las rutas asignadas a esos permisos
        $rutas = RutasModel::whereIn('id_ruta', $permisos->pluck('id_ruta')->toArray())
            ->get();
        // Recorremos las rutas y, si encontramos la que coincide, llamamos a la función de configuración
        foreach ($rutas as $ruta) {
            if ($ruta->ruta === $className) {
                return self::configuracionRuta($ruta);
            }
        }

        return null;
    }
    /**
     * Función para obtener la configuración de la ruta y extraer los atributos.
     *
     * @param mixed $ruta
     * @return mixed
     */
    private static function configuracionRuta($ruta)
    {
        switch ($ruta->ruta) {
            case 'Menus\\Tablas':
                // Se obtienen todos los registros que coincidan y además cuyo nombre_config sea 'bienes'
                $configs = ConfiguracionRutasModel::where('id_ruta', $ruta->id_ruta)
                    ->where('nombre_config', 'bienes')
                    ->get();
                return $configs->isEmpty() ? null : $configs->pluck('atributos');
            case 'Ubicaciones\Ubicaciones':
                $configs = ConfiguracionRutasModel::where('id_ruta', $ruta->id_ruta)
                    ->where('nombre_config', 'tipo_ubicacion')->get();
                return $configs->isEmpty() ? null : $configs->pluck('atributos');
            case 'OtroCaso':
                $configs = ConfiguracionRutasModel::where('id_ruta', $ruta->id_ruta)->get();
                return $configs->isEmpty() ? null : $configs->pluck('atributos');
            default:
                return null;
        }
    }
}
