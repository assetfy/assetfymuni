<?php

namespace App\Livewire\Exports;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;

class CotizacionesTemplateExport implements FromCollection, WithMapping, WithHeadings, WithTitle, WithColumnWidths, WithEvents
{
    public function __construct(private string $cuit) {}

    // 1) Encabezados VISIBLES (los que espera tu import)
    private array $headings = [
        'Estado Presupuesto',
        'Precio',
        'Prestadora',
        'Titulo Solicitud',
        'Tipo Solicitud',
        'Nombre del Bien',
        'Categoria del Bien',
        'Subcategoria del Bien',
        'Tipo del Bien',
        'Comentarios',
        'Fecha Hora'
    ];

    // 2) Claves que devuelve el SP (tal cual vienen de DB)
    private array $dbCols = [
        'Estado Presupuesto',
        'Precio',
        'Prestadora',
        'Titulo Solicitud',
        'Tipo Solicitud',
        'Nombre del Bien',
        'Categoria del Bien',
        'Subcategoria del Bien',
        'Tipo del Bien',
        'Comentarios',
        'Fecha Hora'
    ];

    public function collection()
    {
        $rows = DB::select('EXEC dbo.sp_cotizaciones_exportar ?', [$this->cuit]);
        return collect($rows)->map(fn($r) => (array) $r);
    }

    public function map($row): array
    {
        return array_map(fn($dbKey) => Arr::get($row, $dbKey), $this->dbCols);
    }

    public function headings(): array
    {
        return $this->headings;
    }
    public function title(): string
    {
        return 'Worksheet';
    }

    // Anchos de columnas
    public function columnWidths(): array
    {
        return [
            'A' => 35,
            'B' => 28,
            'C' => 18,
            'D' => 30,
            'E' => 22,
            'F' => 16,
            'G' => 18,
            'H' => 22,
            'I' => 20,
            'J' => 32,
            'K' => 22,
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Congelar fila de encabezados y autofiltro
                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:K1');

                // Estilo encabezado
                $header = 'A1:K1';
                $sheet->getRowDimension(1)->setRowHeight(22);
                $sheet->getStyle($header)->getFont()->setBold(true);
                $sheet->getStyle($header)->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER);
                $sheet->getStyle($header)->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFEFEFEF'); // gris suave
                $sheet->getStyle($header)->getBorders()->getBottom()
                    ->setBorderStyle(Border::BORDER_THIN);
            },
        ];
    }
}
