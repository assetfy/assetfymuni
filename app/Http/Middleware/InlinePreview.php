<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class InlinePreview
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Caso especial: preview de Livewire (binario). ¡NO tocar el body!
        if ($request->routeIs('livewire.preview-file')) {
            // Solo ajustar Content-Disposition si vino como "attachment"
            $disposition = $response->headers->get('Content-Disposition');

            if ($disposition && str_starts_with(strtolower($disposition), 'attachment')) {
                // intenta conservar el filename si existe
                $filename = 'preview';
                if (preg_match('/filename="?([^"]+)"?/i', $disposition, $m)) {
                    $filename = $m[1];
                }
                $response->headers->set('Content-Disposition', 'inline; filename="' . $filename . '"');
            }

            // Importante: devolver tal cual (no tocar contenido)
            return $response;
        }

        // Para el resto de rutas: NO procesar nada que no sea HTML
        $ct = $response->headers->get('Content-Type');
        if (! $ct || stripos($ct, 'text/html') === false) {
            return $response;
        }

        // (Si algún día necesitas modificar HTML, hacelo acá)
        // $html = $response->getContent();
        // ... tus transformaciones ...
        // $response->setContent($html);

        return $response;
    }
}
