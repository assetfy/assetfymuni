<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserTipo
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && $user->tipo == 1) {
            // Usuario Administrador
            return $next($request);
        } elseif ($user && ($user->tipo == 2)) {
            return redirect()->route('dashboard-usuario') 
            ->with('error', 'No tienes permiso para acceder a esta sección.');
        }

        abort(403, 'No cuenta con los permisos necesarios');
    }

}