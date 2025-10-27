<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\EmpresasModel;
use Illuminate\Support\Facades\Auth;
use App\Helpers\IdHelper;

class CheckTipoEmpresa
{
    public function handle(Request $request, Closure $next)
    {
        session()->put('user_data', null);
        $user = Auth::user();

        if ($user && in_array($user->panel_actual, ['Empresa', 'Estado', 'Controladora', 'Prestadora'])) {
            $id = IdHelper::idEmpresa();
            $empresa = EmpresasModel::where('cuit', $id)
                ->where(function ($query) {
                    $query->whereIn('tipo', [1, 2, 3, 4]);
                })
                ->first();

            if ($empresa) {
                return $next($request);
            } else {
                return redirect()->route('login')->withErrors(['estado' => 'Tu cuenta está suspendida.']);
            }
        }

        return redirect()->route('dashboard-usuario');
    }
}
