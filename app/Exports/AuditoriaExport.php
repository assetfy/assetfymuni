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

class AuditoriaExport implements FromCollection, WithHeadings, WithColumnWidths, WithEvents
{
    protected Collection $data;

    public function __construct(Collection $selectedData)
    {
        $this->data = $selectedData;
    }

    /**
     * Devuelve una colección de arrays, uno por fila,
     * en el mismo orden que los headings.
     */
    public function collection()
    {
        return $this->data->map(function ($item) {
            return [
                $item->clase_tabla ?? '', // B: 
                $item->pk_nombre  ?? '',                              // C: 
                $item->id_tabla  ?? '',                    // D: 
                $item->campo  ?? '',                               // E: 
                $item->valor_previo  ?? '',
                $item->valor_actual  ?? '',
                $item->evento    ?? '',
                $item->fechahora_cambio  ?? '', // F:                     //
                $item->autor  ?? '',                               // G: 
                $item->autor_empresa  ?? '',                    // H: 
                $item->ip  ?? '',                               // I:   
            ];
        });
    }
    /**
     * Títulos de las columnas (fila 1).
     */
    public function headings(): array
    {
        return [
            'Clase tabla',
            'Nombre de la Pk',
            'id de la tabla',
            'campo',
            'valor previo',
            'valor actual',
            'evento',
            'Fecha y hora del cambio',
            'Autor',
            'Empresa del autor',
            'IP',
        ];
    }

    /*
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
            'G' => 40,
            'H' => 30,
            'I' => 20,
            'J' => 20,
            'K' => 20,
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
                    'G' => 'FFCCEF3F',
                    'H' => 'FFB3D9FF',
                    'I' => 'FFC2E699',
                    'J' => 'FFFFFF99',
                    'K' => 'FFFFCCCC',
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
