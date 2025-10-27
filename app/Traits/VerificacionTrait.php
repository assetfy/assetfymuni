<?php

namespace App\Traits;

use App\Utils\StringNormalizer;
use Illuminate\Support\Facades\DB;

trait VerificacionTrait
{
    /**
     * Verifica y actualiza los campos de un modelo.
     *
     * @param \Illuminate\Database\Eloquent\Model $modelo
     * @param array $campos
     * @param array $valoresActualizados
     * @return void
     */
    public function verificar($modelo, array $campos, array $valoresActualizados)
    {
        $this->validate();

        $guardar = false;

        foreach ($campos as $campo) {
            // Verificar si el valor actualizado es un array (posible relación o estructura compleja)
            if (is_array($valoresActualizados[$campo] ?? null)) {
                // Manejo específico para el campo 'descripcion'
                if (isset($valoresActualizados['descripcion']) && $modelo->descripcion !== $valoresActualizados['descripcion']) {
                    $modelo->descripcion = $valoresActualizados['descripcion'];
                    $guardar = true;
                }

                // Iterar sobre los campos internos
                foreach ($campos as $campoInterno) {
                    if ($campoInterno !== 'descripcion' && isset($valoresActualizados[$campoInterno])) {
                        if ($modelo->{$campoInterno} !== $valoresActualizados[$campoInterno]) {
                            // Normalizar el nuevo valor para comparación
                            $valorSinAcentosNuevo = StringNormalizer::normalize($valoresActualizados[$campoInterno]);

                            // Actualizar el campo en el modelo
                            $modelo->{$campoInterno} = $valoresActualizados[$campoInterno];
                            $guardar = true;

                            // Verificar unicidad
                            if ($this->existeValorNormalizado($modelo, $campoInterno, $valorSinAcentosNuevo, $modelo->id)) {
                                $this->dispatch('warning');
                                $guardar = false;
                                break 2; // Salir de ambos bucles
                            }
                        }
                    }
                }
            } else {
                // Manejo para campos simples (no array)
                if (isset($valoresActualizados[$campo]) && $modelo->{$campo} !== $valoresActualizados[$campo]) {
                    // Normalizar el nuevo valor para comparación
                    $valorSinAcentosNuevo = StringNormalizer::normalize($valoresActualizados[$campo]);

                    // Actualizar el campo en el modelo
                    $modelo->{$campo} = $valoresActualizados[$campo];
                    $guardar = true;

                    // Verificar unicidad
                    if ($this->existeValorNormalizado($modelo, $campo, $valorSinAcentosNuevo, $modelo->id)) {
                        $this->dispatch('warning');
                        $guardar = false;
                        break;
                    }
                }
            }
        }

        // Guardar el modelo si hay cambios y pasar la validación de unicidad
        if ($guardar) {
            $modelo->save();
            $this->dispatch('lucky');
        }

        $this->close();
    }

    /**
     * Crea uno o varios nuevos registros en el modelo.
     *
     * @param \Illuminate\Database\Eloquent\Model $modelo
     * @param array $campos
     * @param array $valoresNuevos
     * @return void
     */
    public function create($modelo, array $campos, array $valoresNuevos)
    {
        $this->validate();

        // Determinar si se trata de una inserción múltiple
        $esMultiple = $this->esInsercionMultiple($valoresNuevos);

        // Iniciar una transacción para asegurar la integridad de las inserciones
        DB::beginTransaction();

        try {
            if ($esMultiple) {
                // Manejo para múltiples inserciones
                foreach ($valoresNuevos as $valores) {
                    if ($this->procesarCreacion($modelo, $campos, $valores)) {
                        // Registro creado exitosamente
                        // Continuar con el siguiente registro
                        continue;
                    } else {
                        // Si falla alguna inserción, revertir toda la transacción
                        DB::rollBack();
                        $this->close();
                        return;
                    }
                }

                // Si todas las inserciones fueron exitosas, confirmar la transacción
                DB::commit();
                $this->dispatch('lucky');
            } else {
                // Manejo para una sola inserción
                if ($this->procesarCreacion($modelo, $campos, $valoresNuevos)) {
                    DB::commit();
                    $this->dispatch('lucky');
                } else {
                    DB::rollBack();
                }
            }
        } catch (\Exception $e) {
            // En caso de excepción, revertir la transacción y manejar el error
            DB::rollBack();
            // Puedes personalizar el manejo de excepciones según tus necesidades
            throw $e;
        }

        $this->close();
    }

    /**
     * Procesa la creación de un registro individual.
     *
     * @param \Illuminate\Database\Eloquent\Model $modelo
     * @param array $campos
     * @param array $valores
     * @return bool
     */
    private function procesarCreacion($modelo, array $campos, array $valores): bool
    {
        foreach ($campos as $campo) {
            if (isset($valores[$campo])) {
                $valor = $valores[$campo];

                if (is_numeric($valor)) {
                    // Manejo para valores numéricos
                    $existingUnidad = $modelo::where($campo, $valor)->first();

                    if ($existingUnidad) {
                        $this->dispatch('warning');
                        return false;
                    }
                } else {
                    // Manejo para valores no numéricos
                    $valorNormalizado = StringNormalizer::normalize($valor);

                    // Verificar si ya existe un registro con el mismo valor normalizado
                    if ($modelo::whereRaw("LOWER(REPLACE(REPLACE(REPLACE($campo, 'á', 'a'), 'é', 'e'), 'í', 'i')) = ?", [strtolower($valorNormalizado)])->exists()) {
                        $this->dispatch('warning');
                        return false;
                    }
                }
            }
        }

        // Crear el registro si pasa las validaciones
        $modelo::create($valores);
        return true;
    }

    /**
     * Determina si los valores proporcionados corresponden a una inserción múltiple.
     *
     * @param array $valoresNuevos
     * @return bool
     */
    private function esInsercionMultiple(array $valoresNuevos): bool
    {
        // Una inserción múltiple es un array de arrays
        return isset($valoresNuevos[0]) && is_array($valoresNuevos[0]);
    }

    /**
     * Verifica si ya existe un valor normalizado en el modelo, excluyendo el registro actual.
     *
     * @param \Illuminate\Database\Eloquent\Model $modelo
     * @param string $campo
     * @param string $valorNormalizado
     * @param int|null $idActual
     * @return bool
     */
    private function existeValorNormalizado($modelo, string $campo, string $valorNormalizado, $idActual = null): bool
    {
        return $modelo::where($campo, '!=', $idActual)
                      ->get()
                      ->map(function ($item) use ($campo) {
                          return StringNormalizer::normalize($item->{$campo});
                      })
                      ->contains($valorNormalizado);
    }
}