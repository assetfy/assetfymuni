<?php

// File: app/Http/Requests/StoreActivoValidacion.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreActivoValidacion extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    /**
     * Obtiene las reglas de validación según el paso actual.
     *
     * @param int $step
     * @return array
     */
    public static function rulesForStep(int $step): array
    {
        return match ($step) {
            1 => self::rulesStep1(),
            2 => self::rulesStep2(),
            3 => self::rulesStep3(),
            4 => self::rulesStep4(),
            5 => self::rulesStep5(),
            //6 => self::rulesStep6(),
            default => [],
        };
    }
    /**
     * Reglas para el paso 1: datos básicos.
     */
    private static function rulesStep1(): array
    {
        return [
            'id_tipo'         => 'required|integer',
            'id_categoria'    => 'required|integer',
            'id_subcategoria' => 'required|integer',
            'nombre'          => 'required|string|max:255',
            'id_ubicacion'         => 'nullable|integer',
            'selectedPiso'         => 'nullable|string|max:50',
        ];
    }
    /**
     * Reglas para el paso 2: atributos.
     */
    private static function rulesStep3(): array
    {
        return [
            'selectedAtributos'                         => 'required|array|min:1',
            'campo.*'                                   => 'nullable|string|max:255',
            'campo_numerico.*'                          => 'nullable|numeric',
            'fecha.*'                                   => 'nullable|date',
            'atributosSeleccionadosValoresCheckboxes.*' => 'nullable|array',
            'atributosSeleccionadosValoresSelects.*'    => 'nullable|integer',
        ];
    }
    /**
     * Reglas para el paso 3: ubicación y estado de alta.
     */
    private static function rulesStep2(): array
    {
        return [

            'numero_serie'         => 'nullable|string|max:100',
            // Si subes factura, fecha_compra pasa a ser obligatoria
            'fecha_compra'         => 'nullable|date|required_with:factura_compra',
            'factura_compra'       => 'nullable|file|mimes:pdf|max:10240',

            // Siempre debes indicar si hay garantía o no
            'garantia_vigente'     => 'required|in:Si,No',

            // Si elegiste “Sí” en garantía, el certificado y la fecha de vencimiento pasan a ser obligatorios
            'cert_garantia'        => 'nullable|file|mimes:pdf|max:10240|required_if:garantia_vigente,Si',
            'vencimiento_garantia' => 'nullable|date|after_or_equal:fecha_compra|required_if:garantia_vigente,Si',
        ];
    }
    /**
     * Reglas para el paso 4: fotos.
     */
    private static function rulesStep4(): array
    {
        return [
            // 'id_estado_sit_alta'   => 'required|integer',
            'comentarios_sit_alta' => 'nullable|string|max:1000',
            'nuevasFotos.*'    => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'capturedPhotos'   => 'nullable|array|max:10',
            'capturedPhotos.*' => 'nullable|string',
            'id_estado_sit_general' => 'required',
            'prestado' => 'nullable|string|required_if:id_estado_sit_general,5', // Nuevo campo para indicar si el activo está prestado
        ];
    }
    /**
     * Reglas para el paso 5: compra y garantía.
     */
    private static function rulesStep6(): array
    {
        return [
            // Si subes factura, fecha_compra pasa a ser obligatoria
            'fecha_compra'         => 'nullable|date|required_with:factura_compra',
            'factura_compra'       => 'nullable|file|mimes:pdf|max:10240',

            // Siempre debes indicar si hay garantía o no
            'garantia_vigente'     => 'required|in:Si,No',

            // Si elegiste “Sí” en garantía, el certificado y la fecha de vencimiento pasan a ser obligatorios
            'cert_garantia'        => 'nullable|file|mimes:pdf|max:10240|required_if:garantia_vigente,Si',
            'vencimiento_garantia' => 'nullable|date|after_or_equal:fecha_compra|required_if:garantia_vigente,Si',
        ];
    }
    /**
     * Reglas para el paso 6: asignaciones.
     */
    private function rulesStep5(): array
    {
        // Siempre son numéricos o nulos
        $rules = [
            'responsable_id'   => 'nullable|integer',
            'asignado_a_id'    => 'nullable|integer',
        ];

        // Si elegiste responsable o asignado, la fecha ahora sí es obligatoria
        if ($this->responsable_id || $this->asignado_a_id) {
            $rules['fecha_asignacion'] = 'required|date';
        } else {
            $rules['fecha_asignacion'] = 'nullable|date';
        }

        return $rules;
    }
}
