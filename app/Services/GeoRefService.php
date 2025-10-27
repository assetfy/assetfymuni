<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GeoRefService
{
    protected string $base;

    public function __construct()
    {
        $this->base = config('services.georef.base_uri');
    }

    /**
     * Llamada genérica a GeoRef
     */
    private function fetch(string $endpoint, array $params = []): array
    {
        $base = config('services.georef.base_uri');

        $response = Http::withoutVerifying()   // <— desactiva verificación SSL
            ->timeout(10)
            ->get("{$base}/{$endpoint}", $params);

        if ($response->failed()) {
            throw new \Exception("GeoRef API error: {$response->status()}");
        }

        return $response->json($endpoint);
    }
    /**
     * Devuelve todas las provincias
     */
    public function provincias(): array
    {
        return $this->fetch('provincias', [
            'campos' => 'id,nombre',
            'max'    => 24,
        ]);
    }

    /**
     * Devuelve departamentos para una provincia dada
     */
    public function departamentos(string $provincia): array
    {
        return $this->fetch('departamentos', [
            'provincia' => $provincia,
            'campos'    => 'id,nombre',
            'max'       => 200,
        ]);
    }

    /**
     * Devuelve municipios para provincia y departamento
     */
    public function municipios(string $provincia, string $departamento): array
    {
        return $this->fetch('municipios', [
            'provincia'   => $provincia,
            'departamento' => $departamento,
            'campos'       => 'id,nombre',
            'max'          => 500,
        ]);
    }

    /**
     * Devuelve localidades para provincia, departamento y municipio
     */
    public function localidades(
        string $provinciaId,
    ): array {
        $params = [
            'provincia' => $provinciaId,     // aquí va siempre el código (ID)
            'campos'    => 'id,nombre',
            'max'       => 1000,
        ];

        // Sólo agrego estos filtros si no están vacíos
        if (! empty($departamentoId)) {
            $params['departamento'] = $departamentoId;
        }
        if (! empty($municipioId)) {
            $params['municipio'] = $municipioId;
        }

        return $this->fetch('localidades', $params);
    }
}
