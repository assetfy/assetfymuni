<?php

namespace App\Services;

use App\Models\AtributosModel;
use App\Models\AtributosValoresModel;
use Carbon\Carbon;

class GeneradorDeAtributosDeActivo
{
    protected array $selectedAtributos;
    protected array $valoresCheckbox;
    protected array $valoresSelect;
    protected array $camposTexto;
    protected array $camposNumericos;
    protected array $fechas;
    protected int   $idSubcategoria;
    protected int   $idCategoria;
    protected int   $idTipo;

    /**
     * Recibe en el constructor todos los datos necesarios para armar
     * los registros de atributos.
     */
    public function __construct(
        array $selectedAtributos,
        array $valoresCheckbox,
        array $valoresSelect,
        array $camposTexto,
        array $camposNumericos,
        array $fechas,
        int   $idSubcategoria,
        int   $idCategoria,
        int   $idTipo,
    ) {
        $this->selectedAtributos  = $selectedAtributos;
        $this->valoresCheckbox    = $valoresCheckbox;
        $this->valoresSelect      = $valoresSelect;
        $this->camposTexto        = $camposTexto;
        $this->camposNumericos    = $camposNumericos;
        $this->fechas             = $fechas;
        $this->idSubcategoria     = $idSubcategoria;
        $this->idCategoria        = $idCategoria;
        $this->idTipo             = $idTipo;
    }

    /**
     * Genera un array de arrays con los datos listos para insertar
     * en la tabla activos_atributos.
     *
     * @param  int   $activoId
     * @return array Cada elemento es un array $data para un create()
     */
    public function handle(int $activoId): array
    {
        $filas = [];

        foreach ($this->selectedAtributos as $idAtributo => $seleccionado) {
            // 1) Solo procesar si está marcado
            if (! $seleccionado) {
                continue;
            }

            // 2) Cargar la definición del atributo
            $atributo = AtributosModel::find($idAtributo);
            if (! $atributo) {
                continue;
            }

            // 3) Inicializar valores enum
            $campoEnum      = null;
            $campoEnumList  = null;
            $campoEnumId    = null;

            // 4) Si es predefinido, procesar valores enum
            if ($atributo->predefinido === 'Si') {
                if ($atributo->SelectM === 'Si') {
                    // Múltiple: checkbox → array de IDs
                    $ids = array_keys(array_filter(
                        $this->valoresCheckbox[$idAtributo] ?? [],
                        fn($v) => $v === true
                    ));
                    if (! empty($ids)) {
                        $campoEnumId   = implode(',', $ids);
                        $nombres       = AtributosValoresModel::whereIn('id_valor', $ids)
                            ->pluck('valor')
                            ->toArray();
                        $campoEnumList = implode(',', $nombres);
                    }
                } else {
                    // Simple: select único
                    $idValor = $this->valoresSelect[$idAtributo] ?? null;
                    if ($idValor) {
                        $campoEnumId = $idValor;
                        $campoEnum   = AtributosValoresModel::where('id_valor', $idValor)
                            ->value('valor');
                    }
                }
            }

            // 5) Armar el array base de datos
            $data = [
                'id_atributo'            => $idAtributo,
                'id_activo'              => $activoId,
                'id_subcategoria_activo' => $this->idSubcategoria,
                'id_categoria_activo'    => $this->idCategoria,
                'id_tipo_activo'         => $this->idTipo,
                'campo'                  => $this->camposTexto[$idAtributo] ?? null,
                'campo_numerico'         => $this->camposNumericos[$idAtributo] ?? null,
                'fecha'                  => isset($this->fechas[$idAtributo])
                    ? Carbon::parse($this->fechas[$idAtributo])->format('Y-m-d')
                    : null,
            ];

            // 6) Si era predefinido, añadir los campos enum
            if ($atributo->predefinido === 'Si') {
                if ($atributo->SelectM === 'Si') {
                    $data['campo_enum_list'] = $campoEnumList;
                    $data['campo_enum_id']   = $campoEnumId;
                } else {
                    $data['campo_enum']      = $campoEnum;
                    $data['campo_enum_id']   = $campoEnumId;
                }
            }

            // 7) Agregar a la lista final
            $filas[] = $data;
        }

        return $filas;
    }
}
