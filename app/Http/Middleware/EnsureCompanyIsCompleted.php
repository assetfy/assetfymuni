<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureCompanyIsCompleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (session('has_company', false)) {
            return $next($request);
        }

        // Redirigir a la creación de empresa con un mensaje
        return redirect()->route('create-empresas')->with('status', 'Debes crear tu empresa para acceder a esta sección.');
    }
}
