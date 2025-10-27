<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class OrdenesExport implements FromCollection, WithHeadings, WithColumnWidths, WithEvents
{
    protected Collection $data;

    public function __construct(Collection $selectedData)
    {
        // Eager-load relaciones para evitar N+1
        $this->data = $selectedData
            ->load(['proveedores']);
    }

    /**
     * Devuelve una colección de arrays, uno por fila,
     * en el mismo orden que los headings.
     */
    public function collection()
    {
        return $this->data->map(function ($item) {
            $fecha = $item->fecha instanceof Carbon
                ? $item->fechaHora->format('Y-m-d H:i')
                : Carbon::parse($item->fechaHora)->format('Y-m-d H:i');
            return [
                $item->proveedores->razon_social ?? '', // B: Proveedor
                $item->comentarios  ?? '',                              // C: descripcion
                $item->estado_vigencia  ?? '',                    // D: 
                $item->tipo_orden  ?? '',                               // E: 
                $item->estado_orden  ?? '',                             // F:
                $fecha,                          // g: 
            ];
        });
    }
    /**
     * Títulos de las columnas (fila 1).
     */
    public function headings(): array
    {
        return [
            'Proveedor',
            'Descripción',
            'Estado de vigencia',
            'Tipo de orden',
            'Estado de orden',
            'Fecha de creación',
        ];
    }

    /**
     * Anchos para A–H.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 30,
            'C' => 18,
            'D' => 20,
            'E' => 20,
            'F' => 20,
        ];
    }

    /**
     * Evento AfterSheet: negrita + color solo en fila 1, A1:H1.
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Negrita en toda la fila de encabezados
                $sheet->getStyle('A1:H1')->getFont()->setBold(true);

                // Mapa de colores por celda de encabezado
                $map = [
                    'A' => 'FFB3D9FF',
                    'B' => 'FFC2E699',
                    'C' => 'FFFFFF99',
                    'D' => 'FFFFCCCC',
                    'E' => 'FFD9B3FF',
                    'F' => 'FFCCE5FF',
                ];

                foreach ($map as $col => $color) {
                    $sheet->getStyle("{$col}1")
                        ->getFill()
                        ->setFillType(Fill::FILL_SOLID)
                        ->getStartColor()
                        ->setARGB($color);
                }
            },
        ];
    }
}
