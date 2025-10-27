<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class UbicacionesExport implements FromCollection, WithHeadings
{
    protected $selectedData;

    public function __construct($selectedData)
    {
        $this->selectedData = $selectedData;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Transformar los datos seleccionados si es necesario
        $datosFormateados = $this->selectedData->map(function ($ubicacion) {
            return [
                'Nombre' => $ubicacion->nombre,
                'Pais' => $ubicacion->pais,
                'Provincia' => $ubicacion->provincia,
                'Ciudad' => $ubicacion->ciudad,
                'Codigo Postal' => $ubicacion->codigo_postal,
                'Calle' => $ubicacion->calle,
                'Altura' => $ubicacion->altura,
                'Piso' => $ubicacion->piso,
                'Propietario' => $ubicacion->propiedad,
                'tipo de Ubicacion' => $ubicacion->tipo,
                'cuil' => $ubicacion->cuil,
                'cuit' => $ubicacion->cuit,
            ];
        });

        // Retornar los datos formateados como una colección
        return new Collection($datosFormateados);
    }
    /**
     * @return array
     */
    public function headings(): array
    {
        // Define los encabezados de las columnas
        return [
            'Nombre',
            'Pais',
            'Provincia',
            'Ciudad',
            'Codigo Postal',
            'Calle',
            'Altura',
            'Piso',
            'Propietario',
            'Tipo De Ubicacion',
            'cuil',
            'cuit',
        ];
    }
}
