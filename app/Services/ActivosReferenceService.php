<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use App\Models\SubcategoriaModel;
use App\Models\TiposModel;
use App\Models\EstadosAltasModel;
use App\Models\CondicionModel;
use App\Models\EstadoGeneralModel;
use App\Models\TiposCamposModel;
use App\Models\CategoriaModel;

class ActivosReferenceService
{
    /**
     * Obtiene y cachea todos los datos de referencia para el componente Activos.
     *
     * @return array<string, \Illuminate\Support\Collection>
     */
    // app/Services/ActivosReferenceService.php
    public function getReferenceData(): array
    {
        $ttl = now()->addHours(6);
        return Cache::remember('create_activos_reference_data', $ttl, function () {

            // ----- Bases en array plano -----
            $subcategorias = SubcategoriaModel::select(
                'id_subcategoria',
                'id_categoria',
                'id_tipo',
                'nombre'
            )->get()
                ->map(fn($m) => $m->toArray());
            $tipos = TiposModel::whereIn(
                'id_tipo',
                $subcategorias->pluck('id_tipo')->filter()->unique()->map(fn($v) => (int)$v)
            )
                ->get(['id_tipo', 'nombre'])
                ->map(fn($m) => [
                    'id_tipo' => (int) $m->id_tipo,
                    'nombre'  => $m->nombre,
                ])
                ->values()
                ->all();
            $categorias = CategoriaModel::all()->map(fn($m) => [
                'id_categoria' => (int)$m->id_categoria,
                'id_tipo'      => (int)$m->id_tipo,
                'nombre'       => $m->nombre,
            ])->toArray();

            $altas = EstadosAltasModel::whereIn('nombre', ['Nuevo', 'Usado'])
                ->get()->map(fn($m) => [
                    'id_estado_sit_alta' => (int)$m->id_estado_sit_alta,
                    'nombre'             => $m->nombre,
                ])->toArray();

            $condiciones = CondicionModel::all()->map(fn($m) => [
                'id_condicion' => (int)$m->id_condicion,
                'nombre'       => $m->nombre,
            ])->toArray();

            $general = EstadoGeneralModel::all()->map(fn($m) => [
                'id_estado_sit_general' => (int)$m->id_estado_sit_general,
                'nombre'                => $m->nombre,
            ])->toArray();

            $campos = TiposCamposModel::all()->map(fn($m) => [
                'id_campo' => (int)$m->id_campo,
                'nombre'   => $m->nombre,
            ])->toArray();

            // ------ índices en array ------
            // (los armamos acá para no recalcular siempre)
            $tiposById        = collect($tipos)->keyBy('id_tipo')->toArray();
            $categoriasByTipo = collect($categorias)->groupBy('id_tipo')->map->values()->toArray();
            $subsByCategoria  = collect($subcategorias)->groupBy('id_categoria')->map->values()->toArray();
            $subsById         = collect($subcategorias)->keyBy('id_subcategoria')->toArray();
            return [
                'subcategorias'        => $subcategorias,
                'tipoPrueba'           => $tipos,
                'altas'                => $altas,
                'condiciones'          => $condiciones,
                'general'              => $general,
                'campos'               => $campos,
                'categorias'           => $categorias,
                // índices
                'tipos_by_id'          => $tiposById,
                'categorias_by_tipo'   => $categoriasByTipo,
                'subs_by_categoria'    => $subsByCategoria,
                'subs_by_id'           => $subsById,
            ];
        });
    }


    /**
     * Borra la caché de los datos de referencia.
     *
     * @return void
     */
    public function clearCache(): void
    {
        Cache::forget('create_activos_reference_data');
    }
}
