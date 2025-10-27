<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use App\Models\UsuariosEmpresasModel;

class CheckTipoUserEmpresa
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        // Verificar si el usuario es de tipo 2
        $tipo = UsuariosEmpresasModel::where('id_usuario', $user->id)->where('cuit',$user->entidad)->value('tipo_user');
        if ($tipo == 2) {
            // El usuario es de tipo 2, permitir el acceso
            return $next($request);
        } else {
            // El usuario no es de tipo 2, denegar el acceso y redirigir al dashboard
            return redirect()->route('dashboard');
        }
    }
}
