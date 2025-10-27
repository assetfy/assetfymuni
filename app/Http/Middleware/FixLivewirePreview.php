<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class FixLivewirePreview
{
    public function handle(Request $request, Closure $next)
    {
        // Solo tocar el preview binario de Livewire
        if ($request->routeIs('livewire.preview-file')) {
            // Apagar compresión/ob para que no contamine el stream
            @ini_set('zlib.output_compression', '0');
            // Vaciar TODOS los output buffers activos
            while (ob_get_level() > 0) {
                @ob_end_clean();
            }
        }

        return $next($request);
    }
}
