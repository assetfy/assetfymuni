<?php

namespace App\Http\Middleware;

use Symfony\Component\HttpFoundation\Response;
use App\Models\PermisoPorEmpresaModel;
use App\Models\AsignacionesRolesModel;
use App\Models\UsuariosEmpresasModel;
use App\Models\PermisosRolesModel;
use Illuminate\Http\Request;
use App\Models\RutasModel;
use App\Helpers\IdHelper;
use Closure;

class CheckPermisosRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Si el panel actual es "Usuario", permitir el acceso directamente
        if ($user->panel_actual === 'Usuario') {
            return $next($request);
        } else {
            // Si no es "Usuario", verificar los permisos
            return $this->verificarPermisos($request, $next, $user);
        }
    }
    /**
     * Verificar permisos para el usuario
     */
    private function verificarPermisos(Request $request, Closure $next, $user): Response
    {
        $cuit = IdHelper::idEmpresa();
        $empresa = UsuariosEmpresasModel::where('cuit', $cuit)->first();

        if (!$empresa) {
            return $this->accesoDenegado('No se encontró información de la empresa.');
        }

        // Si el usuario es Apoderado, se permite el acceso sin comprobar permisos adicionales
        $esApoderado = UsuariosEmpresasModel::where('id_usuario', $user->id)
            ->where('cuit', $empresa->cuit)
            ->where('cargo', 'Apoderado')
            ->exists();

        if ($esApoderado) {
            return $next($request);
        }

        $rolesAsignados = AsignacionesRolesModel::where('usuario_empresa', $user->id)
            ->where('cuit', $empresa->cuit)
            ->where('id_relacion_empresa', $empresa->id_relacion)
            ->pluck('id_rol');

        // Si no hay roles asignados, permitir el acceso
        if ($rolesAsignados->isEmpty()) {
            return $next($request);
        }

        $rutaActual = $request->path();
        // Verificar si la ruta tiene un permiso asignado
        $permisoPorEmpresa = $this->obtenerPermisoPorRuta($rutaActual);
        if (!$permisoPorEmpresa) {
            return $next($request);
        }
        // Verificar si el usuario tiene el permiso requerido
        if ($this->usuarioTienePermiso($rolesAsignados, $permisoPorEmpresa->id_permiso)) {
            return $next($request);
        }
        // Acceso denegado si no tiene permisos
        return $this->accesoDenegado('No tienes permisos para acceder a esta sección.');
    }

    /**
     * Obtener el permiso asignado a la ruta
     */
    private function obtenerPermisoPorRuta(string $rutaActual)
    {
        $ruta = RutasModel::where('ruta', $rutaActual)->first();
        if (!$ruta) {
            return null; // Permitir acceso si la ruta no está registrada
        }

        return PermisoPorEmpresaModel::where('id_ruta', $ruta->id_ruta)->first();
    }
    /**
     * Verificar si el usuario tiene el permiso requerido
     */
    private function usuarioTienePermiso($rolesAsignados, $idPermiso): bool
    {
        foreach ($rolesAsignados as $rolAsignado) {
            $permisos = PermisosRolesModel::where('id_rol', $rolAsignado)
                ->where('id_permiso', $idPermiso)
                ->exists();

            if ($permisos) {
                return true;
            }
        }
        return false;
    }
    /**
     * Redirigir con mensaje de acceso denegado
     */
    private function accesoDenegado(string $mensaje): Response
    {
        return redirect()->route('dashboard-empresa')
            ->with('error', $mensaje);
    }
}
