<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckUserEstado
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        // Verifica que el usuario no se halle dado de baja con el estado = 2
        if ($user && $user->estado == 2) {

            // Invalidar la sesión actual y cierra la cuenta
            $request->session()->invalidate();

            // Redirigir al inicio de sesión con un mensaje de error
            return redirect()->route('login')->withErrors(['estado' => ['Tu cuenta ha sido dada de baja.', 'Por favor, contacta con soporte.']]);
        }

        return $next($request);
    }
}
