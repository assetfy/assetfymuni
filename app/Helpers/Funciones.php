<?php

namespace App\Helpers;

use App\Models\EstadoGeneralModel;
use App\Models\TiposModel;

class Funciones
{
    public static function activoBaja()
    {
        $estadoBaja = EstadoGeneralModel::whereRaw('LOWER(nombre) = ?', ['baja'])->first();
        return $estadoBaja?->id_estado_sit_general;
    }

    public static function activoService()
    {
        $estadoService = EstadoGeneralModel::whereRaw('LOWER(nombre) = ?', ['en revisión/reparación'])->first();
        return $estadoService?->id_estado_sit_general;
    }

    public static function activosAmbos()
    {
        return array_filter([
            Funciones::activoBaja(),
            Funciones::activoService()
        ]);
    }

    public static function extraerClavesYValores(string $atributosStr): array
    {
        $resultado = [];

        $atributosStr = trim(preg_replace('/\s*\n\s*/', ', ', $atributosStr));

        $pares = explode(',', $atributosStr);

        foreach ($pares as $par) {
            $par = trim($par);
            if (empty($par)) continue;

            $partes = explode(':', $par, 2);

            if (count($partes) == 2) {
                $clave = trim($partes[0]);
                $valor = trim($partes[1]);
                $resultado[$clave] = $valor;
            }
        }

        return $resultado;
    }

     public static function whatType(int|string|null $tipo): ?string
    {
        if ($tipo === null) return null;

        // Si es id buscar por id; si es texto lo devuelve tal cual
        if (is_numeric($tipo)) {
            return TiposModel::where('id_tipo', (int)$tipo)->value('nombre');
        }
        return trim((string)$tipo);
    }

    public static function isInmueble(?string $tipo): bool
    {
        $nombre = strtolower(self::whatType($tipo) ?? '');
        return in_array($nombre, ['inmueble', 'inmuebles'], true);
    }
}