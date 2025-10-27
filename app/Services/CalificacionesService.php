<?php

namespace App\Services;

use App\Models\CalificacionesModel;
use Illuminate\Support\Facades\Http;

class CalificacionesService
{
    /**
     * Obtiene el total global (suma de diagnósticos, precio y calificación)
     * de todos los registros del proveedor.
     *
     * @param mixed $idPrestadora  El identificador del proveedor (valor del campo "proveedor")
     * @return float  El total global.
     */
    public function obtenerValorTotal($idPrestadora)
    {
        $datos = CalificacionesModel::whereHas('serviciosActivos', function ($query) use ($idPrestadora) {
            $query->where('proveedor', $idPrestadora);
        })->get();

        $totalGlobal = $datos->sum(function ($item) {
            return $item->diagnostico + $item->precio + $item->calificacion;
        });

        return $totalGlobal;
    }
    /**
     * Calcula el porcentaje de la suma de (diagnostico+precio+calificacion)
     * de los registros con contratacion = 1 respecto al total global.
     *
     * @param mixed $idPrestadora  El identificador del proveedor.
     * @return float  Porcentaje (valor entre 0 y 100).
     */
    public function obtenerPorcentajeContratacion1($idPrestadora)
    {
        // Se obtienen todos los registros del proveedor.
        $datos = CalificacionesModel::whereHas('serviciosActivos', function ($query) use ($idPrestadora) {
            $query->where('proveedor', $idPrestadora);
        })->get();

        // Se calcula el total global.
        $totalGlobal = $datos->sum(function ($item) {
            return $item->diagnostico + $item->precio + $item->calificacion;
        });

        // Se suma el total de los registros donde contratacion es 1.
        $totalContratacion1 = $datos->where('contratacion', 1)->sum(function ($item) {
            return $item->diagnostico + $item->precio + $item->calificacion;
        });

        // Se calcula el porcentaje.
        $porcentaje1 = $totalGlobal > 0 ? ($totalContratacion1 / $totalGlobal) * 100 : 0;

        return round($porcentaje1, 2);
    }
    /**
     * Calcula el porcentaje de la suma de (diagnostico+precio+calificacion)
     * de los registros con contratacion = 0 respecto al total global.
     *
     * @param mixed $idPrestadora  El identificador del proveedor.
     * @return float  Porcentaje (valor entre 0 y 100).
     */
    public function obtenerPorcentajeContratacion0($idPrestadora)
    {
        // Se obtienen todos los registros del proveedor.
        $datos = CalificacionesModel::whereHas('serviciosActivos', function ($query) use ($idPrestadora) {
            $query->where('proveedor', $idPrestadora);
        })->get();

        // Se calcula el total global.
        $totalGlobal = $datos->sum(function ($item) {
            return $item->diagnostico + $item->precio + $item->calificacion;
        });

        // Se suma el total de los registros donde contratacion es 0.
        $totalContratacion0 = $datos->where('contratacion', 0)->sum(function ($item) {
            return $item->diagnostico + $item->precio + $item->calificacion;
        });

        // Se calcula el porcentaje.
        $porcentaje0 = $totalGlobal > 0 ? ($totalContratacion0 / $totalGlobal) * 100 : 0;

        return round($porcentaje0, 2);
    }


    /**
     * Obtiene el ranking de motivos (diagnostico, precio, calificacion) 
     * según si el cliente volvería a contratar (contratacion = 1)
     * o no (contratacion = 0).
     *
     * Retorna un arreglo con dos claves: 'recomiendan' y 'no_recomiendan'.
     * Cada una es un arreglo asociativo donde la clave es el motivo y el valor es el
     * número de registros en los que ese motivo fue el mayor.
     *
     * @param mixed $idPrestadora
     * @return array
     */
    public function obtenerRankingMotivos($idPrestadora)
    {
        $ranking = [
            'recomiendan' => [],
            'no_recomiendan' => []
        ];

        // Para registros donde contratacion = 1 (recomiendan)
        $registros1 = CalificacionesModel::whereHas('serviciosActivos', function ($q) use ($idPrestadora) {
            $q->where('proveedor', $idPrestadora);
        })->where('contratacion', 1)->get();



        foreach ($registros1 as $registro) {
            $valores = [
                'diagnostico' => $registro->diagnostico,
                'precio'      => $registro->precio,
                'calificacion' => $registro->calificacion,
            ];
            // Se obtiene la clave del valor máximo
            $maxValor = max($valores);
            // Si hay empate, array_keys devolverá todos; tomamos el primero
            $motivo = array_keys($valores, $maxValor)[0];

            if (!isset($ranking['recomiendan'][$motivo])) {
                $ranking['recomiendan'][$motivo] = 0;
            }
            $ranking['recomiendan'][$motivo]++;
        }

        // Para registros donde contratacion = 0 (no recomiendan)
        $registros0 = CalificacionesModel::whereHas('serviciosActivos', function ($q) use ($idPrestadora) {
            $q->where('proveedor', $idPrestadora);
        })->where('contratacion', 0)->get();

        foreach ($registros0 as $registro) {
            $valores = [
                'diagnostico' => $registro->diagnostico,
                'precio'      => $registro->precio,
                'calificacion' => $registro->calificacion,
            ];
            $maxValor = max($valores);
            $motivo = array_keys($valores, $maxValor)[0];

            if (!isset($ranking['no_recomiendan'][$motivo])) {
                $ranking['no_recomiendan'][$motivo] = 0;
            }
            $ranking['no_recomiendan'][$motivo]++;
        }

        // Ordenar cada subarreglo de mayor a menor (opcional)
        arsort($ranking['recomiendan']);
        arsort($ranking['no_recomiendan']);
        return $ranking;
    }

    public function getReviewsGoogle($placeId)
    {
        $apiKey = config('services.google_maps.api_key');
        $fields = 'rating,user_ratings_total';

        // <- con withoutVerifying() desactivas solo aquí la verificación SSL
        $response = Http::withoutVerifying()
            ->get('https://maps.googleapis.com/maps/api/place/details/json', [
                'key'      => $apiKey,
                'place_id' => $placeId,
                'fields'   => $fields,
            ]);

        if ($response->successful()) {
            $data = $response->json();
            return [
                'rating'             => $data['result']['rating'] ?? null,
                'user_ratings_total' => $data['result']['user_ratings_total'] ?? 0,
            ];
        }

        return ['rating' => null, 'user_ratings_total' => 0];
    }
}
