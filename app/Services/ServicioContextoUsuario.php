<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Helpers\IdHelper;
use App\Models\UsuariosEmpresasModel;
use App\Models\AsignacionesRolesModel;
use App\Models\RolesModel;

/**
 * Servicio que proporciona el contexto de usuario (perfiles y permisos) con caché integrado.
 */
class ServicioContextoUsuario
{
    /**
     * Determina si el usuario actual es Apoderado de la empresa.
     *
     * @return bool
     */
    public function esApoderado(): bool
    {
        $userId = IdHelper::idEmpresa();
        $user   = IdHelper::usuarioEmpresaActual()->id_usuario;

        return Cache::remember(
            "ctx_apoderado_{$user}_{$userId}",
            now()->addMinutes(15),
            fn() => UsuariosEmpresasModel::where('id_usuario', $user)
                ->where('cuit', $userId)
                ->where('cargo', 'Apoderado')
                ->exists()
        );
    }

    /**
     * Obtiene los IDs del rol "Admin Empresa".
     *
     * @return int[]
     */
    public function obtenerIdsRolAdmin(): array
    {
        $user = IdHelper::usuarioEmpresaActual()->id_usuario;

        return Cache::remember(
            "ctx_idsRolAdmin_{$user}",
            now()->addMinutes(15),
            fn() => RolesModel::where('nombre', 'Admin Empresa')
                ->pluck('id_rol')
                ->toArray()
        );
    }
    /**
     * Determina si el usuario actual tiene el rol "Admin Empresa".
     *
     * @return bool
     */
    public function esAdminEmpresa(): bool
    {
        $userId =  IdHelper::idEmpresa();
        $user   = IdHelper::usuarioEmpresaActual()->id_usuario;
        $idsRol = $this->obtenerIdsRolAdmin();

        return Cache::remember(
            "ctx_adminEmpresa_{$user}_{$userId}",
            now()->addMinutes(15),
            fn() => AsignacionesRolesModel::where('usuario_empresa', $user)
                ->where('cuit', $userId)
                ->whereIn('id_rol', $idsRol)
                ->exists()
        );
    }
}
