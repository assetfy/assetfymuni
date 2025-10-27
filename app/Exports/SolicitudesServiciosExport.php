<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SolicitudesServiciosExport implements FromCollection, WithHeadings
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
        // Cargar las relaciones necesarias para evitar accesos nulos
        $this->selectedData->load([
            'servicios',
            'activos',
            'empresasSolicitantes',
            'users',
        ]);

        // Transformar los datos seleccionados
        $datosFormateados = $this->selectedData->map(function ($solicitud) {
            return [
                'ID Servicio' => $solicitud->id_servicio,
                'Nombre del Servicio' => $solicitud->servicios->nombre ?? 'Sin Servicio',
                'ID Activo' => $solicitud->id_activo,
                'Nombre del Activo' => $solicitud->activos->nombre ?? 'Sin Activo',
                'Empresa Solicitante' => $solicitud->empresasSolicitantes->razon_social ?? 'Sin Empresa',
                'Solicitante' => $solicitud->users->name ?? 'Sin Solicitante',
                'Fecha y Hora' => $solicitud->fechaHora ?? 'Sin Fecha',
                'Fecha Modificada' => $solicitud->fecha_modificada ?? 'Sin Fecha Modificada',
                'Descripción' => $solicitud->descripcion ?? 'Sin Descripción',
                'Estado' => $solicitud->estado ?? 'Sin Estado',
                'Presupuesto' => $solicitud->presupuesto ?? 'Sin Presupuesto',
                'Estado del Presupuesto' => $solicitud->estado_presupuesto ?? 'Sin Estado de Presupuesto',
                'Precio' => $solicitud->precio ?? 'Sin Precio',
                'Motivo de Cancelación' => $solicitud->motivo_cancelacion ?? 'Sin Motivo',
            ];
        });

        return new Collection($datosFormateados);
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        // Define los nombres de las columnas
        return [
            'ID Servicio',
            'Nombre del Servicio',
            'ID Activo',
            'Nombre del Activo',
            'Empresa Solicitante',
            'Solicitante',
            'Fecha y Hora',
            'Fecha Modificada',
            'Descripción',
            'Estado',
            'Presupuesto',
            'Estado del Presupuesto',
            'Precio',
            'Motivo de Cancelación',
        ];
    }
}
