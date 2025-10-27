<?php

namespace App\Exports;

use App\Models\ActivosModel;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class ActivosExport implements FromCollection, WithHeadings, WithEvents, WithColumnWidths
{
    protected $selectedData;

    public function __construct($selectedData)
    {
        $this->selectedData = $selectedData;
    }

    /**
     * Devuelve la colección de datos a exportar.
     */
    public function collection()
    {
        // Carga relaciones necesarias
        $this->selectedData->load([
            'tipo',
            'categoria',
            'estadoGeneral',
            'ubicacion',
            'activosControles',
            'activosAtributos',
            'usuarioTitular',
            'empresaTitular',
            'nivelOrganizacion.padre',
        ]);

        return new Collection(
            $this->selectedData->map(function (ActivosModel $activo) {
                // Pertenencia: ruta jerárquica completa
                $unidad = $activo->nivelOrganizacion;
                $ruta = [];
                while ($unidad) {
                    array_unshift($ruta, $unidad->Nombre);
                    $unidad = $unidad->padre;
                }
                $pertenencia = count($ruta) ? implode(' → ', $ruta) : 'Raíz';

                // Controles y atributos
                $controles = $activo->activosControles->pluck('nombre')->implode(', ') ?: 'Sin Controles';
                $atributos = $activo->activosAtributos->pluck('nombre')->implode(', ') ?: 'Sin Atributos';

                return [
                    // Columnas visibles
                    'Etiqueta'              => $activo->etiqueta,
                    'Nombre'                => $activo->nombre,
                    'Tipo'                  => $activo->tipo->nombre ?? 'Sin Tipo',
                    'Categoría'             => $activo->categoria->nombre ?? 'Sin Categoría',
                    'Estado General'        => $activo->estadoGeneral->nombre ?? 'Sin Estado',
                    'Ubicación'             => $activo->ubicacion->nombre ?? 'Sin Ubicación',
                    'Controles Realizados'  => $controles,
                    'Atributos'             => $atributos,
                    'Servicios Realizados'  => $activo->serviciosSubcategoria->nombre ?? 'Sin Servicios',
                    'Usuario Titular'       => $activo->usuarioTitular->name ?? 'Sin Usuario',
                    'Empresa Titular'       => $activo->empresaTitular->razon_social ?? 'Sin Empresa',
                    'Pertenencia'           => $pertenencia,

                    // Columnas ocultas (raw IDs y otros)
                    'id_subcategoria'       => $activo->id_subcategoria,
                    'id_categoria'          => $activo->id_categoria,
                    'id_tipo'               => $activo->id_tipo,
                    'nombre_raw'            => $activo->nombre,
                    'id_estado_sit_alta'    => $activo->id_estado_sit_alta,
                    'comentarios_sit_alta'  => $activo->comentarios_sit_alta,
                    'id_estado_sit_general' => $activo->id_estado_sit_general,
                    'usuario_titular_id'    => $activo->usuario_titular,
                    'empresa_titular_id'    => $activo->empresa_titular,
                    'id_ubicacion'          => $activo->id_ubicacion,
                    'imagen'                => $activo->imagen,
                    'fecha_compra'          => $activo->fecha_compra,
                    'factura_compra'        => $activo->factura_compra,
                    'garantia_vigente'      => $activo->garantia_vigente,
                    'vencimiento_garantia'  => $activo->vencimiento_garantia,
                    'id_externo'            => $activo->id_externo,
                ];
            })->toArray()
        );
    }

    /**
     * Encabezados de columna.
     */
    public function headings(): array
    {
        return [
            'Etiqueta',
            'Nombre',
            'Tipo',
            'Categoría',
            'Estado General',
            'Ubicación',
            'Controles Realizados',
            'Atributos',
            'Servicios Realizados',
            'Usuario Titular',
            'Empresa Titular',
            'Pertenencia',

            // Ocultas
            'id_subcategoria',
            'id_categoria',
            'id_tipo',
            'nombre_raw',
            'id_estado_sit_alta',
            'comentarios_sit_alta',
            'id_estado_sit_general',
            'usuario_titular_id',
            'empresa_titular_id',
            'id_ubicacion',
            'imagen',
            'fecha_compra',
            'factura_compra',
            'garantia_vigente',
            'vencimiento_garantia',
            'id_externo',
        ];
    }

    /**
     * Define anchos de columna.
     */
    public function columnWidths(): array
    {
        return [
            'A'  => 15,  // Etiqueta
            'B'  => 30,  // Nombre
            'C'  => 20,  // Tipo
            'D'  => 20,  // Categoría
            'E'  => 20,  // Estado General
            'F'  => 20,  // Ubicación
            'G'  => 25,  // Controles Realizados
            'H'  => 25,  // Atributos
            'I'  => 25,  // Servicios Realizados
            'J'  => 20,  // Usuario Titular
            'K'  => 25,  // Empresa Titular
            'L'  => 50,  // Pertenencia
        ];
    }

    /**
     * Eventos AfterSheet para estilos y ocultar columnas.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow(); // número de fila final, p.ej. 100

                // 1) Cabecera en negrita (opcional)
                $sheet->getStyle('A1:L1')->getFont()->setBold(true);

                // 2) Pintar columnas A, B, C… con colores distintos
                $map = [
                    'A' => 'FFB3D9FF', // azul claro
                    'B' => 'FFC2E699', // verde claro
                    'C' => 'FFFFFF99', // amarillo claro
                    'D' => 'FFFFCCCC', // rojo claro
                    'E' => 'FFD9B3FF', // morado claro
                    // agrega más si necesitas…
                ];

                foreach ($map as $col => $color) {
                    // Rango desde fila 1 (header) hasta la última fila
                    $range = sprintf('%s1:%s%d', $col, $col, $highestRow);

                    $sheet->getStyle($range)
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB($color);
                }

                // 3) Ocultar columnas M en adelante (idéntico al ejemplo anterior)
                foreach (range('M', 'Z') as $col) {
                    $sheet->getColumnDimension($col)->setVisible(false);
                }
            },
        ];
    }
}
