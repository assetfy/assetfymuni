<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Http;

class GeocodingService
{
    /**
     * Realiza geocodificación directa (forward geocoding) usando la API de Google Maps
     * a partir de los componentes de una dirección.
     *
     * Se esperan los siguientes índices en el arreglo $addressComponents:
     * - 'pais'
     * - 'provincia'
     * - 'ciudad'
     * - 'codigo_postal'
     * - 'calle'
     * - 'altura'
     *
     * @param array $addressComponents
     * @return array|null Devuelve ['lat' => ..., 'lon' => ...] o null si no se encuentra
     * @throws Exception
     */
    public function geocodeAddress(array $addressComponents): ?array
    {
        // Construir la consulta a partir de los componentes de la dirección.
        $parts = [];
        if (!empty($addressComponents['calle'])) {
            $parts[] = $addressComponents['calle'];
        }
        if (!empty($addressComponents['altura'])) {
            $parts[] = $addressComponents['altura'];
        }
        if (!empty($addressComponents['ciudad'])) {
            $parts[] = $addressComponents['ciudad'];
        }
        if (!empty($addressComponents['provincia'])) {
            $parts[] = $addressComponents['provincia'];
        }
        if (!empty($addressComponents['pais'])) {
            $parts[] = $addressComponents['pais'];
        }
        if (!empty($addressComponents['codigo_postal'])) {
            $parts[] = $addressComponents['codigo_postal'];
        }
        $query = implode(', ', $parts);

        // Usar la API de geocodificación de Google Maps.
        // Se obtiene la API key desde la configuración (config/services.php o .env)
        $apiKey = config('services.google_maps.api_key');
        if (!$apiKey) {
            throw new Exception('Google Maps API key not configured.');
        }
        $url = 'https://maps.googleapis.com/maps/api/geocode/json?address=' . urlencode($query) . '&key=' . $apiKey;

        try {
            $response = Http::get($url);
            if ($response->successful()) {
                $data = $response->json();
                if (!empty($data['results'])) {
                    // Tomar el primer resultado
                    $location = $data['results'][0]['geometry']['location'];
                    return [
                        'lat' => $location['lat'] ?? null,
                        'lon' => $location['lng'] ?? null,
                    ];
                }
            }
            return null;
        } catch (Exception $e) {
            // Opcional: registrar el error o manejarlo de otra forma
            return null;
        }
    }

    /**
     * Realiza geocodificación para múltiples direcciones usando Google Maps.
     *
     * @param array $addresses Array de direcciones, donde cada elemento es un arreglo con los componentes.
     * @return array Array de resultados con latitud y longitud para cada dirección.
     */
    public function geocodeAddresses(array $addresses): array
    {
        $results = [];
        foreach ($addresses as $index => $address) {
            $results[$index] = $this->geocodeAddress($address);
        }
        return $results;
    }
}
