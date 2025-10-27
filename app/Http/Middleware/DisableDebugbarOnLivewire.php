<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class DisableDebugbarOnLivewire
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('livewire/*')) {
            // Evita inyectar Debugbar en respuestas Livewire (incluye preview-file binario)
            if (class_exists(\Barryvdh\Debugbar\Facades\Debugbar::class)) {
                \Barryvdh\Debugbar\Facades\Debugbar::disable();
            }
        }
        return $next($request);
    }
}
