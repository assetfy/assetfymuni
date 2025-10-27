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

class HistorialServiciosExport implements FromCollection, WithHeadings, WithColumnWidths, WithEvents
{
    protected Collection $data;

    public function __construct(Collection $selectedData)
    {
        // Eager-load relaciones para evitar N+1
        $this->data = $selectedData
            ->load(['empresasPrestadora', 'activos', 'servicios', 'empresasSolicitantes']);
    }

    /**
     * Devuelve una colección de arrays, uno por fila,
     * en el mismo orden que los headings.
     */
    public function collection()
    {
        $signo = '$';
        return $this->data->map(function ($item)   use ($signo) {
            // Fecha como Y-m-d H:i
            $fecha = $item->fechaHora instanceof Carbon
                ? $item->fechaHora->format('Y-m-d H:i')
                : Carbon::parse($item->fechaHora)->format('Y-m-d H:i');

            return [
                $fecha,                                        // A: Fecha y Hora
                $item->empresasPrestadora->razon_social ?? '', // B: Proveedor
                $item->activos->nombre,                              // C: Id del Bien
                $item->empresasSolicitantes->razon_social ?? '',                    // D: Solicitante (o relación si prefieres nombre)
                $item->garantia,                               // E: Garantía
                $item->descripcion,                            // F: Servicio / Descripción
                $item->estado_presupuesto ?? $item->estado,    // G: Estado
                $signo . $item->precio,                          // H: Precio
            ];
        });
    }

    /**
     * Títulos de las columnas (fila 1).
     */
    public function headings(): array
    {
        return [
            'Fecha y Hora',
            'Proveedor',
            'Nombre del Bien',
            'Solicitante',
            'Garantía',
            'Servicio',
            'Estado',
            'Precio',
        ];
    }

    /**
     * Anchos para A–H.
     */
    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 30,
            'C' => 18,
            'D' => 25,
            'E' => 10,
            'F' => 40,
            'G' => 40,
            'H' => 15,
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
                    'G' => 'FFCCFFCC',
                    'H' => 'FFE5CCFF',
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
