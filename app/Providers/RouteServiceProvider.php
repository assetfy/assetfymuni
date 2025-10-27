<?php

namespace App\Providers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @return string
     */
    public static function home()
    {
        $user = Auth::user();

        if ($user) {
            switch ($user->panel_actual) {
                case 'Usuario':
                    return '/usuarios/dashboard-usuario';
                case 'Empresa':
                    return '/empresas/dashboard-empresa';
                case 'Prestadora':
                    return '/empresas/dashboard-empresa';
                case 'Controladora':
                case 'Estado':
                    return '/estado/dashboard-estado';
                default:
                    return '/usuarios/dashboard-usuario'; // Fallback por si no se encuentra el panel_actual
            }
        }

        // Fallback por si no hay usuario autenticado o si ocurre algún error
        return '/login';
    }
}
